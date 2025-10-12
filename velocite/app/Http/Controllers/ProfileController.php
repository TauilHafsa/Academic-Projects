<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\UserProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // Create a profile if one doesn't exist
        if (!$user->profile) {
            UserProfile::create([
                'user_id' => $user->id,
                'city' => 'Paris', // Default city
            ]);

            // Refresh user to get the profile relation
            $user->refresh();
        }

        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        // Update profile data
        $profileData = $request->validate([
            'phone_number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'city' => ['required', 'string', 'max:100'],
            'bio' => ['nullable', 'string'],
        ]);

        $user = $request->user();

        // Create or update profile
        if ($user->profile) {
            $user->profile->update($profileData);
        } else {
            UserProfile::create(array_merge(
                $profileData,
                ['user_id' => $user->id]
            ));
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's profile picture.
     */
    public function updateProfilePicture(Request $request): RedirectResponse
    {
        $request->validate([
            'profile_picture' => ['required', 'image', 'max:2048'],
        ]);

        $user = $request->user();

        // Delete old profile picture if it exists
        if ($user->profile && $user->profile->profile_picture) {
            Storage::disk('public')->delete($user->profile->profile_picture);
        }

        // Store new profile picture
        $profilePicturePath = $request->file('profile_picture')->store('profile_pictures', 'public');

        // Update or create profile
        if ($user->profile) {
            $user->profile->update([
                'profile_picture' => $profilePicturePath
            ]);
        } else {
            UserProfile::create([
                'user_id' => $user->id,
                'profile_picture' => $profilePicturePath,
                'city' => 'Paris', // Default city
            ]);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-picture-updated');
    }

    /**
     * Update the user's CIN images.
     */
    public function updateCinImages(Request $request): RedirectResponse
    {
        $request->validate([
            'cin_front' => ['sometimes', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'cin_back' => ['sometimes', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $user = $request->user();
        $updates = [];

        // Process CIN front image if provided
        if ($request->hasFile('cin_front')) {
            // Delete old image if exists
            if ($user->cin_front) {
                Storage::disk('public')->delete($user->cin_front);
            }
            
            // Store new image
            $cinFrontPath = $request->file('cin_front')->store('cin', 'public');
            $updates['cin_front'] = $cinFrontPath;
        }

        // Process CIN back image if provided
        if ($request->hasFile('cin_back')) {
            // Delete old image if exists
            if ($user->cin_back) {
                Storage::disk('public')->delete($user->cin_back);
            }
            
            // Store new image
            $cinBackPath = $request->file('cin_back')->store('cin', 'public');
            $updates['cin_back'] = $cinBackPath;
        }

        // Update user with new image paths
        if (!empty($updates)) {
            $user->update($updates);
        }

        return Redirect::route('profile.edit')->with('status', 'cin-images-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Delete profile picture if exists
        if ($user->profile && $user->profile->profile_picture) {
            Storage::disk('public')->delete($user->profile->profile_picture);
        }

        // Delete CIN images if they exist
        if ($user->cin_front) {
            Storage::disk('public')->delete($user->cin_front);
        }
        
        if ($user->cin_back) {
            Storage::disk('public')->delete($user->cin_back);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
