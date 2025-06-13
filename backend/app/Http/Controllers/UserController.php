<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource with trashed.
     */
    public function index()
    {
        $users = User::withTrashed()->latest()->paginate(10);

        return $users;
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Prevent non-super_admins from creating super_admin users
        if ($request->input('role') === 'super_admin' && auth()->user()->role !== 'super_admin') {
            return response()->json([
                'message' => 'Unauthorized: Only super admins can create super admin users'
            ], 403);
        }

        // Validate the request data
        $fields = $request->validate([
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'username' => 'nullable|unique:users|max:255',
            'password' => 'required|confirmed',
            'phone_no' => 'nullable|regex:/^(\+60|0)1[0-9]{1}-?\d{7,8}$/',
            'role' => 'nullable|in:admin,user,super_admin',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('ssoProviders');
        return $user;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Prevent non-super_admins from editing super_admin users
        if ($user->role === 'super_admin' && auth()->user()->role !== 'super_admin') {
            return response()->json([
                'message' => 'Unauthorized: Only super admins can edit other super admins'
            ], 403);
        }
        
        $fields = $request->validate([
            'first_name' => 'required|max:255',
            'last_name' => 'nullable|max:255',
            'username' => 'nullable|unique:users,username,' . $user->id . '|max:255',
            'password' => 'nullable',
            'role' => 'nullable|in:admin,user,super_admin',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'phone_no' => ['nullable', 'regex:/^(\+60|0)1[0-9]-?\d{7,8}$/'],
        ]);

        // Prevent non-super_admins from changing email
        if (isset($fields['email']) && $fields['email'] !== $user->email && auth()->user()->role !== 'super_admin') {
            return response()->json([
                'message' => 'Unauthorized: Only super admins can change email addresses'
            ], 403);
        }

        // Prevent role escalation (non-super_admins can't make users super_admin)
        if (isset($fields['role']) && $fields['role'] === 'super_admin' && auth()->user()->role !== 'super_admin') {
            return response()->json([
                'message' => 'Unauthorized: Only super admins can assign super admin role'
            ], 403);
        }

        if ($request->hasFile('avatar')) {
            $fields['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($fields);

        return response([
            'message' => 'User updated successfully',
            'user' => $user,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent non-super_admins from deleting super_admin users
        if ($user->role === 'super_admin' && auth()->user()->role !== 'super_admin') {
            return response()->json([
                'message' => 'Unauthorized: Only super admins can delete other super admins'
            ], 403);
        }

        $user->delete();

        return response([
            'message' => 'User deleted successfully',
        ]);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        $user = User::withTrashed()->find($id);

        // Prevent non-super_admins from restoring super_admin users
        if ($user->role === 'super_admin' && auth()->user()->role !== 'super_admin') {
            return response()->json([
                'message' => 'Unauthorized: Only super admins can restore other super admins'
            ], 403);
        }

        $user->restore();

        return response([
            'message' => 'User restored successfully',
        ]);
    }
}
