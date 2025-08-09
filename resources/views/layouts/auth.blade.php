<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <title>
        {{ config('app.name') }} | Login
    </title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&amp;display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col md:flex-row">
    <!-- Left side -->
    <div class="md:w-1/2 w-full bg-[#3B40D6] relative flex flex-col justify-center px-10 py-16 md:py-24 text-white">
        <h1 class="text-4xl md:text-7xl font-extrabold leading-tight mb-4">
            Say Hello to
            <br />
            Apotek Digital!
            <span class="inline-block">
                👋
            </span>
        </h1>
        <p class="text-base md:text-lg max-w-md leading-relaxed">
            Sistem manajemen apotek yang dirancang untuk mempermudah pengelolaan stok obat, penjualan, dan laporan
            keuangan apotek Anda.
        </p>
        <p class="absolute bottom-6 left-10 text-sm opacity-50 select-none">
            © 2025 Apotek Digital. All rights reserved.
        </p>
        <svg aria-hidden="true" class="absolute top-0 left-0 w-full h-full pointer-events-none" fill="none"
            preserveaspectratio="none" stroke="rgba(255 255 255 / 0.1)" stroke-width="1" viewbox="0 0 800 800">
            <path d="M0 800C200 600 400 400 600 200 700 100 800 0 800 0">
            </path>
            <path d="M0 800C250 600 450 400 650 200 750 100 800 0 800 0">
            </path>
            <path d="M0 800C300 600 500 400 700 200 780 120 800 0 800 0">
            </path>
            <path d="M0 800C350 600 550 400 750 200 800 150 800 0 800 0">
            </path>
            <path d="M0 800C400 600 600 400 800 200 800 180 800 0 800 0">
            </path>
        </svg>
    </div>
    @yield('content')
</body>

</html>
