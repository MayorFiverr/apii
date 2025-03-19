<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Skill;
use Illuminate\Support\Facades\Auth;

class SkillController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'skills' => 'required|array',
        'skills.*' => 'string|max:255',
    ]);

    $user = Auth::user();

    // Delete existing skills before adding new ones (optional)
    $user->skills()->delete();

    $skills = [];
    foreach ($request->skills as $skill) {
        $skills[] = $user->skills()->create(['skill' => $skill]);
    }

    return response()->json(['message' => 'Skills updated', 'skills' => $skills]);
}

}

