<?php

namespace App\Http\Controllers;

use App\Models\GuestSetting;
use App\Http\Requests\StoreGuestSettingRequest;
use App\Http\Requests\UpdateGuestSettingRequest;

class GuestSettingController extends Controller
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
    public function store(StoreGuestSettingRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(GuestSetting $guestSetting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGuestSettingRequest $request, GuestSetting $guestSetting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GuestSetting $guestSetting)
    {
        //
    }
}
