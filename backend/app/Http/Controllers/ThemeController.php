<?php

namespace App\Http\Controllers;

use App\Models\Theme;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $themes = Theme::withTrashed()->paginate(10);

        return $themes;
    }

    /**
     * Display a listing of the resource without trashed items.
     */
    public function indexWithoutTrashed()
    {
        $themes = Theme::latest()->paginate(10);

        return $themes;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $fields['created_by'] = $request->user()->id;

        $theme = Theme::create($fields);

        return response([
            'message' => 'Theme created successfully',
            'theme' => $theme,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Theme $theme)
    {
        return $theme;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Theme $theme)
    {
        $fields = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $theme->update($fields);

        return response([
            'message' => 'Theme updated successfully',
            'theme' => $theme,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Theme $theme)
    {
        $theme->delete();

        return response([
            'message' => 'Theme deleted successfully',
        ], 200);
    }

    /**
     * Restore the specified resource from soft delete.
     */
    public function restore($id)
    {
        $theme = Theme::withTrashed()->findOrFail($id);
        $theme->restore();
        return response([
            'message' => 'Theme restored successfully',
            'theme' => $theme,
        ], 200);
    }
}
