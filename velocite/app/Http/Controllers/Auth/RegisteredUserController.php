<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'cin' => ['required', 'string', 'max:50', 'unique:'.User::class],
            'cin_front' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'cin_back' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        // Store CIN images
        $cinFrontPath = $request->file('cin_front')->store('cin', 'public');
        $cinBackPath = $request->file('cin_back')->store('cin', 'public');

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'client', // Default role is client
            'cin' => $request->cin,
            'cin_front' => $cinFrontPath,
            'cin_back' => $cinBackPath,
        ]);

        // Create user profile (without city now)
        UserProfile::create([
            'user_id' => $user->id,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('client.dashboard');
    }
}