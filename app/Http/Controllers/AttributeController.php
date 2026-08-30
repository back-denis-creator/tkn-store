<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\AttributeOption;
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
        return Inertia::render('Attributes/Create', [
            'color_groups' => AttributeOption::COLOR_GROUPS,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'options.*.new_file' => 'nullable|image|max:5120',
        ]);

        $attribute = Attribute::create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        $this->syncOptions($request, $attribute);

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
        // Загружаем коллекцию AttributeOption с нужным attribute_id
        $attributeOptions = AttributeOption::where('attribute_id', $attribute->id)->get();

        // Получаем URL первого изображения для каждого элемента коллекции
        $options = $attributeOptions->map(function ($attributeOption) {
            return [
                'id' => $attributeOption->id,
                'value' => $attributeOption->value,
                'meta' => $attributeOption->meta,
                'img_url' => $attributeOption->getMedia('default')->first()?->getUrl(),
            ];
        });

        return Inertia::render('Attributes/Edit', [
            'attribute' => $attribute,
            'options' => $options,
            // Always sent (not just for the Колір attribute) so the option editor can
            // react live if the admin renames an attribute to/from "Колір" mid-edit.
            'color_groups' => AttributeOption::COLOR_GROUPS,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attribute $attribute)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'options.*.new_file' => 'nullable|image|max:5120',
        ]);

        $attribute->name = $request->name;
        $attribute->description = $request->description;
        $attribute->save();

        $blockedValues = [];
        foreach ($request->input('deleted_option_ids', []) as $id) {
            $option = AttributeOption::find($id);
            if (!$option) {
                continue;
            }
            // A color/size option already picked on an existing product's SKU can't be
            // silently deleted — the pivot row would cascade-delete and that SKU would
            // quietly lose one of its variant dimensions.
            if ($option->skus()->exists()) {
                $blockedValues[] = $option->value;
                continue;
            }
            $option->clearMediaCollection();
            $option->delete();
        }

        $this->syncOptions($request, $attribute);

        if ($blockedValues) {
            return back()->withErrors([
                'options' => 'Не вдалося видалити (використовується в товарах): ' . implode(', ', $blockedValues),
            ]);
        }

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

    /**
     * Create, rename, and re-color/re-image the attribute's options — shared by
     * store() (brand-new attribute, every option is new) and update().
     *
     * Images arrive as ordinary uploaded files (not base64) — addMediaFromBase64() has no
     * filename to work with and used to store the swatch photo with no extension at all,
     * and base64's ~33% size inflation made it easier for a real phone photo to trip the
     * host's POST size limit and silently drop the whole request.
     */
    private function syncOptions(Request $request, Attribute $attribute): void
    {
        $options = $request->input('options', []);

        foreach ($options as $index => $option) {
            if ($option['id'] === 'new') {
                $attributeOption = $attribute->attributeOptions()->create([
                    'value' => $option['value'],
                ]);
            } else {
                $attributeOption = AttributeOption::find($option['id']);
                $attributeOption?->update(['value' => $option['value']]);
            }

            if ($attributeOption && $attribute->name === Attribute::COLOR && !empty($option['meta']['id'])) {
                $attributeOption->update(['meta' => $option['meta']['id']]);
            }

            $file = $request->file("options.$index.new_file");
            if ($attributeOption && $file) {
                $attributeOption->clearMediaCollection();
                $attributeOption->addMedia($file)->toMediaCollection();
            }
        }
    }
}
