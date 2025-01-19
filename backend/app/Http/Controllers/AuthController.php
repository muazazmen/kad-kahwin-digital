<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request) {
        $fields = $request->validate([
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        $user = User::create($fields);

        return response([
            'message' => 'User registered successfully',
            'user' => $user,
        ], 201);
    }

    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => 'email or password is incorrect'
            ], 401);
        }

        $token = $user->createToken($user->email);

        return response([
            'message' => 'User logged in successfully',
            'user' => $user,
            'accessToken' => $token->plainTextToken
        ]);
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();

        return response([
            'message' => 'Logged out successfully'
        ]);
    }

    public function me(Request $request)
    {
        return $request->user();
    }
}
