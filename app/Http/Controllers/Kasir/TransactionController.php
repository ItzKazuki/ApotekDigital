<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Drug;
use App\Models\Member;
use App\Models\Transaction;
use App\Services\Payments\PaymentGatewayInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Logic to retrieve transactions for the kasir dashboard
        // This could involve fetching data from a model and passing it to a view
        $query = Transaction::with(['transactionDetails.drug', 'member'])->where('kasir_id', Auth::user()->id)->orderBy('transaction_date', 'desc');

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        // Filter by member
        if ($request->filled('member')) {
            $query->whereHas('member', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->member . '%');
            });
        }

        $transactions = $query->get();

        return view('kasir.transaction.index', compact('transactions')); // Adjust the view path as necessary
    }

    public function store(Request $request, PaymentGatewayInterface $paymentService)
    {
        DB::beginTransaction();
        $member = null;
        $transaction = null;

        $validPayment = config('payment.list');

        if (!in_array($request->metode_pembayaran, $validPayment)) {
            return response()->json([
                'success' => false,
                'message' => 'Metode pembayaran tersebut tidak tersedia saat ini.'
            ]);
        }

        try {
            $itemCart = \Cart::getContent();

            $transaction = $request->user()->transactions()->create();

            if ($request->phone) {
                $member = Member::where('phone', $request->phone)->first();
            }

            foreach ($itemCart as $item) {
                $drug = Drug::find($item->id);

                // Cek apakah quantity orderItem lebih besar dari stock produk
                if ($drug->stock < $item->quantity) {
                    // Rollback transaksi jika quantity lebih besar dari stock
                    DB::rollBack();
                    return response()->json([
                        'message' => "Transaksi gagal, obat {$drug->name} stok nya kurang"
                    ], 400);
                }

                $transaction->transactionDetails()->create([
                    'drug_id' => $item->id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'subtotal' => $item->price * $item->quantity
                ]);

                // Kurangi stock produk
                $drug->stock -= $item->quantity;
                $drug->save();
            }

            $transactionData = [
                'cash' => $request->cash ?? 0,
                'status' => $request->metode_pembayaran == Transaction::PAYMENT_METHOD_CASH ? Transaction::STATUS_PAID : Transaction::STATUS_PENDING,
                'payment_method' => $request->metode_pembayaran,
                'total' => config('app.tax.enabled') ? $transaction->transactionDetails->sum('subtotal') + config('app.tax.value') : $transaction->transactionDetails->sum('subtotal'),
            ];

            if (isset($member)) {
                $transactionData['member_id'] = $member->id;
                $rewards = $member->calculateMemberRewards($transactionData['total']);
                $transactionData['reward_point'] = $rewards['point'];
                $member->point += $rewards['point'];
                $member->expires_at = $member->expires_at
                    ? $member->expires_at->addDays($rewards['add_days'])
                    : now()->addDays($rewards['add_days']);

                $member->save();

                $promo = $member->checkPromoMember();

                if ($request->use_member_promo && $request->use_member_promo == true && $promo) {
                    $subtotal = $transaction->transactionDetails()->sum('subtotal');

                    $discountValue = $subtotal * ($promo['discount'] / 100);
                    $remainingPrice = $transactionData['total'] - $discountValue;

                    if ($request->metode_pembayaran == 'cash' && $request->cash < $remainingPrice) {
                        throw new Exception("Transaction failed, insufficient cash to cover the remaining price", 400);
                    }

                    $transactionData['total'] = $remainingPrice;
                    $transactionData['change'] = $request->cash - $remainingPrice;
                    $transactionData['point_usage'] = $promo['used_point'];
                    $member->point -= $promo['used_point'];
                    $member->save();
                }
            }

            if ($request->metode_pembayaran == "qris") {
                // use data to generate qris at midtrans

                $midtrans = $paymentService->createTransaction([
                    'transaction_id' => $transaction->id,
                    'total_price' => $transactionData['total'],
                ]);

                $transactionData['payment_url'] = $midtrans['actions_url'];
                $transactionData['payment_expired'] = $midtrans['expiry_time'];
                $transactionData['cash'] = $midtrans['gross_amount'];
            }

            if ($request->cash && $request->metode_pembayaran != "qris") {
                $transactionData['change'] = $request->cash - $transactionData['total'];
            }

            $transaction->update($transactionData);

            // Hapus item cart
            \Cart::clear();

            // remove session cart_expired_at if exists
            if (session()->has('cart_expired_at')) {
                session()->forget('cart_expired_at');
            }

            // Commit DB transaction
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil! Terimakasih telah membeli',
                'redirect' => route('kasir.transaction.show', $transaction->id)
            ]);
        } catch (Exception $e) {
            // Rollback DB transaction jika terjadi error
            DB::rollBack();

            return response()->json([
                'error' => 'Transaksi gagal, coba ulang kembali nanti.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function checkStatus(Transaction $transaction, PaymentGatewayInterface $paymentService)
    {
        try {
            $status = $paymentService->checkPaymentStatus($transaction->id);

            DB::beginTransaction();

            if ($status['transaction_status'] == 'settlement') {
                $transaction->update([
                    'status' => Transaction::STATUS_PAID,
                    'cash' => $status['gross_amount'],
                ]);
            } elseif ($status['transaction_status'] == 'pending') {
                $transaction->update([
                    'status' => Transaction::STATUS_PENDING,
                ]);
            } else {
                // Kembalikan stok produk
                foreach ($transaction->transactionDetails as $detail) {
                    $drug = $detail->drug;
                    $drug->stock += $detail->quantity;
                    $drug->save();
                }

                $transaction->update([
                    'status' => Transaction::STATUS_CANCELED,
                ]);
            }

            DB::commit();

            return response()->json($status);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to check payment status',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Transaction $transaction)
    {
        // Logic to show a specific transaction
        // This could involve fetching the transaction details and passing them to a view
        if ($transaction->kasir_id !== Auth::user()->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('kasir.transaction.show', compact('transaction')); // Adjust the view path as necessary
    }
}
