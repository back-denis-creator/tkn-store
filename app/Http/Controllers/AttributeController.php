<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attributes = Attribute::all();

        return Inertia::render(
            'Attributes/Index',
            [
                'attributes' => $attributes
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render(
            'Attributes/Create'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255'
        ]);
        Attribute::create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return redirect()->route('attributes.index')->with('message', 'Attribute Created Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Attribute $attribute)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attribute $attribute)
    {
        return Inertia::render(
            'Attributes/Edit',
            [
                'attribute' => $attribute
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attribute $attribute)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255'
        ]);

        $attribute->name = $request->name;
        $attribute->description = $request->description;
        $attribute->save();

        return redirect()->route('attributes.index')->with('message', 'Attribute Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attribute $attribute)
    {
        $attribute->delete();

        return redirect()->route('attributes.index')->with('message', 'Attribute Delete Successfully');
    }
}
