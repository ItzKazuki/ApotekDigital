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
        <div class="flex flex-col md:flex-row md:items-center md:space-x-6">
            <div class="flex-shrink-0 mb-4 md:mb-0">
                <img alt="User profile photo, round with gray background and initials CI"
                    class="w-24 h-24 rounded-full object-cover" height="96" id="profileImage"
                    src="{{ $user->profile_image_url ? $user->profile_image_url : Avatar::create($user->name)->toBase64() }}"
                    width="96" />
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1" for="profilePhoto">
                    Edit Foto Profile
                </label>
                <input accept="image/*"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-gray-600 file:text-white hover:file:bg-gray-700 cursor-pointer"
                    id="profilePhoto" name="profilePhoto" type="file" />
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

@push('scripts')
    <script>
        // Preview uploaded profile image
        const profilePhotoInput = document.getElementById('profilePhoto');
        const profileImage = document.getElementById('profileImage');

        profilePhotoInput.addEventListener('change', (e) => {
            const [file] = e.target.files;
            if (file) {
                profileImage.src = URL.createObjectURL(file);
            }
        });
    </script>
@endpush
