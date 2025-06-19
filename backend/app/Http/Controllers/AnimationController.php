<?php

namespace App\Http\Controllers;

use App\Models\Animation;
use Illuminate\Http\Request;

class AnimationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $animations = Animation::withTrashed()->latest()->paginate(10);

        return $animations;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Animation $animation)
    {
        return $animation;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Animation $animation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Animation $animation)
    {
        $animation->delete();

        return response([
            'message' => 'Animation deleted successfully',
        ]);
    }

    public function restore($id)
    {
        Animation::withTrashed()->find($id)->restore();

        return response([
            'message' => 'Animation restored successfully',
        ]);
    }
}
