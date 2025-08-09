@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold text-gray-700 mb-4">Daftar Obat-Obatan</h1>

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
    <form method="GET" action="{{ route('admin.drug.index') }}"
        class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-2 md:space-y-0 mb-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama produk..."
            class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" />
        <select name="category"
            class="w-full md:w-1/4 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
            <option value="">Semua Kategori</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Filter</button>
        <div class="md:ml-auto">
            <button type="button" id="openAddModal"
                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Tambah Obat</button>
        </div>
    </form>

    <!-- Tabel Produk -->
    <div class="overflow-x-auto bg-white rounded-xl shadow border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Nama Produk</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Gambar</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Harga</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Modal</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Kategori</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Stok</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Tanggal Exp</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Jenis Kemasan</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($drugs as $drug)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $drug->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            @if ($drug->image_path)
                                <img src="{{ asset('storage/' . $drug->image_path) }}" alt="Logo"
                                    class="h-10 w-10 object-cover rounded">
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">Rp. {{ number_format($drug->price, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">Rp. {{ number_format($drug->modal, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $drug->category ? $drug->category->name : '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $drug->stock }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $drug->expired_at }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $drug->packaging_types }}</td>
                        <td class="px-6 py-4 space-x-2">
                            <button class="px-3 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500 text-sm"
                                onclick='openEditModal(@json($drug))'>Edit</button>
                            <button onclick="showBarcodeModal('{{ $drug->barcode }}')"
                                class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm">Barcode</button>
                            <button class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm"
                                onclick="openDeleteModal({{ $drug->id }}, '{{ addslashes($drug->name) }}')">Delete</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push('modals')
    <!-- Modal untuk Tambah dan Edit Obat -->
    <div id="addEditModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span id="closeAddEditModal" class="float-right cursor-pointer text-gray-500">&times;</span>
            <h2 id="modalTitle" class="text-lg font-semibold mb-4">Tambah Obat</h2>
            <form id="modalForm" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="id" id="drugId">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Nama Produk</label>
                            <input type="text" name="name" id="productName"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
                                required />
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Kategori</label>
                            <select name="category_id" id="productCategory"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
                                required>
                                <option value="">Pilih Kategori</option>
                                @foreach (\App\Models\Category::all() as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Stok</label>
                            <input type="number" name="quantity" id="productStock"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
                                required />
                        </div>
                    </div>

                    <div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Harga Jual</label>
                            <input type="number" name="price" id="productPrice"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
                                required />
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Harga Beli</label>
                            <input type="number" name="purchase_price" id="productPurchasePrice"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" />
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Modal</label>
                            <input type="number" name="modal" id="productModal"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" />
                        </div>
                    </div>

                    <div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Expired</label>
                            <input type="date" name="expired_at" id="productExpired"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" />
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Barcode</label>
                            <input type="text" name="barcode" id="productBarcode"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
                                autocomplete="off" />
                        </div>
                        <div class="mb-4">
                            <label for="packagingType" class="block text-sm font-medium text-gray-700 mb-1">Jenis
                                Kemasan</label>
                            <select id="packagingType" name="packaging_types"
                                class="block w-full border-gray-300 rounded-md shadow-sm text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                <option value="">-- Pilih Jenis Kemasan --</option>
                                <option value="strip">Strip / Blister</option>
                                <option value="botol">Botol</option>
                                <option value="sachet">Sachet</option>
                                <option value="ampul">Ampul</option>
                                <option value="vial">Vial</option>
                                <option value="tube">Tube / Salep</option>
                                <option value="suppositoria">Suppositoria</option>
                                <option value="inhaler">Inhaler / Spray</option>
                                <option value="patch">Patch / Plester</option>
                                <option value="box">Box / Dus</option>
                            </select>
                        </div>
                    </div>

                    <div class="md:col-span-3">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <textarea name="description" id="productDescription"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Gambar</label>
                            <input type="file" name="image" id="productImage"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" />
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-4">
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>


    <!-- Modal untuk Konfirmasi Delete -->
    <div id="deleteModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span id="closeDeleteModal" class="float-right cursor-pointer text-gray-500">&times;</span>
            <h2 class="text-lg font-semibold mb-4">Konfirmasi Hapus</h2>
            <p id="deleteMessage" class="mb-4">Apakah Anda yakin ingin menghapus produk ini?</p>
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

    <div id="barcodeModal" class="fixed inset-0 z-[300] hidden" aria-labelledby="barcode-modal-title" role="dialog"
        aria-modal="true">

        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500/75 transition-opacity" aria-hidden="true" onclick="hideBarcodeModal()">
        </div>

        <!-- Modal content wrapper -->
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto flex items-center justify-center p-4">
            <div
                class="relative transform overflow-hidden rounded-lg bg-white shadow-xl transition-all sm:w-full sm:max-w-lg">

                <!-- Modal body -->
                <div class="px-6 py-5">
                    <h3 class="text-lg font-semibold text-gray-900 text-center" id="barcode-modal-title">Barcode Produk
                    </h3>
                    <input type="hidden" name="barcodeModal" id="barcodeModalInput">

                    <!-- Barcode canvas in center -->
                    <div class="mt-6 flex items-center justify-center">
                        <canvas id="barcode" class="block"></canvas>
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="bg-gray-50 px-4 py-3 flex flex-col sm:flex-row-reverse gap-2">
                    <button onclick="hideBarcodeModal()"
                        class="w-full sm:w-auto px-3 py-2 bg-white text-gray-900 rounded-md ring-1 ring-gray-300 hover:bg-gray-50">
                        Tutup
                    </button>
                    <button onclick="downloadBarcode()"
                        class="w-full sm:w-auto px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500">
                        Download Barcode
                    </button>
                </div>
            </div>
        </div>
    </div>

@endpush

@push('scripts')
    <script src="{{ asset('vendor/jsbarcode/JsBarcode.all.min.js') }}"></script>
    <script>
        // Modal functionality
        const addEditModal = document.getElementById("addEditModal");
        const deleteModal = document.getElementById("deleteModal");
        const closeAddEditModal = document.getElementById("closeAddEditModal");
        const closeDeleteModal = document.getElementById("closeDeleteModal");
        const cancelDelete = document.getElementById("cancelDelete");
        const modalForm = document.getElementById("modalForm");
        const deleteForm = document.getElementById("deleteForm");

        let isEditMode = false;

        // barcode
        function showBarcodeModal(barcode) {
            JsBarcode("#barcode", barcode, {
                format: "CODE128",
                lineColor: "#000",
                width: 2,
                height: 100,
                displayValue: true
            });
            document.getElementById('barcodeModal').querySelector('#barcodeModalInput').value = barcode;
            document.getElementById('barcodeModal').classList.remove('hidden');
        }

        function downloadBarcode() {
            let barcodeValue = document.getElementById('barcodeModal').querySelector('#barcodeModalInput').value ?? '';
            if (!barcodeValue) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Barcode tidak ditemukan!',
                });
                return;
            }

            let fileName = `barcode_${barcodeValue}.png`;
            const canvas = document.getElementById('barcode');
            const link = document.createElement('a');
            link.href = canvas.toDataURL('image/png');
            link.download = fileName;
            link.click();
        }

        function hideBarcodeModal() {
            document.getElementById('barcodeModal').classList.add('hidden');
        }
        // end barcode

        // Open Add Modal
        document.getElementById("openAddModal").onclick = function() {
            isEditMode = false;
            document.getElementById("modalTitle").innerText = "Tambah Obat";
            modalForm.action = "{{ route('admin.drug.store') }}";
            document.getElementById("formMethod").value = "POST";
            document.getElementById("drugId").value = "";
            document.getElementById("productName").value = "";
            document.getElementById("productCategory").value = "";
            document.getElementById("productStock").value = "";
            document.getElementById("productPrice").value = "";
            document.getElementById("productPurchasePrice").value = "";
            document.getElementById("productModal").value = "";
            document.getElementById("productDescription").value = "";
            document.getElementById("productImage").value = "";
            document.getElementById("packagingType").value = "";
            addEditModal.style.display = "block";
        }

        // Open Edit Modal
        function openEditModal(drug) {
            console.log(drug);
            isEditMode = true;
            document.getElementById("modalTitle").innerText = "Edit Obat";
            modalForm.action = "/admin/drug/" + drug.id;
            document.getElementById("formMethod").value = "PUT";
            document.getElementById("drugId").value = drug.id;
            document.getElementById("productName").value = drug.name || "";
            document.getElementById("productCategory").value = drug.category_id || "";
            document.getElementById("productStock").value = parseInt(drug.stock) || "";
            document.getElementById("productPrice").value = parseInt(drug.price) || "";
            document.getElementById("productPurchasePrice").value = parseInt(drug.purchase_price) || "";
            document.getElementById("productModal").value = parseInt(drug.purchase_price) * parseInt(drug.stock) || "";
            document.getElementById("productDescription").value = drug.description || "";
            document.getElementById("productExpired").value = drug.expired_at ? drug.expired_at.substring(0, 10) : "";
            document.getElementById("productBarcode").value = drug.barcode || "";
            document.getElementById("packagingType").value = drug.packaging_types || "";
            document.getElementById("productImage").value = ""; // clear file input for edit
            addEditModal.style.display = "block";
        }

        // Open Delete Modal
        function openDeleteModal(id, name) {
            document.getElementById("deleteMessage").innerText = `Apakah Anda yakin ingin menghapus produk "${name}"?`;
            deleteForm.action = "/admin/drug/" + id;
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

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            if (event.target == addEditModal) {
                addEditModal.style.display = "none";
            }
            if (event.target == deleteModal) {
                deleteModal.style.display = "none";
            }
        }

        document.getElementById("productBarcode").addEventListener("keydown", function(e) {
            if (e.key === "Enter") {
                e.preventDefault();
            }
        });
    </script>
@endpush
