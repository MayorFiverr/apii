<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class UserProfileController extends Controller
{
    // Show user profile
    public function show()
    {
        return response()->json(Auth::user());
    }

    // Update user profile information
    public function update(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'language' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'relationship_status' => 'nullable|string|max:255',
        ]);

        $user->update($validatedData);

        return response()->json(['message' => 'Profile updated successfully', 'user' => $user]);
    }

    // Upload profile picture
    public function uploadProfilePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('profile_picture')) {
            $filename = time() . '.' . $request->profile_picture->extension();
            $request->profile_picture->move(public_path('uploads/profile_pictures'), $filename);

            $user->update(['profile_picture' => 'uploads/profile_pictures/' . $filename]);
        }

        return response()->json(['message' => 'Profile picture updated successfully', 'user' => $user]);
    }

    // Upload banner picture
    public function uploadBannerPicture(Request $request)
    {
        $request->validate([
            'banner_picture' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('banner_picture')) {
            $filename = time() . '.' . $request->banner_picture->extension();
            $request->banner_picture->move(public_path('uploads/banner_pictures'), $filename);

            $user->update(['banner_picture' => 'uploads/banner_pictures/' . $filename]);
        }

        return response()->json(['message' => 'Banner picture updated successfully', 'user' => $user]);
    }
}

