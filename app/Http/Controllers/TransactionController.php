<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\FonnteService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected $fonnteService;

    public function __construct(FonnteService $fonnteService)
    {
        $this->fonnteService = $fonnteService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Transaction::with(['member', 'kasir']);

        if ($request->filled('search')) {
            $query->findByInvoice($request->search);
        }

        if ($request->filled('payment_status')) {
            $query->where('status', $request->payment_status);
        }

        $transactions = $query->paginate(10);

        return view('admin.transaction.index', compact('transactions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        return view('admin.transaction.show', compact('transaction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }

    public function streamStruk(string $invoice)
    {
        $transaction = Transaction::findByInvoice($invoice)->first();

        if (!$transaction) {
            return abort(404);
        }

        return view('layouts.struk', compact('transaction'));
    }

    public function sendWhatsappMessage(Request $request)
    {
        $request->validate([
            'phone'    => 'required|string',
            'transaction_id' => 'required|exists:transactions,id',
        ]);

        $transaction = Transaction::findOrFail($request->input('transaction_id'));

        if ($transaction->send_whatsapp_notification) {
            return response()->json([
                'success' => false,
                'message' => 'Pesan sudah pernah dikirim sebelumnya.'
            ], 400);
        }

        $target = $request->input('phone');

        // Ambil data member (jika ada)
        $memberName = $transaction->member->name ?? '-';
        // $poinBertambah = $transaction->order->point ?? 0;

        // Ambil produk yang dibeli
        $produkList = '';
        foreach ($transaction->transactionDetails as $item) {
            $produkList .= "- {$item->drug->name} x{$item->quantity}\n";
        }

        $kembalian = number_format($transaction->change, 0, ',', '.');
        $strukUrl = url()->to(route('struk.search', $transaction->invoice_number, false));

        $message = "Terima kasih telah berbelanja di " . config('app.name') . "!\n"
            . "Nama Member: {$memberName}\n"
            // . "Poin Bertambah: {$poinBertambah}\n"
            . "Produk yang dibeli:\n{$produkList}"
            . "PPN: Rp " . number_format(config('app.tax.value'), 0, ',', '.') . "\n"
            . "Total Harga: Rp " . number_format($transaction->total, 0, ',', '.') . "\n"
            . "Dibayar: Rp " . number_format($transaction->cash, 0, ',', '.') . "\n"
            . "Metode Pembayaran: {$transaction->payment_method}\n"
            . "Kembalian: Rp {$kembalian}\n"
            . "Struk dapat diakses di sini:\n{$strukUrl}";

        $response = $this->fonnteService->sendWhatsAppMessage($target, $message);

        if (!$response['status'] || (isset($response['data']['status']) && !$response['data']['status'])) {
            $errorReason = $response['data']['reason'] ?? 'Unknown error occurred';
            return response()->json(['message' => 'Error', 'error' => $errorReason], 500);
        }

        $transaction->update([
            'send_whatsapp_notification' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pesan berhasil dikirim!',
            'data' => $response['data']
        ]);
    }

    /**
     * This method only admin can execute
     */
    public function updatePaymentStatus(Request $request, Transaction $transaction, string $status)
    {
        $transaction->update([
            'status' => $status
        ]);

        return back()->with('success', 'Update pembayaran berhasil.');
    }
}
