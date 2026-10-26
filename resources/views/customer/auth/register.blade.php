@extends('layouts.customer')

@section('title', 'Create Customer Account')

@section('content')
    <div class="mx-auto flex min-h-[70vh] max-w-7xl items-center justify-center px-5 py-12 sm:px-8">
        <section class="card w-full max-w-2xl p-7 sm:p-10">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-[#e8f1ff] text-3xl">🥚</div>
            <p class="mt-5 text-center text-sm font-bold uppercase tracking-wider text-[#2878d4]">Customer account</p>
            <h1 class="mt-2 text-center text-3xl font-bold">Create your account</h1>
            <p class="mt-3 text-center leading-6 text-slate-600">You need a customer account to place an order and track its delivery.</p>

            <form method="POST" action="{{ route('customer.register.submit') }}" class="mt-8 grid gap-5 sm:grid-cols-2">
                @csrf
                <div class="sm:col-span-2">
                    <label for="name" class="mb-1 block text-sm font-semibold">Full name</label>
                    <input id="name" class="input" name="name" value="{{ old('name') }}" autocomplete="name" maxlength="100" required autofocus>
                </div>
                <div>
                    <label for="email" class="mb-1 block text-sm font-semibold">Email address</label>
                    <input id="email" class="input" type="email" name="email" value="{{ old('email') }}" autocomplete="email" maxlength="150" required>
                </div>
                <div>
                    <label for="contact" class="mb-1 block text-sm font-semibold">Contact number</label>
                    <input id="contact" class="input" name="contact" value="{{ old('contact') }}" autocomplete="tel" maxlength="30" required>
                </div>
                <div class="sm:col-span-2">
                    <label for="address" class="mb-1 block text-sm font-semibold">Delivery address</label>
                    <textarea id="address" class="input" name="address" rows="3" autocomplete="street-address" maxlength="255" required>{{ old('address') }}</textarea>
                </div>
                <div>
                    <label for="password" class="mb-1 block text-sm font-semibold">Password</label>
                    <input id="password" class="input" type="password" name="password" autocomplete="new-password" minlength="8" required>
                    <p class="mt-1 text-xs text-slate-500">Use at least 8 characters.</p>
                </div>
                <div>
                    <label for="password_confirmation" class="mb-1 block text-sm font-semibold">Confirm password</label>
                    <input id="password_confirmation" class="input" type="password" name="password_confirmation" autocomplete="new-password" minlength="8" required>
                </div>
                <div class="sm:col-span-2">
                    <button class="btn-primary w-full" type="submit">Create Account</button>
                </div>
            </form>

            <p class="mt-6 text-center text-sm text-slate-600">
                Already registered?
                <a href="{{ route('login') }}" class="font-semibold text-[#2878d4] hover:underline">Sign in</a>
            </p>
        </section>
    </div>
@endsection
