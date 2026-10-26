@extends('layouts.app')

@section('title', 'Create Customer Account')

@section('body')
    <div class="flex min-h-screen items-center justify-center bg-[#f6faff] p-6">
        <div class="card w-full max-w-2xl p-8 sm:p-10">
            <div class="text-center">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#2878d4]">Customer Registration</p>
                <h1 class="mt-3 text-3xl font-bold text-[#163765]">Create your account</h1>
                <p class="mt-3 text-slate-500">Your account keeps your order history together and private.</p>
            </div>

            @if ($errors->any())
                <div class="mt-6 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800" role="alert">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('customer.register.submit') }}" class="mt-8 space-y-5">
                @csrf
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="name" class="text-sm font-medium text-slate-600">Full name</label>
                        <input id="name" class="input mt-1" type="text" name="name" value="{{ old('name') }}" autocomplete="name" required maxlength="100">
                    </div>
                    <div>
                        <label for="contact" class="text-sm font-medium text-slate-600">Contact number</label>
                        <input id="contact" class="input mt-1" type="text" name="contact" value="{{ old('contact') }}" autocomplete="tel" required maxlength="30">
                    </div>
                </div>
                <div>
                    <label for="email" class="text-sm font-medium text-slate-600">Email address</label>
                    <input id="email" class="input mt-1" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required maxlength="150">
                </div>
                <div>
                    <label for="address" class="text-sm font-medium text-slate-600">Delivery address <span class="font-normal text-slate-400">(optional)</span></label>
                    <textarea id="address" class="input mt-1" name="address" rows="2" autocomplete="street-address" maxlength="255">{{ old('address') }}</textarea>
                </div>
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="password" class="text-sm font-medium text-slate-600">Password</label>
                        <input id="password" class="input mt-1" type="password" name="password" autocomplete="new-password" required minlength="8">
                    </div>
                    <div>
                        <label for="password_confirmation" class="text-sm font-medium text-slate-600">Confirm password</label>
                        <input id="password_confirmation" class="input mt-1" type="password" name="password_confirmation" autocomplete="new-password" required minlength="8">
                    </div>
                </div>
                <button type="submit" class="btn-primary w-full">Create account and continue</button>
            </form>

            <div class="mt-7 border-t border-[#d8e5f3] pt-6 text-center text-sm">
                <p class="text-slate-600">Already registered? <a href="{{ route('customer.login') }}" class="font-semibold text-[#2878d4]">Sign in</a></p>
                <a href="{{ route('home') }}" class="mt-3 block text-slate-500 hover:text-[#2878d4]">Back to customer home</a>
            </div>
        </div>
    </div>
@endsection
