@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-4xl font-semibold text-gray-700 mb-4">Daftar Kasir</h1>

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
        <form method="GET" action="{{ route('admin.kasir.index') }}"
            class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-2 md:space-y-0 mb-4">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari kasir berdasarkan nama atau telepon..."
                class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" />
            <!-- Status Filter -->
            <select name="status"
                class="w-full md:w-1/4 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">Semua Status Kasir</option>
                <option value="online" {{ request('status') === 'online' ? 'selected' : '' }}>Online
                </option>
                <option value="offline" {{ request('status') === 'offline' ? 'selected' : '' }}>Offline
                </option>
            </select>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Filter</button>
            <div class="md:ml-auto">
                <button type="button" id="openAddModal"
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Tambah
                    Kasir</button>
            </div>
        </form>

        <div class="overflow-x-auto bg-white rounded-xl shadow border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Foto Profile</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Nama</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Email</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Telepon</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Total Transaksi yang Dibuat</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Total Produk Terjual</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($kasir as $user)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <img src="{{ $user->profile_image_url }}" alt="Logo"
                                    class="h-15 w-15 object-cover rounded-full">
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $user->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $user->email }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $user->phone }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <p
                                    class="inline-flex rounded-full bg-opacity-10 px-3 py-1 text-sm font-medium {{ $user->is_logged_in ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                    {{ $user->is_logged_in ? 'Online' : 'Offline' }}
                                </p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $user->transactions->count() }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $user->transactions->flatMap->transactionDetails->count() }}</td>
                            <td class="px-6 py-4 space-x-2">
                                <button class="px-3 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500 text-sm"
                                    onclick='openEditModal(@json($user))'>Edit</button>
                                <button class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm"
                                    onclick="openDeleteModal({{ $user->id }}, '{{ addslashes($user->name) }}')">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                Tidak ada kasir yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('modals')
    <!-- Modal Tambah/Edit Kasir -->
    <div id="addEditModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span id="closeAddEditModal" class="float-right cursor-pointer text-gray-500">&times;</span>
            <h2 id="modalTitle" class="text-lg font-semibold mb-4">Tambah Kasir</h2>
            <form id="modalForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="id" id="kasirId">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Nama <span class="text-red-600">*</span></label>
                    <input type="text" name="name" id="kasirName" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
                        required />
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Email <span class="text-red-600">*</span></label>
                    <input type="email" name="email" id="kasirEmail" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
                        required />
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Telepon <span
                            class="text-red-600">*</span></label>
                    <input type="tel" name="phone" id="kasirPhone" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" />
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Password <span id="passwordRequired"
                            class="text-red-600">*</span></label>
                    <input type="password" name="password" id="kasirPassword" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
                        minlength="8" />
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Konfirmasi Password <span
                            id="confirmPasswordRequired" class="text-red-600">*</span></label>
                    <input type="password" name="password_confirmation" id="kasirPasswordConfirm" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
                        minlength="8" />
                </div>
                <div class="flex justify-end">
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Konfirmasi Delete -->
    <div id="deleteModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span id="closeDeleteModal" class="float-right cursor-pointer text-gray-500">&times;</span>
            <h2 class="text-lg font-semibold mb-4">Konfirmasi Hapus</h2>
            <p id="deleteMessage" class="mb-4">Apakah Anda yakin ingin menghapus kasir ini?</p>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-end">
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Hapus</button>
                    <button type="button" id="cancelDelete"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Batal</button>
                </div>
            </form>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        const addEditModal = document.getElementById("addEditModal");
        const deleteModal = document.getElementById("deleteModal");
        const closeAddEditModal = document.getElementById("closeAddEditModal");
        const closeDeleteModal = document.getElementById("closeDeleteModal");
        const cancelDelete = document.getElementById("cancelDelete");
        const modalForm = document.getElementById("modalForm");
        const deleteForm = document.getElementById("deleteForm");

        let isEditMode = false;

        // Open Add Modal
        document.getElementById("openAddModal").onclick = function() {
            isEditMode = false;
            document.getElementById("modalTitle").innerText = "Tambah Kasir";
            modalForm.action = "{{ route('admin.kasir.store') }}";
            document.getElementById("formMethod").value = "POST";
            document.getElementById("kasirId").value = "";
            document.getElementById("kasirName").value = "";
            document.getElementById("kasirEmail").value = "";
            document.getElementById("kasirPhone").value = "";
            document.getElementById("kasirPassword").value = "";
            document.getElementById("kasirPasswordConfirm").value = "";

            // Saat tambah, password tetap required
            document.getElementById("kasirPassword").setAttribute("required", "required");
            document.getElementById("kasirPasswordConfirm").setAttribute("required", "required");
            document.getElementById('passwordRequired').classList.remove('hidden')
            document.getElementById('confirmPasswordRequired').classList.remove('hidden');

            addEditModal.style.display = "block";
        }

        // Open Edit Modal
        function openEditModal(user) {
            isEditMode = true;
            document.getElementById("modalTitle").innerText = "Edit Kasir";
            modalForm.action = "/admin/kasir/" + user.id;
            document.getElementById("formMethod").value = "PUT";
            document.getElementById("kasirId").value = user.id;
            document.getElementById("kasirName").value = user.name || "";
            document.getElementById("kasirEmail").value = user.email || "";
            document.getElementById("kasirPhone").value = user.phone || "";
            document.getElementById("kasirPassword").value = "";
            document.getElementById("kasirPasswordConfirm").value = "";
            // Saat edit, password tidak required
            document.getElementById("kasirPassword").removeAttribute("required");
            document.getElementById("kasirPasswordConfirm").removeAttribute("required");
            document.getElementById('passwordRequired').classList.add('hidden');
            document.getElementById('confirmPasswordRequired').classList.add('hidden');
            addEditModal.style.display = "block";
        }

        // Open Delete Modal
        function openDeleteModal(id, name) {
            document.getElementById("deleteMessage").innerText = `Apakah Anda yakin ingin menghapus kasir "${name}"?`;
            deleteForm.action = "/admin/kasir/" + id;
            deleteModal.style.display = "block";
        }

        // Close Modals
        closeAddEditModal.onclick = function() {
            addEditModal.style.display = "none";
        }
        closeDeleteModal.onclick = function() {
            deleteModal.style.display = "none";
        }
        cancelDelete.onclick = function() {
            deleteModal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == addEditModal) {
                addEditModal.style.display = "none";
            }
            if (event.target == deleteModal) {
                deleteModal.style.display = "none";
            }
        }
    </script>
@endpush
