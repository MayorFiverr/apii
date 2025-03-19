<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompanyOverview;
use Illuminate\Support\Facades\Auth;

class CompanyOverviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'website_link' => 'nullable|url',
            'phone_number' => 'nullable|string|max:20',
            'language' => 'nullable|string|max:50',
            'number_of_employees' => 'nullable|integer|min:1'
        ]);

        $user = Auth::user();

        // Check if user already has a company overview (update instead of create)
        $companyOverview = $user->companyOverview()->first();

        if ($companyOverview) {
            $companyOverview->update($request->all());
            return response()->json(['message' => 'Company overview updated', 'company_overview' => $companyOverview]);
        } else {
            $companyOverview = $user->companyOverview()->create($request->all());
            return response()->json(['message' => 'Company overview created', 'company_overview' => $companyOverview]);
        }
    }
}
