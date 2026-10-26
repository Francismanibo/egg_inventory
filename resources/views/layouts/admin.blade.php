@extends('layouts.app')

@section('body')
    <div class="min-h-screen bg-[#f6faff]">
        <aside class="fixed bottom-6 left-6 top-6 hidden w-60 rounded-2xl border border-[#d8e5f3] bg-white p-4 shadow-sm lg:block">
            <div class="mb-7 px-3 pt-2">
                <p class="text-xs font-semibold uppercase tracking-wider text-[#2878d4]">Egg Production</p>
                <h2 class="mt-1 text-lg font-bold text-[#163765]">Inventory System</h2>
            </div>

            <nav aria-label="Main navigation" class="space-y-1">
                <a href="{{ route('dashboard') }}"
                   class="block rounded-xl px-4 py-3 text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-[#e8f1ff] text-[#2878d4]' : 'text-slate-600 hover:bg-[#f3f8ff]' }}">
                    Dashboard
                </a>
                <a href="{{ route('admin.production') }}"
                   class="block rounded-xl px-4 py-3 text-sm font-medium {{ request()->routeIs('admin.production*') ? 'bg-[#e8f1ff] text-[#2878d4]' : 'text-slate-600 hover:bg-[#f3f8ff]' }}">
                    Production
                </a>
                <a href="{{ route('admin.inventory') }}"
                   class="block rounded-xl px-4 py-3 text-sm font-medium {{ request()->routeIs('admin.inventory*') ? 'bg-[#e8f1ff] text-[#2878d4]' : 'text-slate-600 hover:bg-[#f3f8ff]' }}">
                    Inventory
                </a>
                <a href="{{ route('admin.reports') }}"
                   class="block rounded-xl px-4 py-3 text-sm font-medium {{ request()->routeIs('admin.reports') ? 'bg-[#e8f1ff] text-[#2878d4]' : 'text-slate-600 hover:bg-[#f3f8ff]' }}">
                    Reports
                </a>

                @if (auth()->user()->role === 'admin')
                    <div class="px-4 pb-1 pt-5 text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Administration
                    </div>
                    <a href="{{ route('admin.orders', 'pending') }}"
                       class="block rounded-xl px-4 py-3 text-sm font-medium {{ request()->routeIs('admin.orders*') ? 'bg-[#e8f1ff] text-[#2878d4]' : 'text-slate-600 hover:bg-[#f3f8ff]' }}">
                        Customer Orders
                    </a>
                    <a href="{{ route('admin.products') }}"
                       class="block rounded-xl px-4 py-3 text-sm font-medium {{ request()->routeIs('admin.products*') ? 'bg-[#e8f1ff] text-[#2878d4]' : 'text-slate-600 hover:bg-[#f3f8ff]' }}">
                        Egg Products
                    </a>
                    <a href="{{ route('admin.users') }}"
                       class="block rounded-xl px-4 py-3 text-sm font-medium {{ request()->routeIs('admin.users*') ? 'bg-[#e8f1ff] text-[#2878d4]' : 'text-slate-600 hover:bg-[#f3f8ff]' }}">
                        User Accounts
                    </a>
                @endif
            </nav>

            <form method="POST" action="{{ route('logout') }}" class="mt-8 border-t border-[#eef3f8] pt-5">
                @csrf
                <button type="submit" class="w-full rounded-xl border border-[#d8e5f3] px-4 py-3 text-left text-sm font-semibold text-slate-600 hover:bg-[#f8fbff]">
                    Sign Out
                </button>
            </form>
        </aside>

        <div class="lg:ml-[17rem]">
            <header class="border-b border-[#d8e5f3] bg-white px-5 py-5 sm:px-8">
                <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-[#163765]">@yield('page-title')</h1>
                        <p class="mt-1 text-sm text-slate-500">@yield('page-subtitle')</p>
                    </div>
                    <div class="flex items-center justify-between gap-3 text-sm sm:justify-end">
                        <div class="text-right">
                            <p class="font-semibold text-[#163765]">{{ auth()->user()->name }}</p>
                            <p class="text-slate-500">{{ auth()->user()->role === 'admin' ? 'Administrator' : 'User' }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="lg:hidden">
                            @csrf
                            <button type="submit" class="btn-secondary">Sign Out</button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="mx-auto max-w-[1500px] p-4 sm:p-6">
                @if (session('success'))
                    <div class="mb-5 rounded-lg border border-green-200 bg-green-50 px-5 py-3 text-sm text-green-800">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-5 py-3 text-sm text-red-800" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
@endsection
