<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title> {{ config('app.name') }} | Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    @stack('styles')
    <style>
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 50;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 8px;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-white flex flex-col">
            <div class="p-4 text-2xl font-bold border-b border-gray-700 text-center">
                <img src="{{ asset('static/images/logo-apotek-v2-darkmode.png') }}" alt="Logo"
                    class="mx-auto h-30 w-auto">
            </div>

            <a href="{{ route('admin.profile') }}" class="block">
                <div class="flex items-center p-4 border-b border-gray-700 hover:bg-gray-800 cursor-pointer">
                    <img src="{{ Auth::user()->profile_image_url }}"
                        alt="Profile" class="w-12 h-12 rounded-full mr-2">
                    <div>
                        <div class="text-sm font-semibold">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-gray-300">{{ Auth::user()->role }}</div>
                    </div>
                </div>
            </a>

            <nav class="flex-1 p-4 space-y-2 text-sm font-medium">
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center px-4 py-2 rounded {{ request()->is('admin/dashboard*') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">Dashboard</a>
                <a href="{{ route('admin.kasir.index') }}"
                    class="flex items-center px-4 py-2 rounded {{ request()->is('admin/kasir*') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">Kasir</a>
                <a href="{{ route('admin.member.index') }}"
                    class="flex items-center px-4 py-2 rounded {{ request()->is('admin/member*') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">Member</a>
                <a href="{{ route('admin.category.index') }}"
                    class="flex items-center px-4 py-2 rounded {{ request()->is('admin/category*') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">Kategori</a>
                <a href="{{ route('admin.drug.index') }}"
                    class="flex items-center px-4 py-2 rounded {{ request()->is('admin/drug*') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">Obat</a>
                <a href="{{ route('admin.transaction.index') }}"
                    class="flex items-center px-4 py-2 rounded {{ request()->is('admin/transaction*') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">Transaksi</a>
                <a href="{{ route('admin.report.index') }}"
                    class="flex items-center px-4 py-2 rounded {{ request()->is('admin/report*') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">Laporan</a>
            </nav>
            <div class="mt-auto border-t border-gray-700">
                <form action="{{ route('auth.logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full text-left flex items-center px-4 py-2 text-sm text-red-400 hover:text-white hover:bg-red-600 rounded-b">
                        Logout
                    </button>
                </form>
            </div>
        </aside>
        <!-- Main content -->
        <main class="flex-1 p-6 overflow-y-auto">
            @yield('content')
        </main>
    </div>
</body>

{{-- modals --}}
@stack('modals')

{{-- scripts --}}
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@stack('scripts')

</html>
