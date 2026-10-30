<?php

namespace App\Services;

use App\Models\Page;
use App\Models\Setting;

/**
 * Renders a website page with the original design.
 * Content comes from the `pages` table, settings from `settings`.
 */
class SiteRenderer
{
    protected static bool $loaded = false;

    public const SEO_KEYS = [
        'focus_keyword', 'seo_title', 'seo_description', 'canonical_url', 'robots_index',
        'robots_follow', 'og_title', 'og_description', 'og_image', 'schema_type', 'custom_schema',
    ];

    public static function boot(): void
    {
        if (static::$loaded) {
            return;
        }
        require_once resource_path('site/icons.php');
        require_once resource_path('site/engine.php');
        require_once resource_path('site/layout.php');
        require_once resource_path('site/parts.php');
        require_once resource_path('site/blog.php');
        require_once resource_path('site/blocks_extra.php');
        static::$loaded = true;
    }

    /** Build the content array a template reads with c(). */
    public static function contentFor(Page $page): array
    {
        $content = is_array($page->content) ? $page->content : [];
        // Builder state from Filament uses UUID keys — keep order, drop keys.
        foreach (['blocks', 'extra_blocks'] as $key) {
            if (isset($content[$key]) && is_array($content[$key])) {
                $content[$key] = array_values($content[$key]);
            }
        }
        $content['template'] = $page->template ?: 'builder';
        foreach (static::SEO_KEYS as $k) {
            $content[$k] = $page->{$k};
        }
        if (empty($content['page_name'])) {
            $content['page_name'] = $page->title;
        }
        if ($page->slug === 'thank-you') {
            $content['robots_index'] = false;
        }

        // Blog post details
        if (($page->type ?? 'page') === 'post') {
            $date = $page->published_at ?: $page->created_at;
            $content['post_id'] = $page->id;
            $content['post_date'] = $date ? $date->format('d M Y') : '';
            $content['post_date_iso'] = $date ? $date->toIso8601String() : '';
            $content['post_modified_iso'] = $page->updated_at ? $page->updated_at->toIso8601String() : '';
            $content['post_image'] = $page->featured_image ?: ($content['hero_image'] ?? '');
            $content['post_excerpt'] = $page->excerpt;
            $content['post_category_id'] = $page->category_id;
            $cat = $page->category_id ? \App\Models\Category::find($page->category_id) : null;
            $content['post_category'] = $cat?->name ?? '';
            $content['post_category_url'] = $cat ? url('category/'.$cat->slug) : '';
            if (empty($content['seo_description']) && ! empty($page->excerpt)) {
                $content['seo_description'] = $page->excerpt;
            }
        }
        $content['page_type'] = $page->type ?: 'page';

        return $content;
    }

    /**
     * @param  bool  $preview  true = admin preview / SEO check (no tracking codes)
     */
    public static function render(Page $page, bool $preview = false, array $extra = []): string
    {
        static::boot();

        $content = static::contentFor($page);

        $GLOBALS['HC'] = [
            'settings' => Setting::allSettings(),
            'content' => $content,
            'page' => $page->slug === 'home' ? 'home' : $page->slug,
            'preview' => $preview,
            'landing' => $content['template'] === 'landing',
            ...$extra,
        ];
        $GLOBALS['HC']['faq'] = hc_collect_faq($content);

        return static::renderTemplate($content['template']);
    }

    /**
     * Small standalone page showing ONE section (hover preview, or the service-card
     * style preview with unsaved settings via $settings).
     */
    public static function renderBlockPreview(string $type, array $data, ?array $settings = null, bool $interactive = false): string
    {
        static::boot();

        $GLOBALS['HC'] = [
            'settings' => $settings ?? Setting::allSettings(),
            'content' => ['template' => 'builder'],
            'page' => 'preview',
            'preview' => true,
            'faq' => [],
        ];
        $html = hc_blocks([['type' => $type, 'data' => $data]]);

        return '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
            .($interactive ? '<base href="'.hc_e(rtrim(url('/'), '/')).'/" target="_blank">' : '')
            .'<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">'
            .'<link rel="stylesheet" href="'.hc_e(hc_asset('assets/site/style.css')).'">'
            .hc_vars_style()
            .'<style>body{margin:0;'.($interactive ? '' : 'pointer-events:none;overflow:hidden').'}.rv{opacity:1!important;transform:none!important}</style></head>'
            .'<body>'.hc_sprite().$html.'</body></html>';
    }

    public static function renderNotFound(): string
    {
        static::boot();

        $GLOBALS['HC'] = [
            'settings' => Setting::allSettings(),
            'content' => [
                'template' => '404',
                'seo_title' => Setting::get('notfound_title', 'Page Not Found').' | '.Setting::get('business_name', ''),
                'page_name' => Setting::get('notfound_title', 'Page Not Found'),
                'hero_title' => Setting::get('notfound_title', 'Page Not Found'),
                'hero_text' => Setting::get('notfound_text', 'The page you are looking for does not exist.'),
                'body_text' => Setting::get('notfound_body', 'Please use the menu above, or go back to the home page.'),
                'button_text' => Setting::get('notfound_button', 'Go to Home Page'),
                'robots_index' => false,
                'robots_follow' => false,
            ],
            'page' => '404',
            'preview' => false,
            'faq' => [],
        ];

        return static::renderTemplate('404');
    }

    protected static function renderTemplate(string $template): string
    {
        $tpl = preg_replace('/[^a-z0-9-]/', '', $template);
        $file = resource_path('site/templates/'.$tpl.'.php');
        if (! is_file($file)) {
            $file = resource_path('site/templates/builder.php');
        }

        $level = ob_get_level();
        ob_start();
        try {
            (static function ($__file) {
                include $__file;
            })($file);
        } catch (\Throwable $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }
            throw $e;
        }

        return ob_get_clean();
    }
}
