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
        $data = [
            'attribute' => $attribute,
            'options' => $options
        ];
        if($attribute->name === Attribute::COLOR) {
            $data['color_groups'] = AttributeOption::COLOR_GROUPS;
        }
        return Inertia::render('Attributes/Edit', $data);
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

        foreach ($request->options as $option) {
            //Значение атрибута для обновления
            $attributeOption = AttributeOption::find($option['id']);
            //Обновление цвета
            if($attributeOption && $attribute->name === Attribute::COLOR && $option['meta'] && $option['meta']['id']) {
                //Группировка значений через мета
                $attributeOption->update(['meta' => $option['meta']['id']]);
            }
            //Если есть картинка, устанавливаем ее значению
            if($attributeOption && $option['new_src']) {
                $attributeOption->clearMediaCollection();
                //Картинка для значения атрибута
                $attributeOption->addMediaFromBase64($option['new_src'])->toMediaCollection();
            }
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
}
