<?php

namespace App\Http\Controllers;

use App\Models\Effect;
use Illuminate\Http\Request;

class EffectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $effects = Effect::withTrashed()->paginate(10);

        return $effects;
    }

    /**
     * Display a listing of the resource without trashed items.
     */
    public function indexWithoutTrashed()
    {
        $effects = Effect::latest()->get();

        return $effects;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|max:255',
            'particle_config' => 'required|json',
        ]);

        $fields['created_by'] = auth()->user()->id;

        $effect = Effect::create($fields);

        return response([
            'message' => 'Effect created successfully',
            'effect' => $effect,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Effect $effect)
    {
        return $effect;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Effect $effect)
    {
        $fields = $request->validate([
            'name' => 'required|max:255',
            'particle_config' => 'required|json',
        ]);

        $fields['updated_by'] = auth()->user()->id;

        $effect->update($fields);

        return response([
            'message' => 'Effect updated successfully',
            'effect' => $effect,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Effect $effect)
    {
        $effect->delete();

        return response([
            'message' => 'Effect deleted successfully',
        ], 200);
    }

    /**
     * Restore a soft-deleted effect.
     */
    public function restore($id)
    {
        Effect::withTrashed()->findOrFail($id)->restore();

        return response([
            'message' => 'Effect restored successfully',
        ], 200);
    }
}
