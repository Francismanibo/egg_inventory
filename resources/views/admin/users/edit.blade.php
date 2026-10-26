@extends('layouts.admin')

@section('title', 'Edit User Account')
@section('page-title', 'Edit User Account')
@section('page-subtitle', 'Update account details, permissions, or password')

@section('content')
    <section class="card max-w-3xl p-6 sm:p-7">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="text-sm font-medium text-slate-600">Full name</label>
                <input id="name" class="input mt-1" name="name" value="{{ old('name', $user->name) }}" required>
            </div>

            <div>
                <label for="email" class="text-sm font-medium text-slate-600">Email address</label>
                <input id="email" class="input mt-1 bg-slate-50" value="{{ $user->email }}" disabled>
                <p class="mt-1 text-xs text-slate-400">Email changes are not available on this form.</p>
            </div>

            <div>
                <label for="role" class="text-sm font-medium text-slate-600">Account role</label>
                <select id="role" class="input mt-1" name="role" required>
                    <option value="user" @selected(old('role', $user->role) === 'user')>User</option>
                    <option value="admin" @selected(old('role', $user->role) === 'admin')>Administrator</option>
                </select>
            </div>

            <div>
                <label for="status" class="text-sm font-medium text-slate-600">Account status</label>
                <select id="status" class="input mt-1" name="status" required>
                    <option value="active" @selected(old('status', $user->status) === 'active')>Active</option>
                    <option value="inactive" @selected(old('status', $user->status) === 'inactive')>Inactive</option>
                </select>
            </div>

            <div>
                <label for="password" class="text-sm font-medium text-slate-600">Set a new password</label>
                <input id="password" class="input mt-1" type="password" name="password" minlength="12" autocomplete="new-password" placeholder="Leave blank to keep the current password">
                <p class="mt-1 text-xs text-slate-400">If entered, use at least 12 characters.</p>
            </div>

            <div class="flex flex-wrap gap-3">
                <button type="submit" class="btn-primary">Save Changes</button>
                <a class="btn-secondary inline-flex items-center" href="{{ route('admin.users') }}">Back to Accounts</a>
            </div>
        </form>
    </section>
@endsection
