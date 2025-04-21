<?php

namespace App\Http\Controllers;

use App\Models\Prayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrayerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $prayer = Prayer::withTrashed()->paginate(10);

        return $prayer;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'title' => 'required|max:255',
            'prayer' => 'required',
        ]);

        $fields['created_by'] = Auth::user()->id;

        $prayer = Prayer::create($fields);

        return response([
            'message' => 'Prayer added successfully',
            'prayer' => $prayer,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Prayer $prayer)
    {
        return $prayer;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Prayer $prayer)
    {
        $fields = $request->validate([
            'title' => 'required|max:255',
            'prayer' => 'required',
        ]);

        $fields['updated_by'] = Auth::user()->id;

        $prayer->update($fields);

        return response([
            'message' => 'Prayer updated successfully',
            'prayer' => $prayer,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prayer $prayer)
    {
        $prayer->delete();

        return response([
            'message' => 'Prayer deleted successfully',
        ]);
    }

    public function restore($id)
    {
        Prayer::withTrashed()->find($id)->restore();

        return response([
            'message' => 'Prayer restored successfully',
        ]);
    }
}
