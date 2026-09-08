<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('default_colors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('hex', 7)->nullable(); // null only for "Мультиколор"
            $table->unsignedInteger('sort_order');
            $table->timestamps();
        });

        // Fixed reference palette, seeded here (not admin-editable) — see
        // create_attribute_option_default_color_table for how AttributeOptions
        // get tagged with these.
        $colors = [
            ['Білий', '#FFFFFF'],
            ['Молочний', '#FAF7EF'],
            ['Бежевий', '#DECBAE'],
            ['Коричневий', '#7E583A'],
            ['Теракотовий', '#BE5D41'],
            ['Червоний', '#BE2D2D'],
            ['Бордовий', '#802332'],
            ['Рожевий', '#E091A6'],
            ['Жовтий', '#EEC337'],
            ['Гірчичний', '#C39B2D'],
            ['Зелений', '#5B8752'],
            ['Оливковий', '#808041'],
            ['Смарагдовий', '#267D5B'],
            ['Блакитний', '#89C4DA'],
            ['Синій', '#3764A5'],
            ['Фіолетовий', '#785291'],
            ['Бузковий', '#AE91BE'],
            ['Сірий', '#9B9B96'],
            ['Чорний', '#191919'],
            ['Мультиколор', null],
        ];

        foreach ($colors as $index => [$name, $hex]) {
            DB::table('default_colors')->insert([
                'name' => $name,
                'hex' => $hex,
                'sort_order' => $index + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('default_colors');
    }
};
