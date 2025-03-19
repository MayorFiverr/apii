<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Education;
use Illuminate\Support\Facades\Auth;

class EducationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'school' => 'required|string|max:255',
            'degree' => 'required|string|max:255',
            'field_of_study' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $education = Auth::user()->educations()->create($request->all());

        return response()->json(['message' => 'Education added', 'education' => $education]);
    }

    public function destroy($id)
    {
        $education = Education::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $education->delete();

        return response()->json(['message' => 'Education deleted']);
    }
}

