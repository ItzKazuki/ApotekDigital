@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold text-gray-700 mb-4">Daftar Kategori</h1>

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

    <!-- Tombol tambah kategori -->
    <div class="flex justify-end mb-4">
        <button id="openAddModal" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Tambah
            Kategori</button>
    </div>

    <!-- Tabel Kategori -->
    <div class="overflow-x-auto bg-white rounded-xl shadow border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">ID</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Logo Kategori</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Nama Kategori</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Deskripsi</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Jumlah Obat</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($categories as $category)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $category->id }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            @if ($category->image_path)
                                <img src="{{ asset('storage/' . $category->image_path) }}" alt="Logo"
                                    class="h-10 w-10 object-cover rounded">
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $category->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $category->description }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $category->drugs->count() }}</td>
                        <td class="px-6 py-4 space-x-2">
                            <button class="px-3 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500 text-sm"
                                onclick='openEditModal(@json($category))'>Edit</button>
                            <button class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm"
                                onclick="openDeleteModal({{ $category->id }}, '{{ addslashes($category->name) }}')">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            Tidak ada kategori yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="my-4">
            @if ($categories->hasPages())
                <nav class="flex justify-center">
                    <ul class="flex items-center space-x-1">
                        {{-- Tombol Previous --}}
                        @if ($categories->onFirstPage())
                            <li>
                                <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">&laquo;</span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $categories->previousPageUrl() }}"
                                    class="px-3 py-1 text-gray-700 bg-white border rounded hover:bg-blue-50">&laquo;</a>
                            </li>
                        @endif

                        {{-- Nomor Halaman --}}
                        @foreach ($categories->links()->elements[0] ?? [] as $page => $url)
                            @if ($page == $categories->currentPage())
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
                        @if ($categories->hasMorePages())
                            <li>
                                <a href="{{ $categories->nextPageUrl() }}"
                                    class="px-3 py-1 text-gray-700 bg-white border rounded hover:bg-blue-50">&raquo;</a>
                            </li>
                        @else
                            <li>
                                <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">&raquo;</span>
                            </li>
                        @endif
                    </ul>
                </nav>
            @endif
        </div>
    </div>
@endsection

@push('modals')
    <!-- Modal Tambah/Edit Kategori -->
    <div id="addEditModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span id="closeAddEditModal" class="float-right cursor-pointer text-gray-500">&times;</span>
            <h2 id="modalTitle" class="text-lg font-semibold mb-4">Tambah Kategori</h2>
            <form id="modalForm" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="id" id="categoryId">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Nama Kategori <span
                            class="text-red-600">*</span></label>
                    <input type="text" name="name" id="categoryName"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
                        required />
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea name="description" id="categoryDescription"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Gambar <span
                            class="text-red-600">*</span></label>
                    <input type="file" name="image" id="categoryImage"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" />
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
            <p id="deleteMessage" class="mb-4">Apakah Anda yakin ingin menghapus kategori ini?</p>
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

        // Open Add Modal
        document.getElementById("openAddModal").onclick = function() {
            document.getElementById("modalTitle").innerText = "Tambah Kategori";
            modalForm.action = "{{ route('admin.category.store') }}";
            document.getElementById("formMethod").value = "POST";
            document.getElementById("categoryId").value = "";
            document.getElementById("categoryName").value = "";
            document.getElementById("categoryDescription").value = "";
            document.getElementById("categoryImage").value = "";
            addEditModal.style.display = "block";
        }

        // Open Edit Modal
        function openEditModal(category) {
            document.getElementById("modalTitle").innerText = "Edit Kategori";
            modalForm.action = "/admin/category/" + category.id;
            document.getElementById("formMethod").value = "PUT";
            document.getElementById("categoryId").value = category.id;
            document.getElementById("categoryName").value = category.name || "";
            document.getElementById("categoryDescription").value = category.description || "";
            document.getElementById("categoryImage").value = ""; // file input can't be set for security
            addEditModal.style.display = "block";
        }

        // Open Delete Modal
        function openDeleteModal(id, name) {
            document.getElementById("deleteMessage").innerText = 'Apakah Anda yakin ingin menghapus kategori "' + name +
                '"?';
            deleteForm.action = "/admin/category/" + id;
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
