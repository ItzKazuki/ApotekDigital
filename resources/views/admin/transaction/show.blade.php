@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">
    <!-- Judul Halaman -->
    <h1 class="text-3xl font-bold text-gray-900 mb-4">
        Detail Transaksi
    </h1>

    <!-- Info Transaksi -->
    <div class="bg-white shadow rounded-lg p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Invoice</p>
                <p class="font-semibold text-lg">{{ $transaction->invoice_number ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Tanggal Transaksi</p>
                <p class="font-semibold text-lg">
                    {{ $transaction->transaction_date->format('d M Y, H:i') }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Kasir</p>
                <p class="font-semibold text-lg">{{ $transaction->kasir->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Member</p>
                <p class="font-semibold text-lg">
                    {{ $transaction->member->name ?? 'Non Member' }}
                    @if ($transaction->member)
                        <span class="text-xs text-gray-500">
                            ({{ $transaction->member->phone }})
                        </span>
                    @endif
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Metode Pembayaran</p>
                <p class="font-semibold text-lg">{{ ucfirst($transaction->payment_method) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Status</p>
                <span class="px-3 py-1 text-sm rounded-full
                    {{ $transaction->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ ucfirst($transaction->status) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Detail Produk -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-bold mb-4">Daftar Produk</h2>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-left text-sm">
                        <th class="p-3 border-b">Nama Produk</th>
                        <th class="p-3 border-b text-right">Harga</th>
                        <th class="p-3 border-b text-center">Qty</th>
                        <th class="p-3 border-b text-right">Subtotal</th>
                        <th class="p-3 border-b text-right">Modal</th>
                        <th class="p-3 border-b text-right">Profit</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @foreach ($transaction->transactionDetails as $detail)
                        @php
                            $profit = ($detail->price - $detail->drug->purchase_price) * $detail->quantity;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 border-b">{{ $detail->drug->name }}</td>
                            <td class="p-3 border-b text-right">
                                Rp{{ number_format($detail->price, 0, ',', '.') }}
                            </td>
                            <td class="p-3 border-b text-center">{{ $detail->quantity }}</td>
                            <td class="p-3 border-b text-right">
                                Rp{{ number_format($detail->subtotal, 0, ',', '.') }}
                            </td>
                            <td class="p-3 border-b text-right">
                                Rp{{ number_format($detail->drug->purchase_price, 0, ',', '.') }}
                            </td>
                            <td class="p-3 border-b text-right">
                                Rp{{ number_format($profit, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Ringkasan -->
    <div class="bg-white shadow rounded-lg p-6 space-y-2">
        <div class="flex justify-between text-lg font-semibold">
            <span>Total</span>
            <span>Rp{{ number_format($transaction->total, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between">
            <span>Bayar</span>
            <span>Rp{{ number_format($transaction->cash, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between">
            <span>Kembalian</span>
            <span>Rp{{ number_format($transaction->change, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between text-green-700 font-bold">
            <span>Total Profit</span>
            <span>
                Rp{{ number_format($transaction->transactionDetails->sum(fn($d) => ($d->price - $d->drug->purchase_price) * $d->quantity), 0, ',', '.') }}
            </span>
        </div>
    </div>

    <!-- Tombol Kembali -->
    <div>
        <a href="{{ route('admin.transaction.index') }}"
           class="inline-block bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
            ← Kembali
        </a>
    </div>
</div>
@endsection
