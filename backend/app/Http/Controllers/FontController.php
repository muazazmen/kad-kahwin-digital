<?php

namespace App\Http\Controllers;

use App\Models\Font;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FontController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fonts = Font::withTrashed()->latest()->paginate(10);

        return $fonts;
    }

    /**
     * Display a listing of the resource without trashed items.
     */
    public function indexWithoutTrashed()
    {
        $fonts = Font::latest()->get();

        return $fonts;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|max:50',
            'font_family' => 'required|max:50',
            'font_path' => 'required|file|extensions:ttf,otf,woff,woff2|max:10240',
            'font_type' => 'required|max:50',
        ]);

        $fields['created_by'] = Auth::user()->id;

        // Check if a new avatar is being uploaded
        if ($request->hasFile('font_path')) {
            $file = $request->file('font_path');
            $extension = strtolower($file->getClientOriginalExtension());

            // Generate a unique filename with the correct extension
            $filename = 'font_' . uniqid() . '.' . $extension;

            // Store with the new filename
            $fields['font_path'] = $file->storeAs('fonts', $filename, 'public');
        }

        $font = Font::create($fields);

        return response([
            'message' => 'Font added successfully',
            'font' => $font,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Font $font)
    {
        return $font;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Font $font)
    {
        $fields = $request->validate([
            'name' => 'required|max:50',
            'font_family' => 'required|max:50',
            'font_path' => 'nullable|file|extensions:ttf,otf,woff,woff2|max:10240',
            'font_type' => 'required|max:50',
        ]);

        $fields['updated_by'] = Auth::user()->id;

        // Check if a new avatar is being uploaded
        if ($request->hasFile('font_path')) {
            // Get the original filename without extension
            $originalFilename = pathinfo($font->font_path, PATHINFO_FILENAME);

            // Delete the previous avatar if it exists
            if ($font->font_path && Storage::disk('public')->exists($font->font_path)) {
                Storage::disk('public')->delete($font->font_path);
            }

            // Store the new avatar with the original filename
            $newFile = $request->file('font_path');
            $extension = $newFile->getClientOriginalExtension();
            $filename = "{$originalFilename}.{$extension}";
            $fields['font_path'] = $newFile->storeAs('fonts', $filename, 'public');
        }

        $font->update($fields);

        return response([
            'message' => 'Font updated successfully',
            'font' => $font,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Font $font)
    {
        $font->delete();

        return response([
            'message' => 'Font deleted successfully',
        ]);
    }

    public function restore($id)
    {
        Font::withTrashed()->find($id)->restore();

        return response([
            'message' => 'Font restored successfully',
        ]);
    }
}
