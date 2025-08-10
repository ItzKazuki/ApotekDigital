@extends('layouts.kasir')

@push('styles')
    <style>
        .member-card {
            transition: all 0.3s ease;
        }

        .modal {
            transition: opacity 0.3s ease;
        }
    </style>
@endpush

@section('content')
    <div class="bg-gray-100 min-h-screen p-6">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Cari Member</h1>

            <!-- Search Form -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex flex-col md:flex-row gap-4">
                    <input type="text" id="phoneInput" placeholder="Masukkan nomor telepon member"
                        class="flex-grow px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <div class="flex gap-2">
                        <button id="searchBtn"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Search
                        </button>
                        <button id="addMemberBtn"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                            Tambah Member
                        </button>
                    </div>
                </div>
            </div>

            <!-- Results Section -->
            <div id="results" class="space-y-4">
                <!-- Results will be displayed here -->
            </div>

            <!-- Add Member Modal -->
            <div id="addMemberModal"
                class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center p-4 modal pointer-events-none opacity-0 transition-opacity duration-300">
                <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-800">Tambah Member Baru</h2>
                        <button id="closeModalBtn" class="text-gray-500 hover:text-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form id="memberForm" class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" id="name" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                            <input type="text" id="phone" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="pt-2">
                            <button type="submit"
                                class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Simpan Member
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection
@push('scripts')
    <script>
        // DOM elements
        const searchBtn = document.getElementById('searchBtn');
        const addMemberBtn = document.getElementById('addMemberBtn');
        const phoneInput = document.getElementById('phoneInput');
        const resultsDiv = document.getElementById('results');
        const addMemberModal = document.getElementById('addMemberModal');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const memberForm = document.getElementById('memberForm');

        // Toggle modal function
        function toggleModal(show) {
            if (show) {
                addMemberModal.classList.remove('opacity-0', 'pointer-events-none');
                addMemberModal.classList.add('opacity-100', 'pointer-events-auto');
            } else {
                addMemberModal.classList.remove('opacity-100', 'pointer-events-auto');
                addMemberModal.classList.add('opacity-0', 'pointer-events-none');
            }
        }

        // Event listeners
        addMemberBtn.addEventListener('click', () => toggleModal(true));
        closeModalBtn.addEventListener('click', () => toggleModal(false));

        searchBtn.addEventListener('click', () => {
            const phone = phoneInput.value.trim();
            if (!phone) return;

            axios.post("{{ route('kasir.member.search') }}", {
                phone: phone
            }).then(searchMemberResponse => {

                const member = searchMemberResponse.data.member;

                if (searchMemberResponse.data.success && member) {
                    // Display member card
                    resultsDiv.innerHTML = `
                    <div class="member-card bg-gradient-to-br from-yellow-600 to-indigo-700 rounded-2xl shadow-xl p-6 text-white w-full max-w-md mx-auto">
                        <div class="flex justify-between items-center mb-6">
                            <div class="flex items-center space-x-2">
                                <img src="{{ asset('static/images/logo-apotek-v2-darkmode.png') }}" alt="Logo" class="mx-auto h-15 w-auto">
                            </div>
                            <span class="bg-yellow-800 text-white px-3 py-1 rounded-full text-xs">${parseInt(member.point)} pts</span>
                        </div>

                        <div class="mb-6">
                            <h2 class="text-2xl font-bold mb-2">${member.name}</h2>
                            <p class="text-yellow-100">${member.phone}</p>
                        </div>

                        <div class="flex justify-between items-center text-sm">
                            <div>
                                <p class="text-yellow-200">Sejak</p>
                                <p class="font-medium">${new Date(member.created_at).toLocaleDateString('id-ID')}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-yellow-200">Kadaluarsa pada</p>
                                <p class="font-medium">${new Date(member.expires_at).toLocaleDateString('id-ID')}</p>
                            </div>
                        </div>
                    </div>
                `;
                } else {
                    // Display not found message
                    resultsDiv.innerHTML = `
                    <div class="bg-white rounded-lg shadow p-6 text-center">
                        <p class="text-gray-600">Member dengan nomor telepon tersebut tidak ditemukan.</p>
                    </div>
                `;
                }
            }).catch(error => {
                console.error(error);
                // Display not found message
                resultsDiv.innerHTML = `
                    <div class="bg-white rounded-lg shadow p-6 text-center">
                        <p class="text-gray-600">Member dengan nomor telepon tersebut tidak ditemukan.</p>
                    </div>
                `;
            })
        });

        // Form submission
        memberForm.addEventListener('submit', (e) => {
            e.preventDefault();

            const newMember = {
                name: document.getElementById('name').value,
                phone: document.getElementById('phone').value,
            };

            axios.post("{{ route('kasir.member.create') }}", newMember).then(memberCreateResponse => {
                // Reset form and close modal
                memberForm.reset();
                toggleModal(false);

                // Show success message
                resultsDiv.innerHTML = `
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Berhasil!</strong>
                    <span class="block sm:inline">Member baru (${newMember.name}) telah ditambahkan.</span>
                </div>
            `;
            }).catch(error => {
                console.error('Error creating member:', error);
                memberForm.reset();
                toggleModal(false);

                resultsDiv.innerHTML = `
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">Gagal menambahkan member baru. ${error.response?.data?.message || error.message} Silakan coba lagi.</span>
                </div>
            `;
            });
        });
    </script>
@endpush
