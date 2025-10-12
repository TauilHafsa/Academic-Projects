<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PartnerUpgradeController extends Controller
{
    /**
     * Show the partner terms and conditions page.
     */
    public function showTerms(): View
    {
        // Ensure only clients can access this page
        if (Auth::user()->role !== 'client') {
            abort(403, 'Only clients can upgrade to partner status.');
        }
        
        return view('partner.terms');
    }

    /**
     * Handle the acceptance of terms and upgrade to partner.
     */
    public function acceptTerms(Request $request): RedirectResponse
    {
        // Ensure only clients can access this functionality
        if (Auth::user()->role !== 'client') {
            abort(403, 'Only clients can upgrade to partner status.');
        }

        $request->validate([
            'accept_terms' => ['required', 'accepted'],
            'phone_number' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
        ]);

        $user = Auth::user();
        
        // Update user role
        $user->role = 'partner';
        $user->save();

        // Get or create user profile
        $profile = $user->profile ?? new UserProfile(['user_id' => $user->id]);
        
        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete existing profile picture if it exists
            if ($profile->profile_picture) {
                Storage::disk('public')->delete($profile->profile_picture);
            }
            
            $profilePicturePath = $request->file('profile_picture')->store('profile_pictures', 'public');
            $profile->profile_picture = $profilePicturePath;
        }

        // Update profile fields
        $profile->phone_number = $request->phone_number;
        $profile->address = $request->address;
        $profile->bio = $request->bio;
        $profile->save();

        return redirect()->route('partner.dashboard')
            ->with('success', 'Congratulations! Your account has been upgraded to Partner status.');
    }
}