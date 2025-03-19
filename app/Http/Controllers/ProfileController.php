<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\WorkExperience;
use App\Models\Education;
use App\Models\Skill;
use App\Models\CompanyOverview;

class ProfileController extends Controller
{
    // Update user basic info (Bio)
    public function updateBio(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'language' => 'nullable|string',
            'location' => 'nullable|string',
            'relationship_status' => 'nullable|string'
        ]);

        $user->update($request->all());

        return response()->json(['message' => 'Profile updated successfully', 'user' => $user]);
    }

    // Update Work Experience
    public function updateWorkExperience(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'title' => 'required|string|max:255',
            'organization' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date'
        ]);

        $workExperience = new WorkExperience($request->all());
        $user->workExperiences()->save($workExperience);

        return response()->json(['message' => 'Work experience added successfully', 'work_experience' => $workExperience]);
    }

    // Update Education
    public function updateEducation(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'school' => 'required|string|max:255',
            'degree' => 'required|string|max:255',
            'field_of_study' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date'
        ]);

        $education = new Education($request->all());
        $user->educations()->save($education);

        return response()->json(['message' => 'Education added successfully', 'education' => $education]);
    }

    // Update Skills
    public function updateSkills(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'skills' => 'required|array',
            'skills.*' => 'string'
        ]);

        foreach ($request->skills as $skill) {
            $user->skills()->create(['skill' => $skill]);
        }

        return response()->json(['message' => 'Skills updated successfully', 'skills' => $user->skills]);
    }

    // Update Company Overview
    public function updateCompanyOverview(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'description' => 'nullable|string',
            'website_link' => 'nullable|url',
            'phone_number' => 'nullable|string|max:15',
            'language' => 'nullable|string',
            'number_of_employees' => 'nullable|integer'
        ]);

        $companyOverview = $user->companyOverview()->updateOrCreate([], $request->all());

        return response()->json(['message' => 'Company overview updated successfully', 'company_overview' => $companyOverview]);
    }

    // Upload Profile Picture
    public function uploadProfilePicture(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png|max:2048'
        ]);

        $path = $request->file('profile_picture')->store('profile_pictures', 'public');

        $user->update(['profile_picture' => $path]);

        return response()->json(['message' => 'Profile picture updated successfully', 'profile_picture' => $path]);
    }

    // Upload Banner Picture
    public function uploadBannerPicture(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'banner_picture' => 'required|image|mimes:jpeg,png|max:2048'
        ]);

        $path = $request->file('banner_picture')->store('banner_pictures', 'public');

        $user->update(['banner_picture' => $path]);

        return response()->json(['message' => 'Banner picture updated successfully', 'banner_picture' => $path]);
    }

    // Retrieve user profile
    public function getProfile()
    {
        $user = Auth::user();

        return response()->json([
            'user' => [
                'id' => $user->id,
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
}
