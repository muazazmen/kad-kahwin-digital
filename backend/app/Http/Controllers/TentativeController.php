<?php

namespace App\Http\Controllers;

use App\Models\Tentative;
use App\Http\Requests\StoreTentativeRequest;
use App\Http\Requests\UpdateTentativeRequest;

class TentativeController extends Controller
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
    public function store(StoreTentativeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Tentative $tentative)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTentativeRequest $request, Tentative $tentative)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tentative $tentative)
    {
        //
    }
}
