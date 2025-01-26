<?php

namespace App\Http\Controllers;

use App\Models\Transition;
use App\Http\Requests\StoreTransitionRequest;
use App\Http\Requests\UpdateTransitionRequest;

class TransitionController extends Controller
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
    public function store(StoreTransitionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Transition $transition)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransitionRequest $request, Transition $transition)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transition $transition)
    {
        //
    }
}
