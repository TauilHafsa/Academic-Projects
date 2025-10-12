<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

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
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            // Check for intended role
            $intendedRole = $request->input('intended_role', null);

            // Authenticate the user
            $request->authenticate();

            // Check if user's role matches intended role
            $user = Auth::user();
            if ($intendedRole && $user->role !== $intendedRole) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                throw ValidationException::withMessages([
                    'email' => "The account exists but as a {$user->role} account, not a {$intendedRole} account.",
                ]);
            }

            $request->session()->regenerate();

            // Redirect based on user role
            switch ($user->role) {
                case 'admin':
                    return redirect()->intended(route('admin.dashboard'));
                case 'partner':
                    return redirect()->intended(route('partner.dashboard'));
                case 'agent':
                    return redirect()->intended(route('agent.dashboard'));
                default:
                    return redirect()->intended(route('client.dashboard'));
            }
        } catch (ValidationException $e) {
            throw $e;
        }
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
