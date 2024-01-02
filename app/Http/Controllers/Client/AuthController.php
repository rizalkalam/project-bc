<?php

namespace App\Http\Controllers\Client;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if (! Auth::attempt($request->only('name', 'password'))) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $user = User::where('name', $request->name)->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        $userA = Auth::user();
        $roleName = $userA->getRoleNames()->first();

        if ($roleName == 'master') {
            return response()->json([
                'message' => 'Login success',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'role'=>$roleName,
            ]);
        } elseif ($roleName == 'ppk') {
            return response()->json([
                'message' => 'Login success',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'role'=>$roleName,
            ]);
        }
        return response()->json([
            'message' => 'Access denied',
        ], 403);

    }

    public function logout()
    {
        Auth::user()->tokens()->delete();
        return response()->json([
            'message' => 'logout success'
        ]);
    }

    public function wrongtoken()
    {
        return response()->json([
            "erorr" => "Unauthorized"
        ],401);
    }
}
