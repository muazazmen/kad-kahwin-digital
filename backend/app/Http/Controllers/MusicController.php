<?php

namespace App\Http\Controllers;

use App\Models\Music;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MusicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $music = Music::withTrashed()->latest()->paginate(10);

        return $music;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'title' => 'required|max:255',
            'artist' => 'required|max:255',
            'album' => 'nullable|max:255',
            'genre' => 'nullable|max:255',
            'year' => 'nullable|max:255',
            'music_path' => 'required|file|mimes:mp3,wav,ogg,flac,aac|max:10240',
        ]);

        $fields['created_by'] = Auth::user()->id;

        // Check if a new avatar is being uploaded
        if ($request->hasFile('music_path')) {
            $fields['music_path'] = $request->file('music_path')->store('musics', 'public');
        }

        $music = Music::create($fields);

        return response([
            'message' => 'Music added successfully',
            'music' => $music,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Music $music)
    {
        return $music;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Music $music)
    {
        $fields = $request->validate([
            'title' => 'required|max:255',
            'artist' => 'required|max:255',
            'album' => 'nullable|max:255',
            'genre' => 'nullable|max:255',
            'year' => 'nullable|max:255',
            'music_path' => 'nullable|file|mimes:mp3,wav,ogg,flac,aac|max:10240',
        ]);

        $fields['updated_by'] = Auth::user()->id;

        // Check if a new url is being uploaded
        if ($request->hasFile('music_path')) {
            // Get the original filename without extension
            $originalFilename = pathinfo($music->music_path, PATHINFO_FILENAME);

            // Delete the previous music if it exists
            if ($music->music_path && Storage::disk('public')->exists($music->music_path)) {
                Storage::disk('public')->delete($music->music_path);
            }

            // Store the new music with the original filename
            $newFile = $request->file('music_path');
            $extension = $newFile->getClientOriginalExtension();
            $filename = "{$originalFilename}.{$extension}";

            // Store with original filename in the same directory
            $path = $newFile->storeAs(
                pathinfo($music->music_path, PATHINFO_DIRNAME),
                $filename,
                'public'
            );

            $fields['music_path'] = $path;
        }

        $music->update($fields);

        return response([
            'message' => 'Music updated successfully',
            'music' => $music,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Music $music)
    {
        $music->delete();

        return response([
            'message' => 'Music deleted successfully',
        ]);
    }

    public function restore($id)
    {
        Music::withTrashed()->find($id)->restore();

        return response([
            'message' => 'Music restored successfully',
        ]);
    }
}
