<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => App\Http\Middleware\RoleMiddleware::class,
            'customer.auth' => App\Http\Middleware\EnsureCustomerIsAuthenticated::class,
        ]);

        $middleware->redirectGuestsTo(fn (Request $request): string => route('login'));
        $middleware->redirectUsersTo(fn (Request $request): string => route('dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // If an old logout form has an expired CSRF token, finish the sign-out cleanly
        // instead of leaving the user on Laravel's 419 Page Expired screen.
        $exceptions->render(function (TokenMismatchException $exception, Request $request) {
            if ($request->isMethod('POST') && in_array($request->path(), ['logout', 'customer/logout'], true)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with(
                    'success',
                    'Your session expired while signing out. You have been signed out; please sign in again.',
                );
            }

            return null;
        });
    })
    ->create();
