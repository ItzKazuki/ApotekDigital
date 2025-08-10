@extends('layouts.kasir')

@php use Carbon\Carbon; @endphp

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <h2 class="text-lg font-extrabold text-gray-900 mb-4 md:mb-0">
            Pilih Kategori
        </h2>
        <form class="flex items-center space-x-2 text-gray-600 text-sm">
            <i class="fas fa-search">
            </i>
            <input name="search"
                class="rounded-md border border-gray-300 px-3 py-1.5 text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-[#f9d36b] focus:border-transparent"
                placeholder="Cari item menu" type="search" />
        </form>
    </div>
    <!-- Categories -->
    <nav class="flex space-x-4 overflow-x-auto scrollbar-hide pb-4 mb-8">
        @foreach ($categories as $category)
            @php
                $isActive = request('category') === $category->name;
                $link = $isActive
                    ? route('kasir.index') // kalau aktif, klik akan reset filter
                    : route('kasir.index', ['category' => $category->name]); // kalau belum aktif, pasang filter
            @endphp

            <a href="{{ $link }}"
                class="flex flex-col items-center justify-center min-w-[120px] {{ $isActive ? 'bg-[#f9d36b]' : 'bg-[#fff9e6]' }} rounded-md p-4 border border-[#f9d36b] shrink-0">
                <img alt="{{ $category->name }} icon" class="mb-2" height="40" loading="lazy"
                    src="{{ $category->image_url }}" width="40" />
                <span class="text-xs font-extrabold text-black leading-tight text-center">
                    {{ $category->name }}
                </span>
            </a>
        @endforeach
    </nav>

    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <h2 class="text-lg font-extrabold text-gray-900 mb-4 md:mb-0">
            Pilih Obat Obatan
        </h2>
    </div>
    <!-- Menu Items Grid -->
    <div class="overflow-y-auto" style="max-height: 600px;">
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse ($drugs as $drug)
                @php
                    $expiredDate = Carbon::parse($drug->expired_at);
                    $today = Carbon::today();
                    $diffInDays = $today->diffInDays($expiredDate, false);

                    if ($diffInDays < 0) {
                        $expiredBadgeColor = 'bg-red-500 text-white';
                        $expiredLabel = 'Kadaluarsa';
                    } elseif ($diffInDays <= 30) {
                        $expiredBadgeColor = 'bg-yellow-400 text-black';
                        $expiredLabel = 'Hampir Expired';
                    } else {
                        $expiredBadgeColor = 'bg-green-600 text-white';
                        $expiredLabel = 'Aman';
                    }

                    if ($drug->stock <= 4) {
                        $stockBadgeColor = 'bg-red-500 text-white';
                        $stockLabel = 'Stok Kritis';
                    } elseif ($drug->stock <= 29) {
                        $stockBadgeColor = 'bg-yellow-400 text-black';
                        $stockLabel = 'Stok Menipis';
                    } else {
                        $stockBadgeColor = 'bg-green-600 text-white';
                        $stockLabel = 'Stok Aman';
                    }

                    $isExpired = $drug->expired_at && Carbon::parse($drug->expired_at)->isPast();
                @endphp

                <article class="bg-white rounded-md shadow-sm p-4 relative flex flex-col">
                    <button aria-label="Add to cart"
                        class="absolute top-3 right-3 text-gray-300 cursor-pointer disabled:cursor-not-allowed"
                        @if ($drug->stock > 0 && !$isExpired) onclick="addDrugToCart({{ $drug->id }})" @else disabled @endif>
                        <i class="fas fa-shopping-basket"></i>
                    </button>

                    <!-- Image container -->
                    <div class="w-full aspect-[4/3] mb-4 overflow-hidden rounded">
                        <img alt="{{ $drug->name }} image"
                            src="{{ $drug->image_url ?? 'https://via.placeholder.com/200x180?text=No+Image' }}"
                            class="w-full h-full object-cover" loading="lazy" />
                    </div>

                    <!-- Badge -->
                    <div class="space-y-1">
                        <span class="inline-block {{ $expiredBadgeColor }} text-[10px] font-semibold rounded px-2 py-0.5">
                            {{ $drug->expired_at }} ({{ $expiredLabel }})
                        </span>
                        <span class="inline-block {{ $stockBadgeColor }} text-[10px] font-semibold rounded px-2 py-0.5">
                            Stok: {{ $drug->stock }} ({{ $stockLabel }})
                        </span>
                    </div>

                    <!-- Info -->
                    <h3 class="font-extrabold text-md mb-1 mt-2">
                        {{ $drug->name }} <span class="text-xs text-gray-400">{{ $drug->category->name }}</span>
                    </h3>
                    <p class="text-red-500 text-sm mb-1 font-semibold inline">
                        Rp{{ number_format($drug->price, 0, ',', '.') }}
                    </p>

                    <!-- Button -->
                    <button
                        @if ($drug->stock > 0 && !$isExpired) onclick="addDrugToCart({{ $drug->id }})" @else disabled @endif
                        class="mt-auto w-full bg-[#f9d36b] border border-[#f9d36b] rounded-md py-2 font-extrabold text-xs flex items-center justify-center gap-2 hover:bg-yellow-400 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-plus"></i>
                        Pilih Obat
                    </button>
                </article>
            @empty
                <div class="col-span-full text-center text-gray-500 py-10">
                    <i class="fas fa-pills text-4xl mb-3"></i>
                    <p class="font-semibold">Obat tidak tersedia</p>
                </div>
            @endforelse
        </section>

    </div>
