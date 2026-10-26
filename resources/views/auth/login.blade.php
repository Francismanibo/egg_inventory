@extends('layouts.app')

@section('title', 'Sign In | Egg Production & Inventory')

@section('body')
    <main class="flex min-h-screen items-center justify-center bg-[#f6faff] px-4 py-10 sm:px-6">
        <section class="card w-full max-w-md p-8 sm:p-10">
            <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-[#e8f1ff] text-3xl">
                🥚
            </div>

            <p class="text-center text-xs font-semibold uppercase tracking-[0.2em] text-[#2878d4]">
                Egg Production System
            </p>
            <h1 class="mt-3 text-center text-3xl font-bold text-[#163765]">Welcome back</h1>

            @if (session('success'))
                <div class="mt-6 rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('warning'))
                <div class="mt-6 rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800">
                    {{ session('warning') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-6 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}" class="mt-8 space-y-5">
                @csrf

                <div>
                    <label for="email" class="text-sm font-medium text-slate-700">Email address</label>
                    <input
                        id="email"
                        class="input mt-2"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        autocomplete="username"
                        maxlength="150"
                        required
                        autofocus
                    >
                </div>

                <div>
                    <label for="password" class="text-sm font-medium text-slate-700">Password</label>
                    <input
                        id="password"
                        class="input mt-2"
                        type="password"
                        name="password"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input
                        class="rounded border-slate-300 text-[#2878d4] focus:ring-[#2878d4]"
                        type="checkbox"
                        name="remember"
                        value="1"
                        @checked(old('remember'))
                    >
                    Remember me
                </label>

                <button type="submit" class="btn-primary w-full justify-center py-3.5">
                    Sign In
                </button>
            </form>

            <p class="mt-7 text-center text-sm text-slate-600">
                Ordering eggs for the first time?
                <a href="{{ route('customer.register') }}" class="font-semibold text-[#2878d4] hover:underline">Create a customer account</a>
            </p>

            <a href="{{ route('home') }}" class="mt-5 block text-center text-sm font-semibold text-slate-500 hover:text-[#2878d4]">Back to Home</a>
        </section>
    </main>
@endsection
