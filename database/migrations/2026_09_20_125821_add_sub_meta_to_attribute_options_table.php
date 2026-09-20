<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * DEV: expand the fabric print taxonomy (AttributeOption::COLOR_GROUPS) from
 * a flat list of 7 groups to 9 categories, most with their own subcategories.
 * meta keeps the category id; sub_meta is the new subcategory id.
 *
 * Ids 0 (Однотон) and 1 (Мармур) mean the same thing in both lists, so they
 * need no remap. Ids 2-5 (Новий рік, Пасха, Квіти, Геометрія) move to a
 * different id in the new list and are remapped below. Id 6 (Прованс) has no
 * home in the new list — confirmed with the team that no fabric is tagged
 * with it yet, so it is dropped rather than remapped.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attribute_options', function (Blueprint $table) {
            $table->string('sub_meta')->nullable()->after('meta');
        });

        DB::table('attribute_options')->where('meta', '2')->update(['meta' => '6', 'sub_meta' => '0']); // Новий рік -> Святкові > Новорічні
        DB::table('attribute_options')->where('meta', '3')->update(['meta' => '6', 'sub_meta' => '1']); // Пасха -> Святкові > Великодній
        DB::table('attribute_options')->where('meta', '4')->update(['meta' => '3']); // Квіти -> Ботаніка (без підкатегорії)
        DB::table('attribute_options')->where('meta', '5')->update(['meta' => '2']); // Геометрія -> Геометричні (без підкатегорії)
    }

    public function down(): void
    {
        DB::table('attribute_options')->where('meta', '6')->where('sub_meta', '0')->update(['meta' => '2', 'sub_meta' => null]);
        DB::table('attribute_options')->where('meta', '6')->where('sub_meta', '1')->update(['meta' => '3', 'sub_meta' => null]);
        DB::table('attribute_options')->where('meta', '3')->update(['meta' => '4']);
        DB::table('attribute_options')->where('meta', '2')->update(['meta' => '5']);

        Schema::table('attribute_options', function (Blueprint $table) {
            $table->dropColumn('sub_meta');
        });
    }
};
