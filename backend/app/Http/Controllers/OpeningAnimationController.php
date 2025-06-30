<?php

namespace App\Http\Controllers;

use App\Models\OpeningAnimation;
use Illuminate\Http\Request;

class OpeningAnimationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $openings = OpeningAnimation::withTrashed()->latest()->paginate(10);

        return $openings;
    }

    public function indexWithoutTrashed()
    {
        $openings = OpeningAnimation::latest()->get();

        return $openings;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string|max:255',
            'shadow' => 'nullable|string',
            'left_door' => 'required|string',
            'left_door_open' => 'required|string',
            'right_door' => 'required|string',
            'right_door_open' => 'required|string',
            'sealer_position' => 'nullable|string',
            'sealer_style' => 'nullable|string',
            'is_sealer_custom' => 'boolean',
        ]);

        $fields['created_by'] = auth()->id();

        $opening = OpeningAnimation::create($fields);

        return response([
            'message' => 'Opening Animation created successfully',
            'opening' => $opening,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(OpeningAnimation $opening)
    {
        return $opening;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OpeningAnimation $opening)
    {
        $fields = $request->validate([
            'name' => 'required|string|max:255',
            'shadow' => 'nullable|string',
            'left_door' => 'required|string',
            'left_door_open' => 'required|string',
            'right_door' => 'required|string',
            'right_door_open' => 'required|string',
            'sealer_position' => 'nullable|string',
            'sealer_style' => 'nullable|string',
            'is_sealer_custom' => 'boolean',
        ]);

        $fields['updated_by'] = auth()->id();

        $opening->update($fields);

        return response([
            'message' => 'Opening Animation updated successfully',
            'opening' => $opening,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OpeningAnimation $opening)
    {
        $opening->delete();

        return response([
            'message' => 'Opening Animation deleted successfully',
        ]);
    }

    public function restore($id)
    {
        OpeningAnimation::withTrashed()->find($id)->restore();

        return response([
            'message' => 'Opening Animation restored successfully',
        ]);
    }
}
