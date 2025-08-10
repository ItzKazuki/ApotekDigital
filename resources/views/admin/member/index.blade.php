@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-4xl font-semibold text-gray-700 mb-4">Daftar Member</h1>

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
        <form method="GET" action="{{ route('admin.member.index') }}"
            class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-2 md:space-y-0 mb-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari member berdasarkan no telpon atau nama..."
                class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" />
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Filter</button>
            <div class="md:ml-auto">
                <button type="button" id="openAddModal" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Tambah
                    Member</button>
            </div>
        </form>

        <div class="overflow-x-auto bg-white rounded-xl shadow border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Nama</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Telepon</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Expired</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Point</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($members as $member)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $member->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $member->phone }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $member->expires_at ? \Carbon\Carbon::parse($member->expires_at)->format('d-m-Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ number_format($member->point, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 space-x-2">
                                <button class="px-3 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500 text-sm"
                                    onclick='openEditModal(@json($member))'>Edit</button>
                                <button class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm"
                                    onclick="openDeleteModal({{ $member->id }}, '{{ addslashes($member->name) }}')">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                Tidak ada member yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="my-4">
                @if ($members->hasPages())
                    <nav class="flex justify-center">
                        <ul class="flex items-center space-x-1">
                            {{-- Tombol Previous --}}
                            @if ($members->onFirstPage())
                                <li>
                                    <span
                                        class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">&laquo;</span>
                                </li>
                            @else
                                <li>
                                    <a href="{{ $members->previousPageUrl() }}"
                                        class="px-3 py-1 text-gray-700 bg-white border rounded hover:bg-blue-50">&laquo;</a>
                                </li>
                            @endif

                            {{-- Nomor Halaman --}}
                            @foreach ($members->links()->elements[0] ?? [] as $page => $url)
                                @if ($page == $members->currentPage())
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
                            @if ($members->hasMorePages())
                                <li>
                                    <a href="{{ $members->nextPageUrl() }}"
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
                @endif
            </div>
        </div>
    </div>
@endsection

@push('modals')
    <!-- Modal Tambah/Edit Member -->
    <div id="addEditModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span id="closeAddEditModal" class="float-right cursor-pointer text-gray-500">&times;</span>
            <h2 id="modalTitle" class="text-lg font-semibold mb-4">Tambah Member</h2>
            <form id="modalForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="id" id="memberId">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Nama <span class="text-red-600">*</span></label>
                    <input type="text" name="name" id="memberName"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
                        required />
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Telepon <span
                            class="text-red-600">*</span></label>
                    <input type="tel" name="phone" id="memberPhone" required
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
            <p id="deleteMessage" class="mb-4">Apakah Anda yakin ingin menghapus member ini?</p>
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
            document.getElementById("modalTitle").innerText = "Tambah Member";
            modalForm.action = "{{ route('admin.member.store') }}";
            document.getElementById("formMethod").value = "POST";
            document.getElementById("memberId").value = "";
            document.getElementById("memberName").value = "";
            document.getElementById("memberPhone").value = "";
            addEditModal.style.display = "block";
        }

        // Open Edit Modal
        function openEditModal(member) {
            isEditMode = true;
            document.getElementById("modalTitle").innerText = "Edit Member";
            modalForm.action = "/admin/member/" + member.id;
            document.getElementById("formMethod").value = "PUT";
            document.getElementById("memberId").value = member.id;
            document.getElementById("memberName").value = member.name || "";
            document.getElementById("memberPhone").value = member.phone || "";
            addEditModal.style.display = "block";
        }

        // Open Delete Modal
        function openDeleteModal(id, name) {
            document.getElementById("deleteMessage").innerText = `Apakah Anda yakin ingin menghapus member "${name}"?`;
            deleteForm.action = "/admin/member/" + id;
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
