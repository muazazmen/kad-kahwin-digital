<?php

namespace App\Http\Controllers;

use App\Models\Design;
use App\Http\Requests\StoreDesignRequest;
use App\Http\Requests\UpdateDesignRequest;
use App\Models\DesignImage;
use Auth;
use Illuminate\Support\Facades\Storage;

class DesignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $designs = Design::with(['images', 'theme'])
            ->paginate(10);

        return $designs;
    }

    // TODO: filter themes, color
    /**
     * Display a listing of the resource without trashed items.
     */
    public function indexWithoutTrashed()
    {
        $designs = Design::with(['images', 'theme'])
            ->withTrashed()
            ->paginate(10);

        return $designs;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDesignRequest $request)
    {
        $fields = $request->validated();
        $fields['created_by'] = Auth::id();

        // Remove bg_images from the fields so they don’t conflict with fillable fields
        unset($fields['bg_images']);

        // 1️⃣ Create the design first
        $design = Design::create($fields);

        // 2️⃣ Handle multiple bg_images
        if ($request->hasFile('bg_images')) {
            foreach ($request->file(key: 'bg_images') as $file) {
                $path = $file->store('designs', 'public');

                // Save each image to the related table
                $design->images()->create([
                    'image_path' => $path,
                ]);
            }
        }

        return response([
            'message' => 'Design added successfully',
            'design' => $design->load('images'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Design $design)
    {
        return $design;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDesignRequest $request, Design $design)
    {
        $fields = $request->validated();

        // Exclude bg_images from fillable fields to prevent mass assignment issues
        unset($fields['bg_images']);

        $fields['updated_by'] = Auth::id();
        $fields['updated_at'] = now();

        // 1️⃣ Update the design info (colors, name, etc.)
        $design->update($fields);

        // 2️⃣ Handle new uploaded images (if any)
        if ($request->hasFile('bg_images')) {
            foreach ($request->file('bg_images') as $file) {
                $path = $file->store('designs', 'public');

                $design->images()->create([
                    'image_path' => $path,
                ]);
            }
        }

        // 3️⃣ (Optional) Handle image removals if user requests it
        // Example: request includes an array of image IDs to delete
        if ($request->filled('delete_image_ids')) {
            $idsToDelete = $request->input('delete_image_ids', []);

            $images = DesignImage::whereIn('id', $idsToDelete)
                ->where('design_id', $design->id)
                ->get();

            foreach ($images as $image) {
                // delete file from storage
                Storage::disk('public')->delete($image->image_path);
                // delete record
                $image->delete();
            }
        }

        return response([
            'message' => 'Design updated successfully',
            'design' => $design->load('images'),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Design $design)
    {
        $design->delete();

        return response([
            'message' => 'design deleted successfully',
        ]);
    }

    public function restore($id)
    {
        Design::withTrashed()->find($id)->restore();

        return response([
            'message' => 'design restored successfully',
        ]);
    }
}
