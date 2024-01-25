<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use App\Services\InvitationService;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class EmployeeController extends Controller
{

    public function __construct(InvitationService $invitationService)
    {
        $this->invitationService = $invitationService;
    }

    public function index(Request $request)
    {

        $query = Employee::with('company', 'user');

        if ($request->has('company')) {
            $company_id = $request->input('company');
            $query->where('company_id', $company_id);
        }

        return $query->get();
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:employees',
            'company_id' => 'required|exists:companies,id'
        ]);

        $employee = Employee::create($validatedData);

        $user = User::create([
            'name' => $employee->name,
            'email' => $employee->email,
            'password' => Hash::make(Str::random(10)),
            'status' => 'Inactive',
            'role' => 'Employee'
        ]);
        $employee->user_id = $user->id;
        $employee->save();
        return response()->json($employee, 201);
    }

    public function show(Employee $employee)
    {
        $employee->load('company', 'company.sector');
        return $employee;
    }

    public function showMe()
    {
        $user = auth()->user();
        $employee = Employee::where('user_id', $user->id)->first();
        $employee->load('company', 'company.sector');
        return $employee;
    }

    public function updateMe(Request $request)
    {
        $user = auth()->user();
        $employee = Employee::where('user_id', $user->id)->first();
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'sometimes|string',
            'phone_number' => 'sometimes|string',
            'birth_at' => 'sometimes|date'
        ]);
        $employee->update($validatedData);
        return response()->json(['message' => 'Employee updated successfully', 'employee' => $employee], 200);
    }

    public function getMyCollegues(): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();
        $employee = Employee::where('user_id', $user->id)->first();
        if (!$employee->company_id) {
            return response()->json(['message' => 'This employee does not belong to any company.'], 404);
        }

        $colleagues = Employee::with('user')
            ->where('company_id', $employee->company_id)
            ->where('id', '!=', $employee->id)
            ->get();

        return response()->json($colleagues);
    }

    public function getCollegues(Employee $employee)
    {
        if (!$employee->company_id) {
            return response()->json(['message' => 'This employee does not belong to any company.'], 404);
        }

        $colleagues = Employee::with('user')
            ->where('company_id', $employee->company_id)
            ->where('id', '!=', $employee->id)
            ->get();

        return response()->json($colleagues);
    }


    // Update an employee
    public function update(Request $request, Employee $employee)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
        ]);

        $employee->update($validatedData);
        $employee->user->name = $employee->name;
        $employee->save();
        return response()->json($employee, 200);
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return response()->json(null, 204);
    }


    public function changeUserStatus(Request $request, $id)
    {

        $request->validate([
            'status' => 'required|in:Inactive,Pending,Active'
        ]);
        $user = Employee::find($id)->user;
        $status = $request->status;
        $return_status = $this->invitationService->changeUserStatus($status, $user->id, auth()->user()->id);
        return response()->json(['status' => $return_status]);

    }
}

