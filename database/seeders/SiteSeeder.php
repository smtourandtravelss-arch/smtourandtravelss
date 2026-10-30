<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\Service;
use App\Models\Setting;
use App\Services\SeoAnalyzer;
use Illuminate\Database\Seeder;

/**
 * Copies the original website (text, services, settings) into the database.
 * Safe to run again: existing records are NOT overwritten.
 */
class SiteSeeder extends Seeder
{
    public function run(): void
    {
        // Settings — only add the ones that do not exist yet
        $existing = Setting::query()->pluck('key')->all();
        $new = array_diff_key(config('site_defaults'), array_flip($existing));
        if ($new) {
            Setting::putMany($new);
        }

        $data = require __DIR__.'/data/site_content.php';

        // New install: the blog list lives at /blogs.php, so the empty "/blog" page made by the
        // migration is removed. On an installed website (home page exists) existing pages are never touched.
        if (! Page::query()->where('slug', 'home')->exists() && ! Page::query()->where('slug', 'blogs.php')->exists()) {
            Page::query()->where('slug', 'blog')->where('template', 'blog')->whereColumn('updated_at', 'created_at')->delete();
        }

        foreach ($data['pages'] as $p) {
            if (Page::query()->where('slug', $p['slug'])->exists()) {
                continue;
            }
            Page::query()->create([
                'title' => $p['title'],
                'slug' => $p['slug'],
                'template' => $p['template'],
                'content' => $p['content'],
                'is_published' => true,
                'is_system' => in_array($p['slug'], ['home', 'thank-you', 'blogs.php'], true),
                'seo_title' => $p['seo_title'] ?? null,
                'seo_description' => $p['seo_description'] ?? null,
                'focus_keyword' => $p['focus_keyword'] ?? null,
                'schema_type' => $p['schema_type'] ?? null,
                'robots_index' => $p['slug'] !== 'thank-you',
                'robots_follow' => $p['slug'] !== 'thank-you',
                'in_sitemap' => $p['slug'] !== 'thank-you',
            ]);
        }

        if (Service::query()->count() === 0) {
            foreach ($data['services'] as $i => $s) {
                Service::query()->create([
                    'title' => $s['title'],
                    'subtitle' => $s['subtitle'] ?? null,
                    'price_prefix' => $s['price_prefix'] ?? null,
                    'price_mode' => $s['price_mode'] ?? 'price',
                    'image' => $s['image'] ?? null,
                    'image_alt' => $s['image_alt'] ?? null,
                    'icon' => $s['icon'] ?? null,
                    'badge' => $s['badge'] ?? null,
                    'label' => $s['label'] ?? null,
                    'category' => $s['category'] ?? null,
                    'text' => $s['text'] ?? null,
                    'features' => $s['features'] ?? [],
                    'price' => $s['price'] ?? null,
                    'old_price' => $s['old_price'] ?? null,
                    'price_note' => $s['price_note'] ?? null,
                    'button_text' => $s['button_text'] ?? null,
                    'is_active' => (bool) ($s['show'] ?? true),
                    'sort_order' => (int) ($s['order'] ?? $i + 1),
                ]);
            }
        }

        // Sample blog posts. Newest first in the data → newest gets the latest time.
        $start = now();
        foreach (array_values($data['posts'] ?? []) as $i => $p) {
            if (Page::query()->where('slug', $p['slug'])->exists()) {
                continue;
            }
            Page::query()->create([
                'type' => 'post',
                'title' => $p['title'],
                'slug' => $p['slug'],
                'template' => 'post',
                'content' => $p['content'],
                'excerpt' => $p['excerpt'] ?: null,
                'featured_image' => $p['featured_image'] ?: null,
                'published_at' => (clone $start)->subMinutes($i),
                'is_published' => true,
                'seo_title' => $p['seo_title'] ?? null,
                'seo_description' => $p['seo_description'] ?? null,
                'focus_keyword' => $p['focus_keyword'] ?? null,
                'og_image' => $p['featured_image'] ?: null,
                'robots_index' => true,
                'robots_follow' => true,
                'in_sitemap' => true,
            ]);
        }

        // No blog posts yet: keep the empty blog list page out of Google until the first post
        if (! Page::query()->where('type', 'post')->exists()) {
            Page::query()->where('template', 'blog')->whereColumn('updated_at', 'created_at')->update(['is_published' => false, 'in_sitemap' => false]);
        }

        // First SEO scores
        foreach (Page::query()->whereNull('seo_score')->get() as $page) {
            try {
                $page->forceFill(['seo_score' => SeoAnalyzer::analyze($page)['score']])->saveQuietly();
            } catch (\Throwable) {
                // score is calculated again when the page is saved
            }
        }
    }
}