@endsection

@section('keranjang')
    <!-- Right Sidebar -->
    <aside class="w-96 bg-white p-6 flex flex-col space-y-6 border-l border-gray-200 pt-8">
        <h2 class="text-lg font-extrabold flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
            <span class="flex items-center gap-2">
                <i class="fas fa-shopping-basket"></i>
                Keranjang
            </span>
            <span class="text-sm font-normal text-gray-600">
                Sisa waktu keranjang:
                <span id="cart-countdown" class="font-mono font-bold text-yellow-500">--:--</span>
            </span>
        </h2>

        <div class="bg-[#f9fafd] rounded-lg p-4 space-y-4 flex-1 overflow-y-auto max-h-[300px]" id="cartContainer"
            style="">
        </div>
        <!-- Summary -->
        <div class="border border-gray-300 rounded-md p-4 text-sm text-gray-900 hidden" id="detailCartItems">
            <div class="flex justify-between mb-2">
                <span>
                    Sub Total
                </span>
                <span id="totalPriceCartItems">
                    Rp0
                </span>
            </div>
            @if (config('app.tax.enabled'))
                <div class="flex justify-between">
                    <span>
                        Pajak ({{ config('app.tax.name') }})
                    </span>
                    <span id="totalPriceCartItems">
                        Rp{{ number_format(config('app.tax.value'), 0, ',', '.') }}
                    </span>
                </div>
            @endif
        </div>
        <div class="border border-gray-300 rounded-md p-4 font-extrabold text-lg flex justify-between" style="">
            <span>
                Total
            </span>
            <span id="totalPrice">
                Rp0
            </span>
        </div>
        <!-- New Inputs for Cash, Member Phone and Change -->
        <form class="space-y-4">
            <div class="flex gap-4">
                <div class="w-1/2">
                    <label class="block text-sm font-semibold mb-1" for="cashInput" style="">
                        Uang Tunai
                    </label>
                    <input
                        class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#f9d36b] focus:border-transparent"
                        id="cashInput" placeholder="Masukkan uang tunai" type="number" min="0" step="1000"
                        style="" />
                </div>
                <div class="w-1/2">
                    <label class="block text-sm font-semibold mb-1 text-gray-900" for="metode-pembayaran">
                        Metode Pembayaran
                    </label>
                    <select
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"
                        id="metode-pembayaran" name="metode-pembayaran">
                        <option value="" disabled selected>
                            Pilih
                        </option>
                        @foreach ($paymentAvailable as $payment)
                            <option value="{{ $payment }}">{{ ucwords($payment) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1" for="memberPhone" id="phoneNumberMemberLabel"
                    style="">
                    Member (Nomor Telepon)
                </label>
                <div class="flex">
                    <input
                        class="w-[70%] rounded-l-md border border-gray-300 px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#f9d36b] focus:border-transparent"
                        id="phoneNumberMember" placeholder="Masukkan nomor member" type="tel" pattern="[0-9]*"
                        inputmode="numeric" style="" />
                    <button type="button" onclick="checkMember()" id="searchMemberBtn"
                        class="w-[30%] flex items-center justify-center bg-[#f9d36b] border border-l-0 border-gray-300 rounded-r-md hover:bg-yellow-400">
                        <i class="fas fa-search text-gray-700"></i>
                    </button>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1" for="changeOutput" style="">
                    Kembalian
                </label>
                <input
                    class="w-full rounded-md border border-gray-300 bg-gray-100 px-3 py-2 text-sm text-gray-600 cursor-not-allowed"
                    id="changeOutput" readonly="" type="text" value="Rp0" />
            </div>
        </form>
        <button onclick="createTransaction()"
            class="bg-[#f9d36b] rounded-md py-3 font-extrabold text-black hover:bg-yellow-400" type="button">
            Bayar
        </button>
    </aside>
@endsection

@push('scripts')
    <script>
        let cartCountdownInterval = null;
        let cartCountdownTimeout = null;
        let barcode = '';
        let scanTimeout;
        const allowedChars = /^[0-9A-Za-z\-\_]+$/; // karakter yang diizinkan

        // Example script to handle cash input and calculate change
        // onload get cart details dynamically
        document.addEventListener('DOMContentLoaded', function() {
            fetchCartItems();

            const isCartTimerEnabled = {{ config('app.cart.cart_timer_enabled') ? 'true' : 'false' }};
            const expiredAt = {!! session('cart_expired_at') ? '"' . session('cart_expired_at') . '"' : 'null' !!};

            if (isCartTimerEnabled && expiredAt) {
                initializeCartCountdown(expiredAt);
            }
        });

        document.addEventListener('keydown', function(e) {
            if (scanTimeout) clearTimeout(scanTimeout);

            if (e.key === 'Enter') {
                if (barcode.length > 0) {
                    axios.post("{{ route('kasir.cart.store.barcode') }}", {
                            barcode: barcode
                        }, {
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => {
                            if (response.data.success) {
                                // Update keranjang di halaman
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Produk berhasil ditambahkan ke keranjang!',
                                    confirmButtonText: 'OK',
                                    timer: 1000,
                                    timerProgressBar: true,
                                });

                                fetchCartItems();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.data.message,
                                    confirmButtonText: 'OK',
                                    timer: 1000,
                                    timerProgressBar: true,
                                });
                            }
                        })
                        .catch(error => {
                            console.error(error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: error.response?.data?.message || error.message ||
                                    'Terjadi kesalahan saat menambahkan produk ke keranjang.',
                                confirmButtonText: 'OK',
                                timer: 1000,
                                timerProgressBar: true,
                            });
                        });

                    barcode = '';
                }
            } else {
                // Filter hanya karakter yang diizinkan
                if (allowedChars.test(e.key)) {
                    barcode += e.key;
                }
            }

            scanTimeout = setTimeout(() => barcode = '', 300);
        });

        /**
         * Inisialisasi countdown keranjang belanja
         * @param {string} expiredAt - Waktu expire dalam format string ISO
         */
        function initializeCartCountdown(expiredAt) {
            // Bersihkan timer sebelumnya jika ada
            if (cartCountdownInterval) clearInterval(cartCountdownInterval);
            if (cartCountdownTimeout) clearTimeout(cartCountdownTimeout);

            const expireTime = new Date(expiredAt);
            const now = new Date();
            let diffMs = expireTime - now;

            const countdownElement = document.getElementById('cart-countdown');

            if (diffMs <= 0) {
                countdownElement.textContent = "--:--";
                showExpiredMessage();
                return;
            }

            cartCountdownInterval = setInterval(() => {
                const now = new Date();
                const remaining = expireTime - now;

                if (remaining <= 0) {
                    clearInterval(cartCountdownInterval);
                    countdownElement.textContent = "--:--";
                    showExpiredMessage();
                    return;
                }

                const minutes = String(Math.floor(remaining / 60000)).padStart(2, '0');
                const seconds = String(Math.floor((remaining % 60000) / 1000)).padStart(2, '0');
                countdownElement.textContent = `${minutes}:${seconds}`;
            }, 1000);

            // Timer auto-expire
            cartCountdownTimeout = setTimeout(() => {
                clearInterval(cartCountdownInterval);
                showExpiredMessage();
            }, diffMs);
        }

        function clearCartCountdown() {
            if (cartCountdownInterval) clearInterval(cartCountdownInterval);
            if (cartCountdownTimeout) clearTimeout(cartCountdownTimeout);
            cartCountdownInterval = null;
            cartCountdownTimeout = null;

            const countdownElement = document.getElementById('cart-countdown');
            countdownElement.textContent = "--:--";
        }

        function showExpiredMessage() {
            Swal.fire({
                title: 'Keranjang Kosong',
                text: 'Keranjang Anda telah dikosongkan karena tidak ada aktivitas.',
                icon: 'info',
                confirmButtonText: 'OK',
                timer: 3000,
                timerProgressBar: true,
                didClose: () => {
                    clearCartItems();
                }
            });
        }

        document.getElementById('cashInput').addEventListener('input', function() {
            const cash = parseFloat(this.value) || 0;
            const totalText = document.getElementById('totalPrice').innerText;
            const total = parseInt(totalText.replace(/[^\d]/g, ''), 10) || 0;

            const changeOutput = document.getElementById('changeOutput');
            const difference = cash - total;

            if (difference >= 0) {
                changeOutput.value = `Rp${difference.toLocaleString('id-ID')}`;
            } else {
                changeOutput.value = `Kurang: Rp${Math.abs(difference).toLocaleString('id-ID')}`;
            }
        });

        // cart functionality
        function addDrugToCart(drugId) {
            axios.post("{{ route('kasir.cart.store') }}", {
                    drug_id: drugId
                })
                .then(response => {
                    if (response.data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Obat berhasil ditambahkan ke keranjang!',
                            confirmButtonText: 'OK',
                            timer: 800,
                            timerProgressBar: true,
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.data.message,
                            confirmButtonText: 'OK',
                            timer: 800,
                            timerProgressBar: true,
                        });
                    }

                    fetchCartItems();
                })
                .catch(error => {
                    console.error('Error adding drug to cart:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: error.response?.data?.message || error.message ||
                            'Terjadi kesalahan saat menambahkan obat ke keranjang.',
                    });
                });
        }

        function fetchCartItems() {
            axios.get("{{ route('kasir.cart.show') }}")
                .then(responseCartItems => {
                    const cartContainer = document.getElementById('cartContainer');
                    const subtotal = document.getElementById('totalPriceCartItems');
                    const totalSection = document.getElementById('totalPrice');
                    const detailCartItems = document.getElementById('detailCartItems');

                    const subtotalValue = responseCartItems.data.subtotal ?? 0;
                    subtotal.innerHTML = `Rp${subtotalValue.toLocaleString('id-ID')}`;

                    const tax = {{ config('app.tax.value') }};
                    // Remove dots from subtotalValue string and convert to integer
                    const subtotalClean = typeof subtotalValue === 'string' ?
                        parseInt(subtotalValue.replace(/\./g, ''), 10) :
                        parseInt(subtotalValue, 10);
                    const totalValue = subtotalClean + parseInt(tax);

                    const cartItems = Object.values(responseCartItems?.data?.cartItems || {});

                    // jika cart item nya 0 maka sembunyikan detailCartItems
                    if (cartItems.length >= 1) {
                        totalSection.innerHTML = `Rp${totalValue.toLocaleString('id-ID')}`;
                        detailCartItems.classList.remove('hidden');
                    } else {
                        totalSection.innerHTML = `Rp0`;
                        detailCartItems.classList.add('hidden');

                        // Hapus countdown karena keranjang kosong
                        clearCartCountdown();

                        // Panggil route untuk hapus timeout di server
                        axios.post("{{ route('kasir.cart.clearTimeout') }}", {}, {
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        }).catch(err => console.error('Error removing cart timeout:', err));
                    }

                    cartContainer.innerHTML = ''; // Clear the existing content
                    cartItems.forEach(item => {
                        const cartItem = `
                            <div class="flex items-center justify-between bg-white rounded-md p-4 shadow-sm mb-3">
                                <img alt="${item.name}"
                                    class="w-16 h-16 object-contain"
                                    height="60"
                                    loading="lazy"
                                    src="${item.attributes.image}"
                                    width="60" />
                                <div class="flex-1 ml-4">
                                    <h3 class="font-extrabold text-xs leading-tight mb-1">
                                        ${item.name}
                                    </h3>
                                    <p class="text-pink-500 text-xs font-semibold inline">
                                        Rp${item.price.toLocaleString('id-ID')}
                                    </p>
                                    <span class="text-gray-400 text-xs">
                                        /${item.attributes.packaging_types}
                                    </span>
                                </div>
                                <button aria-label="Remove item" class="ml-2 bg-red-400 hover:bg-red-500 text-white rounded-md p-1"
                                    onclick="removeFromCart(${item.id})">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                                <div class="ml-4 flex items-center space-x-2">
                                    <button aria-label="Decrease quantity" class="bg-[#f9d36b] rounded px-2 py-1 font-bold text-xs"
                                        onclick="updateCartQuantity(${item.id}, 'decrement')">-</button>
                                    <span class="font-bold text-xs">${item.quantity}</span>
                                    <button aria-label="Increase quantity" class="bg-[#f9d36b] rounded px-2 py-1 font-bold text-xs"
                                        onclick="updateCartQuantity(${item.id}, 'increment')">+</button>
                                </div>
                            </div>
                        `;
                        cartContainer.insertAdjacentHTML('beforeend', cartItem);

                        const expiredAt = responseCartItems.data.cart_expired_at;
                        if (expiredAt) {
                            initializeCartCountdown(expiredAt);
                        }

                    });

                })
        }

        function removeFromCart(drugId) {
            axios.post("{{ route('kasir.cart.removeItem') }}", {
                drug_id: drugId
            }).then(deleteItemCartResponse => {
                if (deleteItemCartResponse.data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: deleteItemCartResponse.data.message,
                        confirmButtonText: 'OK',
                        timer: 1000,
                        timerProgressBar: true,
                    })
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: deleteItemCartResponse.data.message,
                        confirmButtonText: 'OK',
                        timer: 1000,
                        timerProgressBar: true,
                    });
                }

                fetchCartItems();
            }).catch(error => {
                console.error('Error updating cart quantity:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: error.response?.data?.message || error.message ||
                        'Terjadi kesalahan saat memperbarui jumlah obat di keranjang.',
                });
            });
        }

        function updateCartQuantity(drugId, action) {
            let url = action === 'increment' ?
                `{{ route('kasir.cart.incrementItem', '') }}` :
                `{{ route('kasir.cart.decrementItem', '') }}`;
            axios.post(url, {
                drug_id: drugId
            }).then(quantityUpdateResponse => {
                if (quantityUpdateResponse.data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: quantityUpdateResponse.data.message,
                        confirmButtonText: 'OK',
                        timer: 500,
                        timerProgressBar: true,
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: quantityUpdateResponse.data.message,
                        confirmButtonText: 'OK',
                        timer: 500,
                        timerProgressBar: true,
                    });
                }

                fetchCartItems();
            }).catch(error => {
                console.error('Error updating cart quantity:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: error.response?.data?.message || error.message ||
                        'Terjadi kesalahan saat memperbarui jumlah obat di keranjang.',
                });
            });
        }

        // cart function end

        function checkMember() {
            const phoneNumberMemberInput = document.getElementById('phoneNumberMember');
            const phoneNumberMember = phoneNumberMemberInput.value;

            axios.post("{{ route('kasir.member.search') }}", {
                phone: phoneNumberMember
            }).then(memberResponse => {
                const member = memberResponse.data.member;
                if (memberResponse.data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: `Member dengan nomor ${phoneNumberMember} ditemukan dengan nama ${member.name}`,
                    });

                    // set phoneNumberInput to readonly and disable click on button search member
                    // Buat input readonly
                    phoneNumberMemberInput.readOnly = true;

                    // Disable tombol cari
                    const searchButton = phoneNumberMemberInput
                        .nextElementSibling; // karena button ada di sebelah input
                    searchButton.disabled = true;
                    searchButton.classList.add('opacity-50', 'cursor-not-allowed');

                    document.getElementById('phoneNumberMemberLabel').innerHTML =
                        `Member (${member.name})
                    <span class="text-xs text-gray-400">point: ${parseInt(member.point)}</span>
                    <span class="relative group inline-block align-middle ml-1">
                        <!-- Ikon Info -->
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 text-gray-500 cursor-pointer inline-block"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
                        </svg>

                        <!-- Tooltip -->
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 hidden group-hover:block
                                    bg-gray-800 text-white text-xs px-2 py-1 rounded shadow z-10 whitespace-nowrap">
                            Kadaluarsa pada ${new Date(member.expires_at).toLocaleDateString('id-ID')}
                        </div>
                    </span>`;

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: memberResponse.data.message,
                    });
                }
            }).catch(error => {
                console.error('Error mengambil data member:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: error.response?.data?.message || error.message ||
                        'Terjadi kesalahan saat mencari member.',
                });
            });
        }

        function createTransaction() {
            const cashInput = document.getElementById('cashInput');
            const cash = parseFloat(cashInput.value) || 0;
            const phoneNumberMember = document.getElementById('phoneNumberMember').value ?? null;
            const metodePembayaran = document.getElementById('metode-pembayaran').value;

            // cek jika metode pembayaran tidak dipilih
            if (!metodePembayaran) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Silakan pilih metode pembayaran.',
                    confirmButtonText: 'OK',
                    timer: 2500,
                    timerProgressBar: true,
                });
                return;
            }

            if (cash <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Uang tunai tidak boleh kurang dari atau sama dengan 0.',
                    confirmButtonText: 'OK',
                    timer: 2500,
                    timerProgressBar: true,
                });
                return;
            }

            axios.post("{{ route('kasir.transaction.store') }}", {
                cash: cash,
                phone: phoneNumberMember,
                metode_pembayaran: metodePembayaran
            }).then(transactionResponse => {
                if (transactionResponse.data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: transactionResponse.data.message,
                        confirmButtonText: 'Lihat Transaksi',
                    }).then(result => {
                        if (result.isConfirmed) {
                            window.location.href = transactionResponse.data.redirect;
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: transactionResponse.data.message,
                    });
                }
            }).catch(error => {
                console.error('Error creating transaction:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: error.response?.data?.message || error.message ||
                        'Terjadi kesalahan saat membuat transaksi.',
                });
            });
        }

        function clearCartItems() {
            axios.post("{{ route('kasir.cart.clearItems') }}")
                .then(response => {
                    fetchCartItems();
                })
                .catch(error => {
                    console.error('Error clearing cart items:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: error.response?.data?.message || error.message ||
                            'Terjadi kesalahan saat mengosongkan keranjang.',
                    });
                });
        }
    </script>
@endpush
