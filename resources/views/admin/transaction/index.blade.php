@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-4xl font-semibold text-gray-700 mb-4">Daftar Transaksi</h1>

        <!-- Alert sukses/error -->
        @if (session('success'))
            <div class="mb-4 px-4 py-2 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 px-4 py-2 bg-red-100 text-red-800 rounded">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 px-4 py-2 bg-red-100 text-red-800 rounded">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Filter -->
        <form method="GET" action="{{ route('admin.transaction.index') }}"
            class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-2 md:space-y-0 mb-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari invoice transaksi..."
                class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" />

            <!-- Status Filter -->
            <select name="payment_status"
                class="w-full md:w-1/4 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">Semua Status Pembayaran</option>
                <option value="completed" {{ request('payment_status') === 'completed' ? 'selected' : '' }}>Pembayaran Lunas
                </option>
                <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pembayaran Pending
                </option>
                <option value="canceled" {{ request('payment_status') === 'canceled' ? 'selected' : '' }}>Pembayaran
                    Dibatalkan
                </option>
            </select>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Filter</button>
        </form>

        <!-- Tabel Produk -->
        <div class="overflow-x-auto bg-white rounded-xl shadow border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Invoice</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Member</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Total</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Uang</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Kembalian</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($transactions as $transaction)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $transaction->invoice_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $transaction->member->name ?? 'Non Member' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">Rp.
                                {{ number_format($transaction->total, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">Rp.
                                {{ number_format($transaction->cash, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">Rp.
                                {{ number_format($transaction->change, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $transaction->status }}</td>
                            <td class="px-6 py-4 space-x-2">
                                <a href="{{ route('admin.transaction.show', $transaction->id) }}"><button
                                        class="px-3 py-1 bg-blue-400 text-white rounded hover:bg-blue-500 text-sm">Detail
                                        Transaksi</button></a>
                                <button
                                    onclick="sendInvoiceWhatsapp('{{ $transaction->member->phone ?? '' }}', {{ $transaction->id }})"
                                    type="button"
                                    class="px-3 py-1 bg-green-400 text-white rounded hover:bg-green-500 text-sm cursor-pointer disabled:bg-gray-300 disabled:cursor-not-allowed"
                                    @if ($transaction->status != 'completed' || $transaction->send_whatsapp_notification || !isset($transaction->member)) disabled @endif>Kirim invoice Whatsapp</button>
                                <button class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm"
                                    onclick="openDeleteModal({{ $transaction->id }}, '{{ addslashes($transaction->invoice_number) }}')">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                Tidak ada transaksi yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($transactions->hasPages())
                <div class="my-4">
                    <nav class="flex justify-center">
                        <ul class="flex items-center space-x-1">
                            {{-- Tombol Previous --}}
                            @if ($transactions->onFirstPage())
                                <li>
                                    <span
                                        class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">&laquo;</span>
                                </li>
                            @else
                                <li>
                                    <a href="{{ $transactions->previousPageUrl() }}"
                                        class="px-3 py-1 text-gray-700 bg-white border rounded hover:bg-blue-50">&laquo;</a>
                                </li>
                            @endif

                            {{-- Nomor Halaman --}}
                            @foreach ($transactions->links()->elements[0] ?? [] as $page => $url)
                                @if ($page == $transactions->currentPage())
                                    <li>
                                        <span
                                            class="px-3 py-1 text-white bg-blue-500 border border-blue-500 rounded">{{ $page }}</span>
                                    </li>
                                @else
                                    <li>
                                        <a href="{{ $url }}"
                                            class="px-3 py-1 text-gray-700 bg-white border rounded hover:bg-blue-50">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach

                            {{-- Tombol Next --}}
                            @if ($transactions->hasMorePages())
                                <li>
                                    <a href="{{ $transactions->nextPageUrl() }}"
                                        class="px-3 py-1 text-gray-700 bg-white border rounded hover:bg-blue-50">&raquo;</a>
                                </li>
                            @else
                                <li>
                                    <span
                                        class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">&raquo;</span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                </div>
            @endif

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function sendInvoiceWhatsapp(phone, transactionId) {
            var url = "{{ route('kasir.transaction.send.whatsapp') }}";
            axios.post(url, {
                    phone: phone,
                    transaction_id: transactionId,
                })
                .then(response => {
                    if (response.data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Invoice berhasil dikirimkan ke WhatsApp',
                        });

                        // Disable the button after sending
                        this.disabled = true;
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Invoice gagal dikirimkan ke WhatsApp',
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response?.data?.message || error.message ||
                            'Terjadi kesalahan saat mengirim invoice',
                    });
                });
        }
    </script>
@endpush
