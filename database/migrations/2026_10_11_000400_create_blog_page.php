<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** The /blog page that lists all blog posts (a normal system page — edit it in Pages). */
    public function up(): void
    {
        if (DB::table('pages')->where('slug', 'blog')->exists()) {
            return;
        }
        DB::table('pages')->insert([
            'type' => 'page',
            'title' => 'Blog',
            'slug' => 'blog',
            'template' => 'blog',
            'is_system' => true,
            'is_published' => true,
            'seo_title' => 'Blog',
            'content' => json_encode([
                'hero_show' => true,
                'hero_title' => 'Our [Blog]',
                'hero_text' => 'Tips, guides and news from our team.',
                'page_name' => 'Blog',
                'cta_show' => true,
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('pages')->where('slug', 'blog')->where('is_system', true)->delete();
    }
};
