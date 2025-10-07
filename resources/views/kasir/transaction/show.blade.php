@extends('layouts.kasir')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/struk.css') }}">
@endpush

@section('content')
    <div class="grid grid-cols-1 gap-9 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 px-15 py-4">
        <div class="flex flex-col gap-9 col-span-2">
            <!-- Contact Form -->

            <div class="rounded-lg border border-gray-200 bg-white shadow-md">
                <!-- Header -->
                <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M3 7h18M3 12h18M3 17h18" />
                        </svg>
                        Detail Order
                    </h3>
                </div>

                <!-- Content -->
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 px-6 py-4">
                    <!-- Detail Member -->
                    @if ($transaction->member)
                        <div>
                            <h2 class="font-bold text-lg border-b border-gray-200 pb-2 mb-3">Detail Member</h2>
                            <div class="space-y-3 text-sm text-gray-700">
                                <div>
                                    <h3 class="font-semibold">Nama Member</h3>
                                    <p>{{ $transaction->member->name }}</p>
                                </div>
                                <div>
                                    <h3 class="font-semibold">Nomor Telepon</h3>
                                    <p>{{ $transaction->member->phone }}</p>
                                </div>
                                <div>
                                    <h3 class="font-semibold">Reward Poin</h3>
                                    <p>{{ number_format($transaction->reward_point, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <h3 class="font-semibold">Status Member</h3>
                                    <span
                                        class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                            {{ $transaction->member->status == 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                        {{ ucfirst($transaction->member->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Detail Transaksi -->
                    <div>
                        <h2 class="font-bold text-lg border-b border-gray-200 pb-2 mb-3">Detail Transaksi</h2>
                        <div class="space-y-3 text-sm text-gray-700">
                            <div>
                                <h3 class="font-semibold">Metode Pembayaran</h3>
                                <span
                                    class="inline-flex items-center rounded-full bg-gray-100 border px-3 py-1 text-xs font-medium">
                                    {{ $transaction->payment_method }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <h3 class="font-semibold">Total Harga</h3>
                                    <p class="font-medium">Rp. {{ number_format($transaction->total, 0, ',', '.') }}</p>
                                </div>
                                @if (config('app.tax.enabled'))
                                    <div>
                                        <h3 class="font-semibold">Pajak ({{ config('app.tax.name') }})</h3>
                                        <p>Rp. {{ number_format(config('app.tax.value'), 0, ',', '.') }}</p>
                                    </div>
                                @endif
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <h3 class="font-semibold">Uang Masuk</h3>
                                    <p class="text-green-600 font-medium">Rp.
                                        {{ number_format($transaction->cash, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <h3 class="font-semibold">Uang Keluar</h3>
                                    <p class="text-red-600 font-medium">- Rp.
                                        {{ number_format($transaction->change, 0, ',', '.') }}</p>
                                </div>
                            </div>

                            @if ($transaction->point_usage > 0 && $transaction->member)
                                <div>
                                    <h3 class="font-semibold">Point Digunakan</h3>
                                    <p class="text-red-600">- {{ number_format($transaction->point_usage, 0, ',', '.') }}
                                    </p>
                                </div>
                            @endif

                            <div>
                                <h3 class="font-semibold">Nama Kasir</h3>
                                <p class="text-black">{{ $transaction->kasir->name }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Button -->
                @if ($transaction->member && $transaction->status == 'completed')
                    <div class="p-6">
                        <button id="sendInvoice" type="button" value="{{ $transaction->member->phone }}"
                            @if ($transaction->send_whatsapp_notification) disabled @endif
                            class="flex w-full justify-center items-center gap-2 rounded-lg bg-green-600 text-white py-3 font-medium hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M16.7 14.5c-.3-.2-1.6-.8-1.8-.9-.2-.1-.4-.1-.6.1s-.7.9-.9 1.1c-.2.2-.3.2-.6.1-.3-.2-1.3-.5-2.5-1.6-.9-.8-1.6-1.7-1.8-2-.2-.3 0-.5.1-.6s.3-.3.4-.5c.1-.2.2-.4.3-.5.1-.2 0-.4 0-.6 0-.2-.6-1.5-.8-2s-.4-.5-.6-.5h-.5c-.2 0-.6.1-.9.4-.3.3-1.2 1.2-1.2 3s1.2 3.5 1.3 3.7c.2.2 2.3 3.6 5.5 5 .8.3 1.4.5 1.9.6.8.2 1.5.2 2.1.1.6-.1 1.6-.7 1.9-1.3.2-.6.2-1.1.1-1.3s-.2-.2-.5-.4z" />
                            </svg>
                            Kirim Invoice via WhatsApp
                        </button>
                    </div>

                    @push('scripts')
                        <script>
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
            <div class="rounded-sm border border-gray-300 bg-white shadow-default">
                <div class="border-b border-gray-300 px-6.5 py-4 flex justify-between">
                    <h3 class="font-medium text-black">
                        Struk Pembayaran
                    </h3>
                    <p
                        class="inline-flex rounded-full bg-opacity-10 px-3 py-1 text-sm font-medium
                @if ($transaction->status == \App\Models\Transaction::STATUS_PAID) bg-green-100 text-green-600
                @elseif ($transaction->status == \App\Models\Transaction::STATUS_CANCELED)
                    bg-red-100 text-red-600
                @else
                    bg-yellow-100 text-yellow-600 @endif
                ">
                        {{ $transaction->status }}
                    </p>
                </div>

                <div class="p-6 flex flex-col items-center justify-center">
                    @if ($transaction->status == \App\Models\Transaction::STATUS_PAID)
                        @include('kasir.transaction.struk')
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
                            </script>
                        @endpush
                    @elseif (
                        $transaction->status == \App\Models\Transaction::STATUS_PENDING &&
                            $transaction->payment_method == \App\Models\Transaction::PAYMENT_METHOD_QRIS)
                        <div class="flex flex-col items-center">
                            <h3 class="text-lg font-bold text-gray-700 mb-2">Bayar Sekarang</h3>
                            <img src="{{ $transaction->payment_url }}" alt="QRIS Payment" class="w-[250px] mb-4">
                            <div class="text-center mb-2">
                                <span class="font-bold text-red-600">Kadaluarsa dalam: </span>
                                <span id="countdown" class="font-bold text-red-600"></span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">Scan QR Code di atas untuk melakukan pembayaran.</p>
                            <p class="text-sm text-gray-600">Pastikan pembayaran dilakukan sebelum batas waktu yang
                                ditentukan.</p>
                        </div>

                        <button id="checkPayment" onclick="cekPembayaran()" type="button"
                            class="flex w-full justify-center rounded bg-green-600 text-white p-3 font-medium hover:bg-opacity-90 mt-4">
                            Cek Pembayaran
                        </button>

                        @push('scripts')
                            <script>
                                const expiredTime = new Date("{{ $transaction->payment_expired }}").getTime();

                                function updateCountdown() {
                                    const now = new Date().getTime();
                                    let distance = expiredTime - now;

                                    if (distance < 0) {
                                        document.getElementById("countdown").innerHTML = "Waktu Habis";
                                        clearInterval(interval);

                                        // fetch ke route untuk update payment status
                                        var url = "{{ route('kasir.transaction.update-payment', ['transaction' => $transaction->id]) }}";
                                        axios.post(url)
                                            .then(() => {
                                                // reload halaman setelah sukses
                                                location.reload();
                                            })
                                            .catch((err) => {
                                                console.error("Gagal update payment:", err);
                                            });

                                        return;
                                    }

                                    const hours = Math.floor(distance / (1000 * 60 * 60));
                                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                                    if (hours > 0) {
                                        document.getElementById("countdown").innerHTML =
                                            hours + " jam " + minutes + " menit";
                                    } else {
                                        document.getElementById("countdown").innerHTML =
                                            minutes + " menit " + seconds + " detik";
                                    }
                                }

                                const interval = setInterval(updateCountdown, 1000);
                                updateCountdown();

                                function cekPembayaran() {
                                    var url =
                                        "{{ route('kasir.transaction.update-payment', ['transaction' => $transaction->id]) }}";
                                    axios.post(url)
                                        .then(response => {
                                            if (response.data.transaction_status == 'settlement') {
                                                Swal.fire({
                                                    icon: 'success',
                                                    title: 'Pembayaran Berhasil',
                                                    text: 'Status pembayaran telah diperbarui.',
                                                }).then(() => {
                                                    location.reload();
                                                });
                                            } else if (response.data.transaction_status == 'pending') {
                                                Swal.fire({
                                                    icon: 'info',
                                                    title: 'Pembayaran Pending',
                                                    text: 'Pembayaran masih dalam proses.',
                                                });

                                            } else if (response.data.transaction_status == 'expire') {
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Pembayaran Kadaluarsa',
                                                    text: 'Pembayaran telah kadaluarsa.',
                                                }).then(() => {
                                                    location.reload();
                                                });
                                            } else {
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Pembayaran Gagal',
                                                    text: 'Status pembayaran tidak valid.',
                                                });
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error:', error);
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Error',
                                                text: 'Terjadi kesalahan saat memeriksa status pembayaran.',
                                            });
                                        });
                                }
                            </script>
                        @endpush
                    @elseif ($transaction->status == \App\Models\Transaction::STATUS_CANCELED)
                        <div class="flex flex-col items-center">
                            <h3 class="text-lg font-bold text-red-600 mb-4">Transaksi Dibatalkan</h3>
                            <p class="text-sm text-gray-600">Transaksi ini sudah dibatalkan dan tidak dapat dilanjutkan.
                            </p>
                        </div>
                    @endif

                    @if (config('app.thermal_printer_enabled'))
                        <button type="button"
                            onclick="cetakStruk(`{{ route('dashboard.transactions.print', ['transaction' => $transaction->id]) }}`)"
                            target="_blank"
                            class="flex w-full justify-center rounded bg-red-600 text-white p-3 font-medium hover:bg-opacity-90 mt-4">
                            Print Struk
                        </button>
                    @endif
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
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
    </script>
@endpush
