@extends('layouts.auth')

@section('content')
    <div class="md:w-1/2 w-full bg-white flex flex-col justify-center px-10 py-16 md:py-24">
        <div class="max-w-md w-full mx-auto">
            <h2 class="text-2xl xl:text-5xl font-bold mb-10">
                Reset Password
            </h2>
            <form class="space-y-6" method="POST" action="{{ route('auth.reset-password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input
                    aria-label="Email"
                    class="w-full border-b border-gray-300 focus:border-black focus:outline-none text-sm font-semibold py-2 bg-gray-100"
                    placeholder="Email"
                    type="email"
                    name="email"
                    value="{{ request('email') }}"
                    readonly
                    required
                />
                <input
                    aria-label="Password"
                    class="w-full border-b border-gray-300 focus:border-black focus:outline-none text-sm font-semibold py-2"
                    placeholder="New Password"
                    type="password"
                    name="password"
                    required
                />
                <input
                    aria-label="Confirm Password"
                    class="w-full border-b border-gray-300 focus:border-black focus:outline-none text-sm font-semibold py-2"
                    placeholder="Confirm New Password"
                    type="password"
                    name="password_confirmation"
                    required
                />
                <button class="w-full bg-black text-white text-sm font-semibold py-3 rounded-md hover:bg-gray-900 transition" type="submit">
                    Reset Password Now
                </button>
            </form>
            <p class="mt-6 text-center text-gray-400 text-xs">
                Already know your password?
                <a class="font-semibold underline" href="{{ route('auth.login') }}">
                    Login here
                </a>
            </p>
        </div>
    </div>
@endsection
