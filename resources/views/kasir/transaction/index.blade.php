@extends('layouts.kasir')

@section('content')
    <h1 class="text-3xl font-extrabold mb-8 text-gray-900 text-shadow">
        Riwayat Transaksi
    </h1>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('kasir.transaction') }}"
        class="mb-6 bg-white p-6 rounded-lg shadow flex flex-wrap gap-6">
        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700">Mulai Tanggal</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 p-2">
        </div>
        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700">Sampai Tanggal</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 p-2">
        </div>
        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700">Nama Member</label>
            <input type="text" name="member" placeholder="Cari member" value="{{ request('member') }}"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 p-2">
        </div>
        <div class="flex items-end mt-4">
            <button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700">
                Filter
            </button>
            <a href="{{ route('kasir.transaction') }}"
                class="ml-2 bg-gray-300 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-400">
                Reset
            </a>
        </div>
    </form>


    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <!-- History Card 1 -->
        @foreach ($transactions as $transaction)
            <article
                class="elegant-bg rounded-xl p-6 space-y-4 border border-gray-300 hover:shadow-lg transition cursor-pointer"
                onclick="window.location='{{ route('kasir.transaction.show', $transaction->id) }}'">
                <header class="flex justify-between items-center">
                    <h2 class="text-xl font-bold">{{ $transaction->invoice_number }}</h2>
                    <time datetime="2024-04-20T14:30"
                        class="text-sm text-gray-600 font-mono">{{ $transaction->transaction_date->format('d M Y, H:i') }}</time>
                </header>
                @php
                    $details = $transaction->transactionDetails;
                    $shownDetails = $details->take(2);
                    $remainingCount = $details->count() - $shownDetails->count();
                @endphp

                <ul
                    class="space-y-2 max-h-40 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100 rounded-md">
                    @foreach ($shownDetails as $transactionDetail)
                        <li class="flex justify-between items-center">
                            <span class="text-gray-800">{{ $transactionDetail->drug->name }}</span>
                            <span class="text-gray-600">Rp{{ number_format($transactionDetail->subtotal, 0, ',', '.') }}
                                (x{{ $transactionDetail->quantity }})
                            </span>
                        </li>
                    @endforeach

                    @if ($remainingCount > 0)
                        <li class="text-sm text-gray-500 italic">+{{ $remainingCount }} produk lainnya</li>
                    @endif
                </ul>

                <div class="border-t border-gray-300 pt-4 flex justify-between font-extrabold text-lg">
                    <span>Total</span>
                    <span>Rp{{ number_format($transaction->total, 2, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-700">
                    <span>Member:</span>
                    <span>{{ $transaction->member->name ?? 'Non Member' }} @if ($transaction->member)
                            <span class="text-xs text-gray-500">({{ $transaction->member->phone }})</span>
                        @endif
                    </span>
                </div>
                <div class="flex justify-between text-sm text-gray-700">
                    <span>Kembalian:</span>
                    <span>Rp{{ number_format($transaction->change, 2, ',', '.') }}</span>
                </div>
            </article>
        @endforeach
    </section>
@endsection
