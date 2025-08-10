@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-semibold mb-6">Laporan Penjualan</h1>

        {{-- Filter Tanggal --}}
        <form method="GET" class="mb-6 flex space-x-4">
            <input type="date" name="start_date" value="{{ $startDate }}"
                class="border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <input type="date" name="end_date" value="{{ $endDate }}"
                class="border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">Filter</button>
            <a href="{{ route('admin.report.download.pdf', ['start_at' => $startDate, 'end_at' => $endDate]) }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg shadow-md transition">
                <i class="fas fa-file-pdf"></i>
                Download PDF
            </a>


        </form>

        {{-- Ringkasan --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white p-6 rounded-lg shadow-lg flex flex-col justify-between">
                <p class="text-sm text-gray-500">Total Omzet</p>
                <p class="text-2xl font-bold">Rp {{ number_format($totalSales, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg flex flex-col justify-between">
                <p class="text-sm text-gray-500">Total Keuntungan</p>
                <p class="text-2xl font-bold">Rp {{ number_format($totalProfit, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg flex flex-col justify-between">
                <p class="text-sm text-gray-500">Jumlah Transaksi</p>
                <p class="text-2xl font-bold">{{ $totalTransactions }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg flex flex-col justify-between">
                <p class="text-sm text-gray-500">Produk Terjual</p>
                <p class="text-2xl font-bold">{{ $totalProductsSold }}</p>
            </div>
        </div>

        {{-- Tabel Transaksi --}}
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-300 bg-white rounded-lg shadow-lg">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border-b p-4 text-left text-sm font-medium text-gray-700">ID</th>
                        <th class="border-b p-4 text-left text-sm font-medium text-gray-700">Tanggal</th>
                        <th class="border-b p-4 text-left text-sm font-medium text-gray-700">Kasir</th>
                        <th class="border-b p-4 text-left text-sm font-medium text-gray-700">Total</th>
                        <th class="border-b p-4 text-left text-sm font-medium text-gray-700">Uang</th>
                        <th class="border-b p-4 text-left text-sm font-medium text-gray-700">Kembalian</th>
                        <th class="border-b p-4 text-left text-sm font-medium text-gray-700">Metode</th>
                        <th class="border-b p-4 text-left text-sm font-medium text-gray-700">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $trx)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="border-b p-4 text-sm text-gray-600">{{ $trx->invoice_number }}</td>
                            <td class="border-b p-4 text-sm text-gray-600">{{ $trx->transaction_date }}</td>
                            <td class="border-b p-4 text-sm text-gray-600">{{ $trx->kasir->name }}</td>
                            <td class="border-b p-4 text-sm text-gray-600">Rp {{ number_format($trx->total, 0, ',', '.') }}
                            </td>
                            <td class="border-b p-4 text-sm text-gray-600">Rp {{ number_format($trx->cash, 0, ',', '.') }}
                            </td>
                            <td class="border-b p-4 text-sm text-gray-600">Rp
                                {{ number_format($trx->change, 0, ',', '.') }}</td>
                            <td class="border-b p-4 text-sm text-gray-600">{{ ucfirst($trx->payment_method) }}</td>
                            <td class="border-b p-4 text-sm text-gray-600">{{ ucfirst($trx->status) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="border-b p-4 text-sm text-gray-600">
                                Tidak ada transaksi yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
