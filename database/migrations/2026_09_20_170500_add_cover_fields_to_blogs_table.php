<?php

use App\Models\Blog;
use Cocur\Slugify\Slugify;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A blog post gets a slug for its own public address and a short excerpt
     * for the card that links to it. The cover photo lives in the media
     * library, like every other image in the shop.
     */
    public function up(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('title');
            $table->text('excerpt')->nullable()->after('slug');
        });

        $slugify = new Slugify;

        Blog::whereNull('slug')->get()->each(function (Blog $blog) use ($slugify) {
            $blog->update(['slug' => $slugify->slugify($blog->title).'-'.$blog->id]);
        });

        Schema::table('blogs', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn(['slug', 'excerpt']);
        });
    }
};
