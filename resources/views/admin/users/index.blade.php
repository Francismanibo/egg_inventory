@extends('layouts.admin')

@section('title', 'User Accounts')
@section('page-title', 'User Accounts')
@section('page-subtitle', 'Create and manage Admin and User access')

@section('content')
    <section class="card p-6 sm:p-7">
        <h2 class="text-xl font-bold">Create Account</h2>
        <p class="mt-1 text-sm text-slate-500">Only an administrator can create or manage system accounts.</p>

        <form method="POST" action="{{ route('admin.users.store') }}" class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @csrf

            <div>
                <label for="name" class="text-sm font-medium text-slate-600">Full name</label>
                <input id="name" class="input mt-1" name="name" value="{{ old('name') }}" required>
            </div>
            <div>
                <label for="email" class="text-sm font-medium text-slate-600">Email address</label>
                <input id="email" class="input mt-1" type="email" name="email" value="{{ old('email') }}" required>
            </div>
            <div>
                <label for="password" class="text-sm font-medium text-slate-600">Temporary password</label>
                <input id="password" class="input mt-1" type="password" name="password" minlength="12" autocomplete="new-password" required>
                <p class="mt-1 text-xs text-slate-400">At least 12 characters</p>
            </div>
            <div>
                <label for="role" class="text-sm font-medium text-slate-600">Account role</label>
                <select id="role" class="input mt-1" name="role" required>
                    <option value="user" @selected(old('role', 'user') === 'user')>User</option>
                    <option value="admin" @selected(old('role') === 'admin')>Administrator</option>
                </select>
            </div>

            <div class="md:col-span-2 xl:col-span-4">
                <button type="submit" class="btn-primary">Create Account</button>
            </div>
        </form>
    </section>

    <section class="card mt-6 overflow-hidden">
        <div class="border-b border-[#d8e5f3] px-6 py-5">
            <h2 class="text-xl font-bold">Registered Accounts</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-[#f8fbff] text-slate-500">
                    <tr>
                        <th class="px-5 py-4 font-semibold">Name</th>
                        <th class="px-5 py-4 font-semibold">Email</th>
                        <th class="px-5 py-4 font-semibold">Role</th>
                        <th class="px-5 py-4 font-semibold">Status</th>
                        <th class="px-5 py-4 font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $account)
                        <tr class="border-t border-[#eef3f8]">
                            <td class="px-5 py-4 font-semibold">{{ $account->name }}</td>
                            <td class="px-5 py-4">{{ $account->email }}</td>
                            <td class="px-5 py-4">
                                {{ $account->role === 'admin' ? 'Administrator' : 'User' }}
                            </td>
                            <td class="px-5 py-4">
                                <span class="{{ $account->status === 'active' ? 'badge-done' : 'badge-pending' }}">
                                    {{ ucfirst($account->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <a href="{{ route('admin.users.edit', $account) }}" class="btn-secondary inline-flex">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-slate-500">No accounts have been created.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-5">{{ $users->links() }}</div>
    </section>
@endsection
