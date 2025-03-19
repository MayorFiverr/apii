<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkExperience;
use Illuminate\Support\Facades\Auth;

class WorkExperienceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'organization' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $workExperience = Auth::user()->workExperiences()->create($request->all());

        return response()->json(['message' => 'Work experience added', 'work_experience' => $workExperience]);
    }

    public function destroy($id)
    {
        $workExperience = WorkExperience::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $workExperience->delete();

        return response()->json(['message' => 'Work experience deleted']);
    }
}

