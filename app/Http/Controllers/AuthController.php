<?php

namespace App\Http\Controllers;

use App\Models\CustomerAccount;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    /** Show the single login page used by Customers, Admins, and Users. */
    public function showLogin(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $customerAccountId = $request->session()->get('customer_account_id');

        if ($customerAccountId && CustomerAccount::query()->whereKey($customerAccountId)->exists()) {
            return redirect()->route('customer.order');
        }

        $request->session()->forget('customer_account_id');

        return view('auth.login');
    }

    /** Authenticate an Admin/User account or a Customer account from the same form. */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:150'],
            'password' => ['required', 'string'],
        ]);

        $email = mb_strtolower(trim($credentials['email']));
        $password = $credentials['password'];

        // Check the internal account table first. Only active Admin/User accounts are allowed.
        $user = User::query()->where('email', $email)->first();

        if ($user && Hash::check($password, $user->password)) {
            if ($user->status !== 'active' || ! in_array($user->role, ['admin', 'user'], true)) {
                return $this->invalidCredentials($request);
            }

            Auth::login($user, $request->boolean('remember'));
            $request->session()->forget(['customer_account_id', 'customer.intended', 'url.intended']);
            $request->session()->regenerate();

            return redirect()->route('dashboard')
                ->with('success', 'You are signed in to the system.');
        }

        // Customer accounts use a separate table, but the same login page.
        $customerAccount = CustomerAccount::query()
            ->with('customer')
            ->where('email', $email)
            ->first();

        if (
            $customerAccount
            && $customerAccount->customer
            && Hash::check($password, $customerAccount->password)
        ) {
            Auth::logout();

            $customerIntended = $request->session()->pull('customer.intended');
            $request->session()->forget(['customer_account_id', 'url.intended']);
            $request->session()->regenerate();
            $request->session()->put('customer_account_id', $customerAccount->id);

            if (is_string($customerIntended) && $this->isAllowedCustomerDestination($customerIntended, $request)) {
                return redirect()->to($customerIntended)
                    ->with('success', 'You are signed in to your customer account.');
            }

            return redirect()->route('customer.order')
                ->with('success', 'You are signed in to your customer account.');
        }

        return $this->invalidCredentials($request);
    }

    /** Log out whichever account type is signed in and invalidate its session. */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->forget(['customer_account_id', 'customer.intended', 'url.intended']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been signed out.');
    }

    private function invalidCredentials(Request $request): RedirectResponse
    {
        return back()
            ->withErrors(['email' => 'The email or password is incorrect, or the account is inactive.'])
            ->onlyInput('email');
    }

    private function isAllowedCustomerDestination(string $url, Request $request): bool
    {
        $host = parse_url($url, PHP_URL_HOST);
        $path = parse_url($url, PHP_URL_PATH) ?: '';

        if ($host !== null && strcasecmp($host, $request->getHost()) !== 0) {
            return false;
        }

        return $path === '/order'
            || str_starts_with($path, '/order/')
            || $path === '/track-order'
            || $path === '/my-orders';
    }
}
