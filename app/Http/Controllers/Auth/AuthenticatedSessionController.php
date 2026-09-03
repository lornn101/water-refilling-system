<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
{
    $request->authenticate();

    $user = Auth::user();

    // ✅ ONLY check status if the user is a CUSTOMER
    if ($user->role === 'customer') {
        if ($user->status === 'pending') {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Your account is pending approval. Please wait for the cashier to approve your account.',
            ]);
        }

        if ($user->status === 'rejected') {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Your account has been rejected. Please contact support for more information.',
            ]);
        }
    }

    // ✅ Cashiers and Riders are automatically approved – no status check needed

    $request->session()->regenerate();

    return redirect()->intended(route('dashboard', absolute: false));
}

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
