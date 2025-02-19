<?php

namespace App\Http\Controllers;

use App\Models\Music;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Log;

class MusicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $music = Music::withTrashed()->paginate();

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
            'url' => 'required|file|mimes:mp3,wav,ogg,flac,aac|max:10240',
        ]);

        $fields['created_by'] = Auth::user()->id;

        // Check if a new avatar is being uploaded
        if ($request->hasFile('url')) {
            $fields['url'] = $request->file('url')->store('musics', 'public');
        }

        $music = Music::create($fields);

        return $music;
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
            'url' => 'nullable|file|mimes:mp3,wav,ogg,flac,aac|max:10240',
        ]);

        $fields['created_by'] = Auth::user()->id;

        // Check if a new url is being uploaded
        if ($request->hasFile('url')) {
            // Delete the previous url if it exists
            if ($music->url && Storage::disk('public')->exists($music->url)) {
                Storage::disk('public')->delete($music->url);
            }

            // Store the new url
            $fields['url'] = $request->file('url')->store('musics', 'public');
        }

        $music->update($fields);

        return $music;
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
