<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Historic;
use Illuminate\Http\Request;

class HistoricController extends Controller
{
    public function index()
    {
        $historics = Historic::with("admin","employee","company")->orderBy('created_at', 'DESC')
            ->get();
        return response()->json($historics);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'type' => 'required|string',
            'realised_at' => 'required|date',
            'admin_id' => 'required|integer',
            'employee_id' => 'required|integer',
            'company_id' => 'required|integer',
            'description' => 'nullable|string'
        ]);

        $historic = Historic::create($validatedData);
        return response()->json($historic, 201);
    }

    public function show($id)
    {
        $historic = Historic::with("admin","employee","company")->find($id);

        if (!$historic) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        return response()->json($historic);
    }

    public function update(Request $request, $id)
    {
        $historic = Historic::find($id);

        if (!$historic) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $validatedData = $request->validate([
            'type' => 'string',
            'realised_at' => 'date',
            'admin_id' => 'integer',
            'employee_id' => 'integer',
            'company_id' => 'integer',
            'description' => 'nullable|string'
        ]);

        $historic->update($validatedData);
        return response()->json($historic);
    }

    public function destroy($id)
    {
        $historic = Historic::find($id);

        if (!$historic) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $historic->delete();
        return response()->json(['message' => 'Record deleted successfully']);
    }
}
