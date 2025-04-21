<?php

namespace App\Http\Controllers;

use App\Models\Frame;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FrameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $frames = Frame::withTrashed()->paginate();

        return $frames;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|max:255',
            'frame' => 'required|file|mimes:png|max:10240'
        ]);

        $fields['created_by'] = Auth::user()->id;

        // Check if a new avatar is being uploaded
        if ($request->hasFile('frame')) {
            $fields['frame'] = $request->file('frame')->store('frames', 'public');
        }

        $frame = Frame::create($fields);

        return response([
            'message' => 'Frame added successfully',
            'frame' => $frame,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Frame $frame)
    {
        return $frame;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Frame $frame)
    {
        $fields = $request->validate([
            'name' => 'required|max:255',
            'frame' => 'required|file|mimes:png|max:10240'
        ]);

        $fields['updated_by'] = Auth::user()->id;

        // Check if a new frame is being uploaded
        if ($request->hasFile('frame')) {
            // Get the original filename without extension
            $originalFilename = pathinfo($frame->frame, PATHINFO_FILENAME);

            // Delete the previous frame if it exists
            if ($frame->frame && Storage::disk('public')->exists($frame->frame)) {
                Storage::disk('public')->delete($frame->frame);
            }

            // Store the new frame with the original filename
            $newFile = $request->file('frame');
            $extension = $newFile->getClientOriginalExtension();
            $filename = "{$originalFilename}.{$extension}";

            // Store with original filename in the same directory
            $path = $newFile->storeAs(
                pathinfo($frame->frame, PATHINFO_DIRNAME),
                $filename,
                'public'
            );

            $fields['frame'] = $path;
        }

        $frame->update($fields);

        return response([
            'message' => 'Frame updated successfully',
            'frame' => $frame,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Frame $frame)
    {
        $frame->delete();

        return response([
            'message' => 'Frame deleted successfully',
        ]);
    }

    public function restore($id)
    {
        Frame::withTrashed()->find($id)->restore();

        return response([
            'message' => 'Frame restored successfully',
        ]);
    }
}
