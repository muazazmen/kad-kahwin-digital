<?php

namespace App\Http\Controllers;

use App\Models\Font;
use App\Http\Requests\StoreFontRequest;
use App\Http\Requests\UpdateFontRequest;

class FontController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFontRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Font $font)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFontRequest $request, Font $font)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Font $font)
    {
        //
    }
}
