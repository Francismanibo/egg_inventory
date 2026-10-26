<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::query()->orderBy('name')->paginate(12),
        ]);
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email', 'unique:customer_accounts,email'],
            'password' => ['required', 'string', 'min:12'],
            'role' => ['required', 'in:admin,user'],
        ]);

        User::create($data + ['status' => 'active']);

        return back()->with('success', 'User account created successfully.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'role' => ['required', 'in:admin,user'],
            'status' => ['required', 'in:active,inactive'],
            'password' => ['nullable', 'string', 'min:12'],
        ]);

        $removingActiveAdmin = $user->role === 'admin'
            && $user->status === 'active'
            && ($data['role'] !== 'admin' || $data['status'] !== 'active');

        if (
            $removingActiveAdmin
            && User::query()->where('role', 'admin')->where('status', 'active')->count() <= 1
        ) {
            return back()->withErrors([
                'role' => 'The system must have at least one active administrator.',
            ])->withInput();
        }

        $user->name = $data['name'];
        $user->role = $data['role'];
        $user->status = $data['status'];

        if (! empty($data['password'])) {
            // The User model's hashed cast securely hashes this value on save.
            $user->password = $data['password'];
        }

        $user->save();

        return redirect()->route('admin.users.edit', $user)
            ->with('success', 'User account updated successfully.');
    }
}
