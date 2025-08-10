@extends('layouts.app')

@section('content')
    <h2 class="text-gray-700 font-semibold text-2xl mb-6">
        Detail Profile
    </h2>

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

    <form action="{{ route('admin.profile.update') }}" class="bg-white rounded-lg shadow p-6 space-y-6"
        enctype="multipart/form-data" method="POST">
        @csrf
        @method('PUT')
        <!-- Hidden untuk base64 image -->
        <input type="hidden" id="profile_img_base64" name="profile_img_base64">

        <div class="flex flex-col md:flex-row md:items-center md:space-x-6">
            <div class="flex-shrink-0 mb-4 md:mb-0" id="previewProfileContainer">
                <img id="previewProfile" alt="User profile photo" class="w-24 h-24 rounded-full object-cover"
                    src="{{ $user->profile_image_url }}" />
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1" for="inputImageProfile">
                    Edit Foto Profile
                </label>
                <input accept="image/*"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
                       file:rounded file:border-0 file:text-sm file:font-semibold
                       file:bg-gray-600 file:text-white hover:file:bg-gray-700 cursor-pointer"
                    id="inputImageProfile" type="file" />
                <p id="profileInfo" class="mt-2 text-sm text-gray-500">
                    Pilih foto untuk mengganti foto profile Anda
                </p>
            </div>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1" for="name">
                Nama
            </label>
            <input
                class="w-full border border-gray-300 rounded px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600"
                id="name" name="name" placeholder="Masukkan nama lengkap" type="text"
                value="{{ $user->name }}" />
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1" for="email">
                Email
            </label>
            <input
                class="w-full border border-gray-300 rounded px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600"
                id="email" name="email" placeholder="Masukkan email" type="email" value="{{ $user->email }}" />
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1" for="phone">
                Nomor Telepon
            </label>
            <input
                class="w-full border border-gray-300 rounded px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600"
                id="phone" name="phone" placeholder="Masukkan nomor telepon" type="tel"
                value="{{ $user->phone }}" />
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1" for="password">
                Password Baru
            </label>
            <input autocomplete="new-password"
                class="w-full border border-gray-300 rounded px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600"
                id="password" name="password" placeholder="Masukkan password baru" type="password" />
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1" for="confirmPassword">
                Konfirmasi Password Baru
            </label>
            <input autocomplete="new-password"
                class="w-full border border-gray-300 rounded px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600"
                id="confirmPassword" name="confirmPassword" placeholder="Konfirmasi password baru" type="password" />
        </div>
        <div class="pt-4">
            <button class="bg-gray-900 text-white font-semibold px-6 py-2 rounded hover:bg-gray-800" type="submit">
                Simpan Perubahan
            </button>
        </div>
    </form>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('vendor/cropperjs/cropper.css') }}" />
    <script src="{{ asset('vendor/cropperjs/cropper.js') }}"></script>
@endpush

@push('modals')
    <div id="cropImageModal" class="fixed inset-0 z-300 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500/75 transition-opacity" aria-hidden="true" onclick="hideModal()"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto flex items-center justify-center p-4">
            <div
                class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:w-full sm:max-w-lg">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-8 sm:pb-6">
                    <div class="sm:flex sm:items-start gap-4">
                        <div id="cropImageContainer">
                            <img id="cropImage" src="https://avatars0.githubusercontent.com/u/3456749" alt="">
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-5">
                    <button id="confirmCropImageBtn" type="button"
                        class="w-full sm:w-auto px-3 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-500">Crop</button>
                    <button onclick="document.getElementById('cropImageModal').classList.add('hidden')"
                        class="mt-3 sm:mt-0 w-full sm:w-auto px-3 py-2 bg-white text-gray-900 rounded-md ring-1 ring-gray-300 hover:bg-gray-50">Cancel</button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        let modalCropImage = document.getElementById('cropImageModal');
        let imageToCrop = document.getElementById('cropImage');
        let cropper;

        document.getElementById("confirmCropImageBtn").addEventListener("click", function() {
            const canvas = cropper.getCroppedCanvas({
                width: 350,
                height: 350,
            });

            canvas.toBlob(function(blob) {
                const reader = new FileReader();
                reader.readAsDataURL(blob);
                reader.onloadend = function() {
                    const base64data = reader.result;
                    document.getElementById('profile_img_base64').value = base64data;

                    // preview image
                    document.getElementById('previewProfile').src = base64data;
                    document.getElementById('profileInfo').classList.remove('text-red-600');
                    document.getElementById('profileInfo').classList.add('text-green-600');
                    document.getElementById('profileInfo').textContent =
                        'Foto Profile Berhasil Diunggah! Silakan simpan perubahan.';

                    modalCropImage.classList.add('hidden');
                };
            });
        });

        document.getElementById('inputImageProfile').addEventListener("change", e => {
            const file = e.target.files[0];
            if (!file) return;
            const url = URL.createObjectURL(file);
            imageToCrop.src = url;
            modalCropImage.classList.remove('hidden');
        });

        const observer = new MutationObserver(() => {
            if (!modalCropImage.classList.contains('hidden')) {
                cropper = new Cropper(imageToCrop, {
                    aspectRatio: 1,
                    viewMode: 3,
                });
            } else {
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
            }
        });

        observer.observe(modalCropImage, {
            attributes: true
        });
    </script>
@endpush
