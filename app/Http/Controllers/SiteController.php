<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Page;
use App\Models\Redirect;
use App\Models\Setting;
use App\Services\SiteRenderer;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    /** Admin → Redirects. Returns a redirect response, or null when the path has none. */
    protected function redirectFor(Request $request)
    {
        try {
            $r = Redirect::query()->where('is_active', true)->where('from_path', Redirect::normalize($request->path()))->first();
        } catch (\Throwable) {
            return null; // table missing before the update is run
        }
        if (! $r) {
            return null;
        }
        $r->increment('hits');
        $to = $r->to_url;
        $to = preg_match('~^(https?:)?//~i', $to) ? $to : url(ltrim($to, '/'));

        return redirect()->away($to, $r->code ?: 301);
    }

    /** Any URL that is not a page: apply a redirect or show the 404 page. */
    public function fallback(Request $request)
    {
        return $this->redirectFor($request) ?? response(SiteRenderer::renderNotFound(), 404)->header('X-Robots-Tag', 'noindex');
    }

    /** /category/{slug} — blog posts of one category. */
    public function category(Request $request, string $slug)
    {
        $cat = Category::query()->where('slug', strtolower($slug))->first();
        if (! $cat) {
            return $this->fallback($request);
        }
        $page = new Page;
        $page->forceFill([
            'title' => $cat->name,
            'slug' => 'category/'.$cat->slug,
            'template' => 'blog',
            'type' => 'page',
            'seo_title' => $cat->seo_title ?: $cat->name.' – Blog | '.Setting::get('business_name', ''),
            'seo_description' => $cat->seo_description ?: $cat->description,
            'robots_index' => true,
            'robots_follow' => true,
            'content' => ['hero_title' => $cat->name, 'hero_text' => (string) $cat->description, 'page_name' => $cat->name, 'cta_show' => true],
        ]);

        return response(SiteRenderer::render($page, false, ['blog_category_id' => $cat->id]))->header('Content-Type', 'text/html; charset=utf-8');
    }

    /**
     * Page by its address. Old website pages end in ".php" (e.g. about-us.php),
     * so "/about-us" also finds "about-us.php" (and the other way round).
     */
    protected function findPage(string $slug): ?Page
    {
        $base = preg_replace('/\.php$/i', '', $slug);
        $candidates = array_unique([$slug, $base.'.php', $base]);
        $pages = Page::query()->whereIn('slug', $candidates)->get()->keyBy('slug');
        foreach ($candidates as $c) {
            if (isset($pages[$c])) {
                return $pages[$c];
            }
        }

        // different capital letters
        return Page::query()->whereIn(\DB::raw('LOWER(slug)'), array_map('strtolower', $candidates))->first();
    }

    public function show(Request $request, ?string $slug = null)
    {
        if ($redirect = $this->redirectFor($request)) {
            return $redirect;
        }
        $slug = trim((string) $slug, '/');
        // Links of the old website: /?page=about → /about
        $old = $request->query('page');
        if ($slug === '' && is_string($old) && preg_match('/^[a-z0-9-]+$/', $old) && $old !== 'home' && Page::query()->where('slug', $old)->exists()) {
            return redirect($old, 301);
        }
        if (in_array(strtolower($slug), ['home', 'index', 'index.php', 'home.php'], true)) {
            return redirect('/', 301);
        }
        if ($slug === '') {
            $slug = 'home';
        }

        $page = $this->findPage($slug);
        if ($page && $page->slug !== $slug && $slug !== 'home') {
            // same page under another spelling (/About-Us.php, /about-us) → the one real address
            return redirect('/'.$page->slug, 301);
        }
        $canPreview = $request->has('preview') && auth()->check();

        $scheduled = $page && $page->type === 'post' && $page->published_at && $page->published_at->isFuture();
        if (! $page || ((! $page->is_published || $scheduled) && ! $canPreview)) {
            return response(SiteRenderer::renderNotFound(), 404)
                ->header('X-Robots-Tag', 'noindex');
        }

        $html = SiteRenderer::render($page);
        $res = response($html)->header('Content-Type', 'text/html; charset=utf-8');
        if (! $page->robots_index || $slug === 'thank-you' || ! $page->is_published) {
            $res->header('X-Robots-Tag', 'noindex'.($page->robots_follow ? '' : ', nofollow'));
        }

        return $res;
    }

    public function sitemap()
    {
        $base = rtrim(Setting::get('website_url') ?: url('/'), '/');
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        $pages = Page::query()->where('is_published', true)->where('robots_index', true)->where('in_sitemap', true)
            ->where(function ($q) { $q->whereNull('published_at')->orWhere('published_at', '<=', now()); })
            ->where('slug', '!=', 'thank-you')->orderByRaw("slug = 'home' desc")->orderBy('title')->get();
        foreach ($pages as $p) {
            $loc = $p->slug === 'home' ? $base.'/' : $base.'/'.$p->slug;
            if ($p->canonical_url && $p->canonical_url !== $loc) {
                continue; // canonical points elsewhere
            }
            $xml .= '  <url><loc>'.htmlspecialchars($loc, ENT_XML1).'</loc><lastmod>'.$p->updated_at?->format('Y-m-d').'</lastmod><priority>'.($p->slug === 'home' ? '1.0' : '0.8').'</priority></url>'."\n";
        }
        $xml .= '</urlset>';

        return response($xml)->header('Content-Type', 'application/xml; charset=utf-8');
    }

    public function robots()
    {
        $base = rtrim(Setting::get('website_url') ?: url('/'), '/');
        $txt = "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /send\nDisallow: /thank-you\n";
        $extra = trim((string) Setting::get('robots_extra', ''));
        if ($extra !== '') {
            $txt .= $extra."\n";
        }
        $txt .= "\nSitemap: ".$base."/sitemap.xml\n";

        return response($txt)->header('Content-Type', 'text/plain; charset=utf-8');
    }
}
