<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class CustomerAuthController extends Controller
{
    public function showLogin(Request $request): View|RedirectResponse
    {
        if ($this->authenticatedAccount($request)) {
            return redirect()->intended(route('customer.order'));
        }

        return redirect()->route('login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:150'],
            'password' => ['required', 'string'],
        ]);

        $account = CustomerAccount::query()
            ->where('email', mb_strtolower(trim($credentials['email'])))
            ->first();

        if (! $account || ! Hash::check($credentials['password'], $account->password)) {
            return back()
                ->withErrors(['email' => 'The email or password is incorrect.'])
                ->onlyInput('email');
        }

        Auth::logout();
        $request->session()->forget(['customer_account_id', 'customer.intended', 'url.intended']);
        $request->session()->regenerate();
        $request->session()->put('customer_account_id', $account->id);

        return redirect()->route('customer.order')
            ->with('success', 'You are now signed in to your customer account.');
    }

    public function showRegister(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        if ($this->authenticatedAccount($request)) {
            return redirect()->intended(route('customer.order'));
        }

        return view('customer.auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:customer_accounts,email', 'unique:users,email'],
            'contact' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $email = mb_strtolower(trim($data['email']));

        $account = DB::transaction(function () use ($data, $email): CustomerAccount {
            $customer = Customer::query()->create([
                'name' => trim($data['name']),
                'contact' => trim($data['contact']),
                'email' => $email,
                'address' => trim($data['address']),
            ]);

            return CustomerAccount::query()->create([
                'customer_id' => $customer->id,
                'email' => $email,
                'password' => Hash::make($data['password']),
            ]);
        });

        Auth::logout();
        $request->session()->forget(['customer_account_id', 'customer.intended', 'url.intended']);
        $request->session()->regenerate();
        $request->session()->put('customer_account_id', $account->id);

        return redirect()->route('customer.order')
            ->with('success', 'Your account was created. You can now place and track orders.');
    }

    public function logout(Request $request): RedirectResponse
    {
        // Preserve an Admin/User login if both account types share this browser session.
        $request->session()->forget('customer_account_id');
        $request->session()->forget('url.intended');
        $request->session()->regenerate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have signed out of your customer account.');
    }

    private function authenticatedAccount(Request $request): ?CustomerAccount
    {
        $accountId = $request->session()->get('customer_account_id');

        if (! $accountId) {
            return null;
        }

        $account = CustomerAccount::query()->with('customer')->find($accountId);

        if (! $account) {
            $request->session()->forget('customer_account_id');
        }

        return $account;
    }
}
