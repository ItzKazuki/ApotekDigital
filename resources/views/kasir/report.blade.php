@extends('layouts.kasir')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Laporan Harian Kasir</h1>
            <div class="flex items-center space-x-4">
                <div class="bg-white shadow rounded-lg px-4 py-2">
                    <span id="current-date" class="text-gray-600"></span>
                </div>
                <a href="{{ route('kasir.report.export.excel') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <span class="hidden sm:inline">Export Laporan (EXCEL)</span>
                </a>
            </div>
        </div>

        <!-- Statistik Card -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white shadow rounded-lg p-6 border-l-4 border-green-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Uang Masuk Hari Ini</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">Rp {{ number_format($cashToday, 2, ',', '.') }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>
                @if ($percentageCashChange > 0)
                    <p class="text-green-600 text-sm mt-2">Naik {{ abs($percentageCashChange) }}% dari kemarin</p>
                @elseif($percentageCashChange < 0)
                    <p class="text-red-600 text-sm mt-2">Turun {{ abs($percentageCashChange) }}% dari kemarin</p>
                @else
                    <p class="text-gray-500 text-sm mt-2">Tidak ada perubahan dari kemarin</p>
                @endif

            </div>

            <div class="bg-white shadow rounded-lg p-6 border-l-4 border-red-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Uang Keluar Hari Ini</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">Rp {{ number_format($changeToday, 2, ',', '.') }}
                        </p>
                    </div>
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-6 border-l-4 border-blue-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Penjualan Produk</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalQuantityToday }} item</p>
                    </div>
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-6 border-l-4 border-yellow-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Keuntungan Hari Ini</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">Rp
                            {{ number_format($totalProfitToday, 2, ',', '.') }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                                @if ($percentageProfitChange > 0)
                    <p class="text-green-600 text-sm mt-2">Naik {{ abs($percentageProfitChange) }}% dari kemarin</p>
                @elseif($percentageProfitChange < 0)
                    <p class="text-red-600 text-sm mt-2">Turun {{ abs($percentageProfitChange) }}% dari kemarin</p>
                @else
                    <p class="text-gray-500 text-sm mt-2">Tidak ada perubahan dari kemarin</p>
                @endif
            </div>
        </div>

        <!-- Grafik Statistik Penjualan -->
        <div class="bg-white shadow rounded-lg p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold text-gray-800">Statistik Penjualan 7 Hari Terakhir</h2>
                <div
                    class="bg-gray-100 border border-gray-300 text-gray-700 py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option>7 Hari Terakhir</option>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Laporan Penjualan -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Detail Transaksi Hari Ini</h2>
            </div>
            <div class="overflow-x-auto scrollbar-hide">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID
                                Transaksi</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jumlah Item</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Metode Bayar</th>
                            <th scope="col"
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($transactions as $transaction)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                                    {{ $transaction->invoice_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $transaction->transaction_date }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $transaction->transactionDetails->sum('quantity') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800">
                                    Rp{{ number_format($transaction->total, 2, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ $transaction->payment_method }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a class="text-blue-600 hover:text-blue-900"
                                        href="{{ route('kasir.transaction.show', ['transaction' => $transaction->id]) }}">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada transaksi
                                    hari ini</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <nav class="flex items-center justify-between">
                    <!-- Info jumlah data -->
                    <div class="hidden sm:block">
                        <p class="text-sm text-gray-700">
                            Menampilkan
                            <span class="font-medium">{{ $transactions->firstItem() }}</span>
                            sampai
                            <span class="font-medium">{{ $transactions->lastItem() }}</span>
                            dari
                            <span class="font-medium">{{ $transactions->total() }}</span>
                            transaksi
                        </p>
                    </div>

                    <!-- Tombol pagination -->
                    <div class="flex-1 flex justify-between sm:justify-end space-x-3">
                        <!-- Tombol Sebelumnya -->
                        <a href="{{ $transactions->previousPageUrl() }}"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md
                      text-gray-700 bg-white hover:bg-gray-50 {{ $transactions->onFirstPage() ? 'opacity-50 pointer-events-none' : '' }}">
                            Sebelumnya
                        </a>

                        <!-- Tombol Berikutnya -->
                        <a href="{{ $transactions->nextPageUrl() }}"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md
                      text-gray-700 bg-white hover:bg-gray-50 {{ !$transactions->hasMorePages() ? 'opacity-50 pointer-events-none' : '' }}">
                            Berikutnya
                        </a>
                    </div>
                </nav>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const transactionsPerDay = @json($transactionsPerDay);
        const revenuePerDay = @json($revenuePerDay);

        // Set current date
        document.getElementById('current-date').textContent = new Date().toLocaleDateString('id-ID', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        const ctx = document.getElementById('salesChart');
        const salesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
                datasets: [{
                    label: 'Jumlah Transaksi',
                    data: transactionsPerDay,
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }, {
                    label: 'Total Pendapatan (juta)',
                    data: revenuePerDay,
                    backgroundColor: 'rgba(16, 185, 129, 0.5)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah Transaksi'
                        }
                    },
                    y1: {
                        position: 'right',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Pendapatan (juta Rp)'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });
    </script>
@endpush
