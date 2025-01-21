<?php

namespace App\Http\Controllers;

use App\Models\SsoProvider;
use App\Http\Requests\StoreSsoProviderRequest;
use App\Http\Requests\UpdateSsoProviderRequest;

class SsoProviderController extends Controller
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
    public function store(StoreSsoProviderRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(SsoProvider $ssoProvider)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSsoProviderRequest $request, SsoProvider $ssoProvider)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SsoProvider $ssoProvider)
    {
        //
    }
}
