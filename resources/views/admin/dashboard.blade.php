@extends('layouts.app')

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
    <h1 class="text-4xl font-semibold text-gray-700 mb-2">Dashboard</h1>
    <h3 class="text-xl mb-6 text-gray-600">Hallo, {{ Auth::user()->name }}! Selamat datang di portal admin.</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Penjualan Hari Ini -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200">
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-sm font-medium">Total Penjualan Hari Ini</span>
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 8v4l3 3m6-2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div class="text-2xl font-semibold text-gray-800">Rp {{ number_format($totalSalesToday, 0, ',', '.') }}</div>
        </div>
        <!-- Jumlah Transaksi -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200">
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-sm font-medium">Total Seluruh Transaksi</span>
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path d="M9 17v-6h13V7H9V1L2 10l7 9z" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div class="text-2xl font-semibold text-gray-800">{{ $totalTransactions }} Transaksi</div>
        </div>
        <!-- Member Terdaftar -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200">
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-sm font-medium">Member Terdaftar</span>
                <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path d="M17 20h5v-2a3 3 0 00-3-3h-2m-4 5v-2a3 3 0 013-3h4m-4-3a4 4 0 11-8 0 4 4 0 018 0z"
                        stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div class="text-2xl font-semibold text-gray-800">{{ $membersRegistered }} Orang</div>
        </div>
        <!-- Keuntungan Hari Ini -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200">
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-sm font-medium">Keuntungan Hari Ini</span>
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M11 17a4 4 0 110-8 4 4 0 010 8z" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div class="text-2xl font-semibold text-gray-800">Rp {{ number_format($profitToday, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="mt-4 grid grid-cols-12 gap-4 md:mt-6 md:gap-6 2xl:mt-7.5 2xl:gap-7">
        <!-- ====== Chart One Start -->
        <div
            class="col-span-12 rounded-sm border border-gray-300 bg-white px-5 pb-5 pt-7.5 shadow-default sm:px-7 xl:col-span-8">
            <div class="flex flex-wrap items-start justify-between gap-3 sm:flex-nowrap">
                <div class="flex w-full flex-wrap gap-3 sm:gap-5">
                    <div class="flex min-w-47.5">
                        <span
                            class="mr-2 mt-1 flex h-4 w-full max-w-4 items-center justify-center rounded-full border border-red-600">
                            <span class="block h-2.5 w-full max-w-2.5 rounded-full bg-red-600"></span>
                        </span>
                        <div class="w-full">
                            <p class="font-semibold  text-red-600">Total Keuntungan</p>
                        </div>
                    </div>
                </div>
                <div class="flex w-full max-w-45 justify-end">
                    <div class="inline-flex items-center rounded-md bg-gray-100 p-1.5">
                        <button id="dayBtn"
                            class="rounded bg-white px-3 py-1 text-xs font-medium text-black shadow-card hover:bg-white hover:shadow-card">
                            Day
                        </button>
                        <button id="weekBtn"
                            class="rounded px-3 py-1 text-xs font-medium text-black hover:bg-white hover:shadow-card">
                            Week
                        </button>
                        <button id="monthBtn"
                            class="rounded px-3 py-1 text-xs font-medium text-black hover:bg-white hover:shadow-card">
                            Month
                        </button>
                        <button id="yearBtn"
                            class="rounded px-3 py-1 text-xs font-medium text-black hover:bg-white hover:shadow-card">
                            Year
                        </button>
                    </div>
                </div>
            </div>
            <div>
                <canvas id="dailyProfitChart" class="-ml-5"></canvas>
            </div>
        </div>

        <!-- ====== Chart Two Start -->
        <div class="col-span-12 rounded-sm border border-gray-300 bg-white p-7 shadow-default xl:col-span-4">
            <div class="mb-4 justify-between gap-4 sm:flex">
                <div>
                    <h4 class="text-xl font-bold text-black">
                        Obat Paling Terlaris
                    </h4>
                </div>
            </div>

            <div>
                {{-- Daftar produk paling banyak terjual --}}
                <ul class="space-y-4">
                    @foreach ($topSellingProducts as $product)
                        <li class="flex items-center justify-between">
                            <div class="flex items-center">
                                <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}"
                                    class="h-20 w-20 rounded-full mr-3 object-cover">
                                <div>
                                    <p class="text-gray-800 font-semibold">{{ $product->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $product->category }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-800">{{ $product->total_sold }} Terjual</p>
                            </div>
                        </li>
                    @endforeach

                </ul>
            </div>

        </div>

        <!-- ====== Chart Two End -->
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var dailyCtx = document.getElementById("dailyProfitChart").getContext("2d");
            var dailyBtn = document.getElementById("dayBtn");
            var weeklyBtn = document.getElementById("weekBtn");
            var monthlyBtn = document.getElementById("monthBtn");
            var yearBtn = document.getElementById("yearBtn");
            var profitChart;

            function updateChart(labelsProfit, profitData, profitLabel) {
                if (profitChart) {
                    profitChart.destroy();
                }

                profitChart = new Chart(dailyCtx, {
                    type: "line",
                    data: {
                        datasets: [{
                            label: profitLabel,
                            data: profitData.map((value, index) => ({
                                x: labelsProfit[index],
                                y: value
                            })),
                            backgroundColor: "rgb(220, 38, 38)",
                            borderColor: "rgb(220, 38, 38)",
                            borderWidth: 1
                        }]
                    }
                });
            }

            dailyBtn.addEventListener("click", function() {
                monthlyBtn.classList.remove("bg-white", "shadow-card");
                weeklyBtn.classList.remove("bg-white", "shadow-card");
                yearBtn.classList.remove("bg-white", "shadow-card");
                dailyBtn.classList.add("bg-white", "shadow-card");
                updateChart(@json($dailyProfitLabels),
                    @json($dailyProfits), "Keuntungan Harian (Rp)");
            });

            weeklyBtn.addEventListener("click", function() {
                dailyBtn.classList.remove("bg-white", "shadow-card");
                monthlyBtn.classList.remove("bg-white", "shadow-card");
                yearBtn.classList.remove("bg-white", "shadow-card");
                weeklyBtn.classList.add("bg-white", "shadow-card");
                updateChart(@json($weeklyProfitLabels),
                    @json($weeklyProfits), "Keuntungan Mingguan (Rp)");
            });

            monthlyBtn.addEventListener("click", function() {
                dailyBtn.classList.remove("bg-white", "shadow-card");
                weeklyBtn.classList.remove("bg-white", "shadow-card");
                yearBtn.classList.remove("bg-white", "shadow-card");
                monthlyBtn.classList.add("bg-white", "shadow-card");
                updateChart(@json($monthlyProfitLabels), @json($monthlyProfits),
                    "Keuntungan Bulanan (Rp)");
            });

            yearBtn.addEventListener("click", function() {
                dailyBtn.classList.remove("bg-white", "shadow-card");
                weeklyBtn.classList.remove("bg-white", "shadow-card");
                monthlyBtn.classList.remove("bg-white", "shadow-card");
                yearBtn.classList.add("bg-white", "shadow-card");
                // Add logic for yearly data if available
            });

            // Initialize chart with daily data by default
            updateChart(@json($dailyProfitLabels), @json($dailyProfits),
                "Keuntungan Harian (Rp)");
        });
    </script>
@endpush
