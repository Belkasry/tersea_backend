<?php

namespace App\Services;

use App\Mail\InvitationEmail;
use App\Models\Company;
use App\Models\Employee;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InvitationService
{
    protected $historicService;

    public function __construct(HistoricService $historicService)
    {
        $this->historicService = $historicService;
    }

    public function changeUserStatus($status, $id, $auth_id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        if ($user->status === 'Active') {
            return "Active";
        }
        $user->status = $status;
        $user->save();
        if ($user->role == "employee") {
            $companyId = Employee::where("user_id", $user->id)->first()->company_id ?? null;
            $company = Company::find($companyId) ?? null;
        }
        if ($status == "Pending") {
            $token = Hash::make(Str::random(60));
            PasswordResetToken::firstOrCreate([
                'email' => $user->email],
                [
                    'token' => $token,
                    'created_at' => now()
                ]);
            $url = "localhost:3000/password?token=" . $token;
            Mail::to($user->email)->send(new InvitationEmail($url));

            $description = "Admin " . auth()->user()->name . " a invite l'employé “" . $user->employee->name . "” à joindre la société " . $company?->name;
            $this->historicService->saveHistoric(
                "sent_invite",
                date("Y-m-d H:i:s"),
                $auth_id,
                $user->employee->id,
                $companyId,
                $description
            );
        } else if ($status == "Inactive") {
            PasswordResetToken::where("email", $user->email)->delete();
            $this->historicService->saveHistoric(
                "cancel_invite",
                date("Y-m-d H:i:s"),
                $auth_id,
                $user->employee->id,
                $companyId,
                "cancel_invite"
            );
        }

        return $user->status;
    }

}
