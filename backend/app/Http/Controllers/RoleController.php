<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Role::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required',
        ]);

        $roles = Role::create($fields);

        return response([
            'message' => 'Role created successfully',
            'role' => $roles
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        return $role;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $fields = $request->validate([
            'name' => 'required',
        ]);

        $role->update($fields);

        return response([
            'message' => 'Role updated successfully',
            'role' => $role
        ]);
    }

    public function delete(Role $role)
    {
        $role->delete();

        return response([
            'message' => 'Role deleted successfully'
        ]);
    }

    public function restore($id)  
    {
        // Find the trashed role
        $role = Role::withTrashed()->findOrFail($id);

        // Restore it
        $role->restore();

        return response([
            'message' => 'Role restored successfully'
        ]);
    }
}
