@extends('layouts.app')

@section('title', 'Customer Sign In')

@section('body')
    <div class="flex min-h-screen items-center justify-center bg-[#f6faff] p-6">
        <div class="card w-full max-w-xl p-8 sm:p-10">
            <div class="text-center">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#2878d4]">Customer Account</p>
                <h1 class="mt-3 text-3xl font-bold text-[#163765]">Welcome back</h1>
                <p class="mt-3 text-slate-500">Sign in to place orders and view your order history.</p>
            </div>

            @if (session('success'))
                <div class="mt-6 rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-6 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('customer.login.submit') }}" class="mt-8 space-y-5">
                @csrf
                <div>
                    <label for="email" class="text-sm font-medium text-slate-600">Email address</label>
                    <input id="email" class="input mt-1" type="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
                </div>
                <div>
                    <label for="password" class="text-sm font-medium text-slate-600">Password</label>
                    <input id="password" class="input mt-1" type="password" name="password" autocomplete="current-password" required>
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" value="1" class="rounded border-slate-300">
                    Remember me
                </label>
                <button type="submit" class="btn-primary w-full">Sign in</button>
            </form>

            <div class="mt-7 space-y-3 border-t border-[#d8e5f3] pt-6 text-center text-sm">
                <p class="text-slate-600">New customer? <a href="{{ route('customer.register') }}" class="font-semibold text-[#2878d4]">Create an account</a></p>
                <a href="{{ route('home') }}" class="block text-slate-500 hover:text-[#2878d4]">Back to customer home</a>
            </div>
        </div>
    </div>
@endsection
