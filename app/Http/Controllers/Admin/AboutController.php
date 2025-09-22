<?php

namespace App\Http\Controllers\Admin;

use App\Models\About;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAboutRequest;

class AboutController extends Controller
{
    public function about()
    {
        return About::first();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $about = $this->about();
        return view('admin.abouts.index', compact('about'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAboutRequest $request)
    {
        $validated = $request->validated();

        $this->about()->update($validated);

        return back()->with('success', 'About Us Updated Successfully');

    }
}