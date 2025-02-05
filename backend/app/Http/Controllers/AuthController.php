<?php

namespace App\Http\Controllers;

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

        // Generate the avatar URL
        $profilePic = "https://avatar.iran.liara.run/username?username={$fields['first_name']}+{$fields['last_name']}";

        // Add the avatar URL to the $fields array
        $fields['avatar'] = $profilePic;

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
                // FIXME: This message is not seamless yet with toast messages
                'message' => 'email or password is incorrect'
            ], 422);
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

    public function update(Request $request)
    {
        $user = $request->user();

        $fields = $request->validate([
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image',
        ]);

        if ($request->hasFile('avatar')) {
            // Store the file in the public disk
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            // Generate the full URL
            $fields['avatar'] = env('APP_URL') . '/storage/' . $avatarPath;
        }

        $user->update($fields);

        return response([
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }
}
