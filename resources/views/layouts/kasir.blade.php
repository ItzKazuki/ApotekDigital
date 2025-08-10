<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <title>{{ config('app.name') }} | Kasir</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <style>
        /* Custom scrollbar for horizontal scroll */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Elegant card background */
        .elegant-bg {
            background-color: #f9fafb;
            /* Tailwind gray-50 */
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1),
                0 2px 4px -2px rgb(0 0 0 / 0.1);
        }

        /* Subtle text shadow for headings */
        .text-shadow {
            text-shadow: 0 1px 2px rgb(0 0 0 / 0.1);
        }
    </style>
</head>

<body class="bg-[#f5f7f9] font-sans text-gray-900">
    <div class="flex min-h-screen">
        <!-- Left Sidebar -->
        <aside
            class="flex flex-col items-center bg-white w-16 md:w-20 lg:w-24 space-y-2 border-r border-gray-200 pt-6 h-screen fixed left-0 top-0">
            <a aria-label="Menu" href="{{ route('kasir.profile') }}"
                class="flex flex-col items-center justify-center w-20 h-20 rounded-full overflow-hidden shrink-0 mb-8">
                <img alt="Profile photo of user, circular" class="w-full h-full object-cover" height="80"
                    loading="lazy"
                    src="{{ auth()->user()->profile_image_url }}"
                    width="80" />
            </a>
            <a href="{{ route('kasir.index') }}" aria-label="Menu"
                class="flex flex-col items-center justify-center w-20 h-20 rounded-md font-bold text-xs
                    {{ request()->routeIs('kasir.index') ? 'bg-[#f9d36b] text-black' : 'bg-white text-black hover:bg-[#e0bb45]' }}">
                <i class="fas fa-th text-base mb-1"></i>
                Menu
            </a>
            <a href="{{ route('kasir.transaction') }}" aria-label="History"
                class="flex flex-col items-center justify-center w-20 h-20 rounded-md font-bold text-xs
                    {{ request()->is('kasir/dashboard/transaction*') ? 'bg-[#f9d36b] text-black' : 'bg-white text-black hover:bg-[#e0bb45]' }}">
                <i class="far fa-clock text-lg mb-1"></i>
                History
            </a>
            <a href="{{ route('kasir.report.index') }}" aria-label="History"
                class="flex flex-col items-center justify-center w-20 h-20 rounded-md font-bold text-xs
                    {{ request()->is('kasir/dashboard/report*') ? 'bg-[#f9d36b] text-black' : 'bg-white text-black hover:bg-[#e0bb45]' }}">
                <i class="far fa-solid fa-file text-lg mb-1"></i>
                Laporan
            </a>
            <a href="{{ route('kasir.member.index') }}" aria-label="History"
                class="flex flex-col items-center justify-center w-20 h-20 rounded-md font-bold text-xs
                    {{ request()->is('kasir/dashboard/member*') ? 'bg-[#f9d36b] text-black' : 'bg-white text-black hover:bg-[#e0bb45]' }}">
                <i class="far fa-solid fa-user text-lg mb-1"></i>
                Member
            </a>
            <div class="flex-1"></div>
            <form method="POST" action="{{ route('auth.logout') }}" class="w-full flex justify-center mb-4">
                @csrf
                <button type="submit" aria-label="Logout"
                    class="flex items-center justify-center gap-2 w-20 h-12 rounded-md border border-red-500 font-bold text-xs text-red-600 hover:bg-red-500 hover:text-white">
                    <i class="fas fa-sign-out-alt text-lg"></i>
                    Logout
                </button>
            </form>
        </aside>
        <!-- Main Content -->
        <main class="flex-1 px-6 py-8 ml-16 md:ml-20 lg:ml-24 overflow-y-auto h-screen">
            @yield('content')
        </main>
        @yield('keranjang')
    </div>

    @stack('modals')

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>

</html>
