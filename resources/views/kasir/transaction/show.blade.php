@extends('layouts.kasir')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/struk.css') }}">
@endpush

@section('content')
    <div class="grid grid-cols-1 gap-9 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 px-15 py-4">
        <div class="flex flex-col gap-9 col-span-2">
            <!-- Contact Form -->

            <div class="rounded-sm border border-gray-300 bg-white shadow-default">
                <div class="border-b border-gray-300 px-6 py-4 flex justify-between">
                    <h3 class="font-medium text-black">
                        Detail Order
                    </h3>

                </div>
                <div class="flex px-6">
                    @if ($transaction->member)
                        <div class="p-2 w-full xl:w-1/2">
                            <div class="border-b border-gray-200 pb-2">
                                <h1 class="font-bold text-lg">Detail Member</h1>
                            </div>
                            <div class="flex flex-col gap-2">
                                <div>
                                    <h3 class="font-bold text-md py-2">Nama Member</h3>
                                    <p>{{ $transaction->member->name }}</p>
                                </div>
                                <div>
                                    <h3 class="font-bold text-md py-2">Nomor Telepon</h3>
                                    <p>{{ $transaction->member->phone }}</p>
                                </div>
                                <div>
                                    <h3 class="font-bold text-md py-2">Reward Poin</h3>
                                    <p>{{ number_format($transaction->reward_point, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <h3 class="font-bold text-md py-2">Status Member</h3>
                                    <p
                                        class="inline-flex rounded-full bg-opacity-10 px-3 py-1 text-sm font-medium {{ $transaction->member->status == 'active' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                        {{ $transaction->member->status }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="p-2 w-full xl:w-1/2">
                        <div class="border-b border-gray-200 pb-2">
                            <h1 class="font-bold text-lg">Detail Transaksi</h1>
                        </div>
                        <div class="flex flex-col gap-2">
                            <div>
                                <h3 class="font-bold text-md py-2">Metode Pembayaran</h3>
                                <p
                                    class="inline-flex rounded-full bg-opacity-10 px-3 py-1 text-sm font-medium border border-gray-600 bg-gray-100">
                                    {{ $transaction->payment_method }}
                                </p>
                            </div>
                            <div class="flex gap-8">
                                <div>
                                    <h3 class="font-bold text-md py-2">Total Harga</h3>
                                    <p>Rp. {{ number_format($transaction->total, 0, ',', '.') }}</p>
                                </div>

                                @if (config('app.tax.enabled'))
                                    <div>
                                        <h3 class="font-bold text-md py-2">Pajak ({{ config('app.tax.name') }})</h3>
                                        <p>Rp. {{ number_format(config('app.tax.value'), 0, ',', '.') }}</p>
                                    </div>
                                @endif
                            </div>
                            <div class="flex gap-8">
                                <div>
                                    <h3 class="font-bold text-md py-2">Uang Masuk</h3>
                                    <p class="text-green-600">Rp. {{ number_format($transaction->cash, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <h3 class="font-bold text-md py-2">Uang Keluar</h3>
                                    <p class="text-red-600">- Rp.
                                        {{ number_format($transaction->change, 0, ',', '.') }}
                                    </p>
                                </div>
                                @if ($transaction->point_usage > 0 && $transaction->member)
                                    <div>
                                        <h3 class="font-bold text-md py-2">Point Digunakan</h3>
                                        <p class="text-red-600">-
                                            {{ number_format($transaction->point_usage, 0, ',', '.') }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h3 class="font-bold text-md py-2">Nama Kasir</h3>
                                <p class="text-black">{{ $transaction->kasir->name }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($transaction->member && $transaction->status == 'completed')
                    <div class="p-6.5">
                        <button id="sendInvoice" type="button" value="{{ $transaction->member->phone }}"
                            @if ($transaction->send_whatsapp_notification) disabled @endif
                            class="flex w-full justify-center rounded bg-yellow-600 text-white p-3 font-medium text-gray hover:bg-opacity-90 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                            Send Invoice to Whatshapp
                        </button>
                    </div>
                @endif
            </div>
            <div class="rounded-sm border border-gray-300 bg-white shadow-default    ">
                <div class="border-b border-gray-300 px-6.5 py-4 flex justify-between">
                    <h3 class="font-medium text-black  ">
                        Detail Order
                    </h3>
                    <p
                        class="inline-flex rounded-full bg-opacity-10 px-3 py-1 text-sm font-medium bg-yellow-100 text-yellow-600">
                        Jumlah barang {{ $transaction->transactionDetails->sum('quantity') }}
                    </p>
                </div>
                <div class="rounded-sm border-gray-300 bg-white shadow-default">
                    <div class="grid grid-cols-6 border-gray-300 bg-gray-200 px-4 py-4 sm:grid-cols-8 md:px-6 2xl:px-7.5">
                        <div class="col-span-1 flex items-center">
                            <p class="font-medium">#</p>
                        </div>
                        <div class="col-span-2 flex items-center">
                            <p class="font-medium">Nama Produk</p>
                        </div>
                        <div class="col-span-1 hidden items-center sm:flex">
                            <p class="font-medium">Kategori</p>
                        </div>
                        <div class="col-span-1 flex items-center">
                            <p class="font-medium">Harga</p>
                        </div>
                        <div class="col-span-1 flex items-center">
                            <p class="font-medium">Quantity</p>
                        </div>
                        <div class="col-span-1 flex items-center">
                            <p class="font-medium">Subtotal</p>
                        </div>
                    </div>

                    @foreach ($transaction->transactionDetails as $index => $transactionDetail)
                        <div class="grid grid-cols-6 px-4 py-4 border-gray-300 border-t sm:grid-cols-8 md:px-6 2xl:px-7.5">
                            <div class="col-span-1 hidden items-center sm:flex">
                                <p class="text-medium font-medium text-black">{{ $index + 1 }}</p>
                            </div>
                            <div class="col-span-2 flex items-center">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                                    <div class="h-12.5 w-15 rounded-md">
                                        <img src="{{ $transactionDetail->drug->image_url }}"
                                            alt="Product {{ $index + 1 }}" />
                                    </div>
                                    <p class="text-medium font-medium text-black">
                                        {{ $transactionDetail->drug->name }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-span-1 hidden items-center sm:flex">
                                <p class="text-medium font-medium text-black">
                                    {{ $transactionDetail->drug->category->name }}
                                </p>
                            </div>
                            <div class="col-span-1 flex items-center">
                                <p class="text-medium font-medium text-black">Rp.
                                    {{ number_format($transactionDetail->drug->price, 0, ',', '.') }}</p>
                            </div>
                            <div class="col-span-1 flex items-center">
                                <p class="text-medium font-medium text-black">{{ $transactionDetail->quantity }}</p>
                            </div>
                            <div class="col-span-1 flex items-center">
                                <p class="text-medium font-medium text-black">Rp.
                                    {{ number_format($transactionDetail->drug->price * $transactionDetail->quantity, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-9">
            <!-- Sign In Form -->
            <div class="rounded-sm border border-gray-300 bg-white shadow-default    ">
                <div class="border-b border-gray-300 px-6.5 py-4 flex justify-between">
                    <h3 class="font-medium text-black  ">
                        Struk Pembayaran
                    </h3>
                    <p
                        class="inline-flex rounded-full bg-opacity-10 px-3 py-1 text-sm font-medium {{ $transaction->status == 'completed' ? 'bg-green-100 text-green-600' : ($transaction->status == 'canceled' ? 'bg-red-100 text-red-600' : 'bg-yellow-100 text-yellow-600') }}">
                        {{ $transaction->status }}
                    </p>
                </div>
                <div class="p-6 flex flex-col items-center justify-center">
                    @include('kasir.transaction.struk')
                    @if (config('app.thermal_printer_enabled'))
                        <button type="button"
                            onclick="cetakStruk(`{{ route('dashboard.transactions.print', ['transaction' => $transaction->id]) }}`)"
                            target="_blank"
                            class="flex w-full justify-center rounded bg-red-600 text-white p-3 font-medium hover:bg-opacity-90">
                            Print Struk
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('vendor/davidshimjs-qrcodejs-04f46c6/qrcode.js') }}"></script>
    <script>
        var qrcode = new QRCode(document.getElementById("qrcode"), {
            text: "{{ $transaction->struk_url }}",
            width: 130,
            height: 130,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });

        // cetak struk using thermal printer
        function cetakStruk(url) {
            // return window.open(url, '_blank', 'location=yes,height=570,width=520,scrollbars=yes,status=yes'
            let b = event.target;
            b.setAttribute('data-old', b.textContent);
            b.textContent = 'wait';
            axios.get(url)
                .then(response => {
                    window.location.href = response.data; // main action
                })
                .catch(() => {
                    alert("ajax error");
                })
                .finally(() => {
                    b.textContent = b.getAttribute('data-old');
                });
        }

        document.getElementById('sendInvoice').addEventListener('click', function() {
            var url = "{{ route('kasir.transaction.send.whatsapp') }}";
            axios.post(url, {
                    phone: this.value,
                    transaction_id: "{{ $transaction->id }}",
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
        });
    </script>
@endpush
