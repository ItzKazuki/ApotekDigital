<?php

namespace App\Http\Controllers\Kasir;

use App\Models\Drug;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class CartController extends Controller
{
    public function showCart(Request $request)
    {
        $itemsCart = \Cart::getContent();
        $subTotalWithoutConditions = \Cart::getSubTotalWithoutConditions();

        return response()->json($itemsCart->count() > 0 ? [
            'cartItems' => $itemsCart,
            'subtotal' => number_format($subTotalWithoutConditions, 0, ',', '.'),
            'cart_expired_at' => session()->get('cart_expired_at'),
        ] : [
            'success' => true,
            'message' => 'Keranjang belanja kosong'
        ]);
    }

    public function clearTimeout()
    {
        if (session()->has('cart_expired_at')) {
            session()->forget('cart_expired_at');
        }

        return response()->json(
            [
                'success' => true,
                'message' => 'Waktu keranjang belanja berhasil direset'
            ]
        );
    }

    public function storeCart(Request $request)
    {
        $drug = Drug::find($request->drug_id);

        if (!session()->has('cart_expired_at') && config('app.cart.cart_timer_enabled')) {
            $minutes = (int) config('app.cart.cart_expired_at');
            session()->put('cart_expired_at', now()->addMinutes($minutes)); // misalnya 30 menit
        }

        // check if drug exists
        // if not return 404
        // if stock is 0 return 400
        if (!$drug) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Produk tidak ditemukan'
                ],
                404
            );
        }

        if ($drug->stock <= 0) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Stock produk sudah habis'
                ],
                400
            );
        }

        // ih this logic you can buy many drug stock without checking stock, check if stock is enough
        if (\Cart::get($drug->id)) {
            $cartItem = \Cart::get($drug->id);
            if ($cartItem->quantity >= $drug->stock) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Stock produk tidak cukup untuk ditambahkan ke keranjang'
                    ],
                    400
                );
            }
        }

        $expiredDate = Carbon::parse($drug->expired_at);
        $today = Carbon::today();
        $diffInDays = $today->diffInDays($expiredDate, false);

        if ($diffInDays < 0) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Produk sudah kadaluarsa'
                ],
                400
            );
        }

        \Cart::add([
            'id' => $drug->id,
            'name' => $drug->name,
            'price' => $drug->price,
            'quantity' => 1,
            'attributes' => [
                'packaging_types' => $drug->packaging_types,
                'image' => $drug->image_url,
                'category' => $drug->category->name
            ]
        ]);

        return response()->json(
            [
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke keranjang'
            ]
        );
    }

    public function storeCartByBarcode(Request $request)
    {
        $drug = Drug::where('barcode', intval($request->barcode))->first();

        if (!session()->has('cart_expired_at') && config('app.cart.cart_timer_enabled')) {
            $minutes = (int) config('app.cart.cart_expired_at');
            session()->put('cart_expired_at', now()->addMinutes($minutes)); // misalnya 30 menit
        }

        if (!$drug) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Produk tidak ditemukan'
                ],
                404
            );
        }

        if ($drug->stock <= 0) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Produk stock sudah habis'
                ],
                400
            );
        }

        // ih this logic you can buy many product stock without checking stock, check if stock is enough
        if (\Cart::get($drug->id)) {
            $cartItem = \Cart::get($drug->id);
            if ($cartItem->quantity >= $drug->stock) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Produk stock tidak cukup untuk ditambahkan ke keranjang'
                    ],
                    400
                );
            }
        }

        $expiredDate = Carbon::parse($drug->expired_at);
        $today = Carbon::today();
        $diffInDays = $today->diffInDays($expiredDate, false);

        if ($diffInDays < 0) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Produk sudah kadaluarsa'
                ],
                400
            );
        }

        \Cart::add([
            'id' => $drug->id,
            'name' => $drug->name,
            'price' => $drug->price,
            'quantity' => 1,
            'attributes' => [
                'packaging_types' => $drug->packaging_types,
                'image' => $drug->image_url,
                'category' => $drug->category->name
            ]
        ]);

        return response()->json(
            [
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke keranjang'
            ]
        );
    }

    public function removeItemCart(Request $request)
    {
        \Cart::remove($request->drug_id);

        // cek apakah item di cart kosong
        if (\Cart::isEmpty()) {
            if (session()->has('cart_expired_at')) {
                session()->forget('cart_expired_at');
            }
        }

        return response()->json(
            [
                'success' => true,
                'message' => 'Produk berhasil dihapus dari keranjang'
            ]
        );
    }

    public function incrementItemCart(Request $request)
    {
        $cartItem = \Cart::get($request->drug_id);
        $drug = Drug::find($cartItem->id);

        if (!$drug) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Produk tidak ditemukan'
                ],
                404
            );
        }
        if ($cartItem->quantity >= $drug->stock) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Stock produk tidak cukup untuk ditambahkan ke keranjang'
                ],
                400
            );
        }
        if ($drug->stock <= 0) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Stock produk sudah habis'
                ],
                400
            );
        }

        \Cart::update(
            $request->drug_id,
            [
                'quantity' => +1,
            ]
        );

        return response()->json(
            [
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke keranjang'
            ]
        );
    }

    public function decrementItemCart(Request $request)
    {
        $cartItem = \Cart::get($request->drug_id);
        $drug = Drug::find($cartItem->id);

        if (!$drug) {
            return response()->json(
                [
                    'message' => 'Produk tidak ditemukan'
                ],
                404
            );
        }

        if ($cartItem->quantity <= 1) {
            \Cart::remove($request->drug_id);

            if (\Cart::isEmpty()) {
                if (session()->has('cart_expired_at')) {
                    session()->forget('cart_expired_at');
                }
            }

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Produk berhasil dihapus dari keranjang'
                ]
            );
        }

        \Cart::update(
            $request->drug_id,
            [
                'quantity' => -1, //
            ]
        );

        return response()->json(
            [
                'success' => true,
                'message' => 'Produk berhasil dikurangi dari keranjang'
            ]
        );
    }

    public function clearCart()
    {
        \Cart::clear();

        if (session()->has('cart_expired_at')) {
            session()->forget('cart_expired_at');
        }

        return response()->json(
            [
                'success' => true,
                'message' => 'Keranjang belanja berhasil dikosongkan'
            ]
        );
    }
}
