<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetToken;
use App\Models\User;
use App\Services\InvitationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdminController extends Controller
{

    public function __construct(InvitationService $invitationService)
    {
        $this->invitationService=$invitationService;
    }

    public function listAdmins(): JsonResponse
    {
        $admins = User::where('role', 'admin')->orderBy('created_at', 'DESC')->get();
        return response()->json($admins);
    }

    public function addAdmin(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|unique:users',
            'name' => 'required'
        ]);

        $admin = User::create([
            'email' => $request->email,
            'name' => $request->name,
            'status' => "Inactive",
            'password' => Hash::make(Str::random(10)),
            'role' => 'admin',
        ]);

        return response()->json($admin, 201);
    }

    public function editAdmin(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
        ]);
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->name = $request->name;
        $user->save();
        return response()->json(['message' => 'name updated successfully', 'user' => $user]);
    }


    public function changeUserStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Inactive,Pending,Active'
        ]);
        $user = User::find($id);
        $status = $request->status;
        $return_status=$this->invitationService->changeUserStatus($status, $user->id,auth()->user()->id);
        return response()->json(['status' => $return_status]);
    }

    public function inviteUser($id)
    {
        $user = User::find($id);
        $email = $user->email;
        $token = Hash::make(Str::random(60));
        PasswordResetToken::create([
            'email' => $user->email,
            'token' => $token,
            'created_at' => now()
        ]);
        return response()->json(['token' => $token]);
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);
        $user = User::where('email', $request->email)->first();
        $token = Hash::make(Str::random(60));
        PasswordResetToken::create([
            'email' => $user->email,
            'token' => $token,
            'created_at' => now()
        ]);

        return response()->json(['token' => $token]);
    }
    public function getUserByToken(Request $request): JsonResponse
    {
        $request->validate(['token' => 'required']);

        $record = PasswordResetToken::where('token', $request->token)->first();

        if (!$record) {
            return response()->json(['message' => 'Invalid token'], 404);
        }

        $user = User::where('email', $record->email)->first();
        return response()->json(['user' => $user]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'new_password' => 'required|min:6',
        ]);

        $token = PasswordResetToken::where('token', $request->token)->first();
        if (!$token) {
            return response()->json(['message' => 'Invalid token'], 404);
        }
        $user = User::where('email', $token->email)->first();

        $user->password = Hash::make($request->new_password);
        $user->status = 'Active';
        $user->save();
        $token->delete();
        $this->historicService->saveHistoric(
            "accept_invite",
            date("Y-m-d H:i:s"),
            null,
            null,
            null,
            $user->name." a validé l’invitation"
        );
        return response()->json(['message' => 'Password has been reset and account activated']);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Admin not found'], 404);
        }

        if ($user->status === 'Inactive') {
            $user->delete();
            return response()->json(['message' => 'Admin deleted successfully'], 200);
        }

        return response()->json(['message' => 'Admin has to be inactive to be deleted'], 403);
    }

}
