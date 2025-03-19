<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);

            // Extract name from email for username
            $baseUsername = explode('@', $request->email)[0];
            $username = $this->generateUniqueUsername($baseUsername);

            // Create user
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'username' => $username, // Auto-generated username
            ]);

            // Generate token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'User registered successfully.',
                'token' => $token,
                'user' => $user,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong, please try again later.'], 500);
        }
    }

    // Function to generate a unique username
    private function generateUniqueUsername($baseUsername)
    {
        $username = strtolower($baseUsername);
        $count = 1;

        while (User::where('username', $username)->exists()) {
            $username = strtolower($baseUsername) . sprintf('%02d', $count);
            $count++;
        }

        return $username;
    }

    // Login a user
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid login credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name, 
                'description' => $user->description,
                'date_of_birth' => $user->date_of_birth,
                'language' => $user->language,
                'location' => $user->location,
                'relationship_status' => $user->relationship_status,
                'profile_picture' => $user->profile_picture,
                'banner_picture' => $user->banner_picture,
                'work_experience' => $user->workExperiences,
                'education' => $user->educations,
                'skills' => $user->skills,
                'company_overview' => $user->companyOverview
            ]
        ]);
    }

    // Logout a user
    public function logout(Request $request)
    {
        $user = $request->user(); // Get the authenticated user

        if ($user) {
            $user->tokens()->delete(); // Delete all tokens
            return response()->json(['message' => 'Logged out successfully'], 200);
        }

        return response()->json(['message' => 'User not authenticated'], 401);
    }
}
