<?php

namespace App\Http\Controllers;

use App\Models\Design;
use App\Http\Requests\StoreDesignRequest;
use App\Http\Requests\UpdateDesignRequest;
use Auth;

class DesignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $designs = Design::withTrashed()->leftJoin('themes', 'designs.theme_id', '=', 'themes.id')->paginate(10);

        return $designs;
    }

    // TODO: filter themes, color
    /**
     * Display a listing of the resource without trashed items.
     */
    public function indexWithoutTrashed()
    {
        $designs = Design::latest()->paginate(10);

        return $designs;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDesignRequest $request)
    {
        $fields = $request->validated();
        $fields['created_by'] = Auth::id();

        if ($request->hasFile('bg_image')) {
            $fields['bg_image'] = $request->file('bg_image')->store('designs', 'public');
        }

        $design = Design::create($fields);

        return response([
            'message' => 'Design added successfully',
            'design' => $design,
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

        if ($request->hasFile('bg_image')) {
            $fields['bg_image'] = $request->file('bg_image')->store('designs', 'public');
        }

        $fields['updated_by'] = Auth::id();
        $fields['updated_at'] = now(); 

        $design->update($fields);

        return response([
            'message' => 'Design updated successfully',
            'design' => $design,
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
