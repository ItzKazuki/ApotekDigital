@extends('layouts.auth')

@section('content')
    <div class="md:w-1/2 w-full bg-white flex flex-col justify-center px-10 py-16 md:py-24">
        <div class="max-w-md w-full mx-auto">
            <h2 class="text-4xl md:text-5xl font-bold mb-10">
                Login to your account
            </h2>
            @if (session('error'))
                <div class="mb-4 flex items-center text-sm text-red-800 bg-red-100 border border-red-300 rounded px-4 py-3">
                    <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if (session('success'))
                <div
                    class="mb-4 flex items-center text-sm text-green-800 bg-green-100 border border-green-300 rounded px-4 py-3">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form class="space-y-6" action="{{ route('auth.login.post') }}" method="POST">
                @csrf
                <input aria-label="Email"
                    class="w-full border-b border-gray-300 focus:border-black focus:outline-none text-sm font-semibold py-2"
                    placeholder="Email" required type="email" name="email" />
                <input aria-label="Password"
                    class="w-full border-b border-gray-300 focus:border-black focus:outline-none text-sm text-gray-400 py-2"
                    placeholder="Password" required type="password" name="password" />
                <button
                    class="w-full bg-black text-white text-sm font-semibold py-3 rounded-md hover:bg-gray-900 transition"
                    type="submit">
                    Login Now
                </button>
            </form>
            <p class="mt-6 text-center text-gray-400 text-xs">
                Forget password
                <a class="font-semibold underline" href="{{ route('auth.reset-password.request') }}">
                    Click here
                </a>
            </p>
        </div>
    </div>
@endsection
