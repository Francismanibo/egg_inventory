@extends('layouts.app')

@section('body')
    <div class="min-h-screen bg-[#f6faff]">
        <header class="border-b border-[#d8e5f3] bg-white">
            <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-5 py-4 sm:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3 text-[#163765]">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#e8f1ff] text-xl">🥚</span>
                    <span>
                        <span class="block text-xs font-semibold uppercase tracking-wider text-[#2878d4]">Fresh from the farm</span>
                        <span class="block text-lg font-bold">Egg Production</span>
                    </span>
                </a>

                <nav aria-label="Customer navigation" class="flex flex-wrap items-center gap-4 text-sm font-semibold sm:gap-6">
                    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-[#2878d4]' : 'text-slate-600 hover:text-[#2878d4]' }}">Home</a>
                    <a href="{{ route('customer.order') }}" class="{{ request()->routeIs('customer.order*') ? 'text-[#2878d4]' : 'text-slate-600 hover:text-[#2878d4]' }}">Order Eggs</a>
                    @if (session()->has('customer_account_id'))
                        <a href="{{ route('customer.my-orders') }}" class="{{ request()->routeIs('customer.my-orders*') ? 'text-[#2878d4]' : 'text-slate-600 hover:text-[#2878d4]' }}">My Orders</a>
                    @endif
                    <a href="{{ route('customer.track.form') }}" class="{{ request()->routeIs('customer.track*') ? 'text-[#2878d4]' : 'text-slate-600 hover:text-[#2878d4]' }}">Track Order</a>

                    @if (session()->has('customer_account_id'))
                        <form method="POST" action="{{ route('logout') }}" class="inline-flex">
                            @csrf
                            <button type="submit" class="rounded-lg border border-[#d8e5f3] px-4 py-2 text-[#163765] hover:bg-[#f8fbff]">Sign Out</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="rounded-lg border border-[#d8e5f3] px-4 py-2 text-[#163765] hover:bg-[#f8fbff]">Sign In</a>
                        <a href="{{ route('customer.register') }}" class="btn-primary inline-flex">Create Account</a>
                    @endif
                </nav>
            </div>
        </header>

        @if (session('success'))
            <div class="mx-auto mt-5 max-w-7xl rounded-lg border border-green-200 bg-green-50 px-5 py-3 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mx-auto mt-5 max-w-7xl rounded-lg border border-red-200 bg-red-50 px-5 py-3 text-red-800" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <main>@yield('content')</main>

        <footer class="mt-16 border-t border-[#d8e5f3] bg-white px-5 py-7 text-center text-sm text-slate-500">
            Egg Production &amp; Inventory Management System
        </footer>
    </div>
@endsection
