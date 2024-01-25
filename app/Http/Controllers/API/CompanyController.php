<?php

namespace App\Http\Controllers;

// app/Http/Controllers/CompanyController.php

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        return Company::with("sector")
            ->withCount("employees")
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'email' => 'nullable|email',
            'sector_id' => 'nullable|exists:sectors,id',
            'address' => 'nullable',
            'phone_number' => 'nullable'
        ]);

        $company = Company::create($validatedData);
        return response()->json($company, 201);
    }

    public function show(Company $company)
    {
        $company= $company->load("sector");
        return $company;
    }

    public function update(Request $request, Company $company)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'email' => 'nullable|email',
            'sector_id' => 'nullable|exists:sectors,id',
            'address' => 'nullable',
            'phone_number' => 'nullable'
        ]);

        $company->update($validatedData);
        return response()->json($company, 200);
    }

    public function destroy(Company $company)
    {
        if ($company->employees()->exists()) {
            return response()->json(['message' => 'Cannot delete a company that has employees.'], 400);
        }
        $company->delete();
        return response()->json(null, 204);
    }

}

