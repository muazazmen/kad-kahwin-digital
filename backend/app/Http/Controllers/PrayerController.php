<?php

namespace App\Http\Controllers;

use App\Models\Prayer;
use App\Http\Requests\StorePrayerRequest;
use App\Http\Requests\UpdatePrayerRequest;

class PrayerController extends Controller
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
    public function store(StorePrayerRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Prayer $prayer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePrayerRequest $request, Prayer $prayer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prayer $prayer)
    {
        //
    }
}
