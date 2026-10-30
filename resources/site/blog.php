<?php
/* ---------------------------------------------------------------------
   BLOG helpers (posts list, categories, post cards)
   --------------------------------------------------------------------- */
/** Address of the blog list page (the page with the "Blog list" design, e.g. blogs.php). */
function hc_blog_slug()
{
    static $slug = null;
    if ($slug === null) {
        $slug = (string)\App\Models\Page::query()->where('template', 'blog')->where('type', 'page')
            ->orderByDesc('is_published')->orderByRaw("slug = 'blogs.php' desc")->orderBy('id')->value('slug') ?: 'blogs.php';
    }
    return $slug;
}

function hc_posts_query($categoryId = null)
{
    return \App\Models\Page::query()->where('type', 'post')->where('is_published', true)
        ->where(function ($q) { $q->whereNull('published_at')->orWhere('published_at', '<=', now()); })
        ->when($categoryId, function ($q) use ($categoryId) { $q->where('category_id', $categoryId); });
}

function hc_posts($limit = 9, $categoryId = null, $offset = 0, $excludeId = null)
{
    return hc_posts_query($categoryId)->with('category')
        ->when($excludeId, function ($q) use ($excludeId) { $q->where('id', '!=', $excludeId); })
        ->orderByDesc('published_at')->orderByDesc('id')->skip((int)$offset)->take((int)$limit)->get()->all();
}

function hc_posts_count($categoryId = null)
{
    return (int)hc_posts_query($categoryId)->count();
}

function hc_blog_filters($activeId = null)
{
    $cats = \App\Models\Category::query()->orderBy('name')->get();
    if ($cats->count() < 1) { return ''; }
    $h = '<div class="filters blog-filters" role="navigation" aria-label="Categories"><a class="' . (!$activeId ? 'on' : '') . '" href="' . hc_e(hc_url(hc_blog_slug())) . '">All</a>';
    foreach ($cats as $c) {
        $h .= '<a class="' . ((int)$activeId === (int)$c->id ? 'on' : '') . '" href="' . hc_e(hc_url('category/' . $c->slug)) . '">' . hc_e($c->name) . '</a>';
    }
    return $h . '</div>';
}

function hc_post_cards_html($posts)
{
    $h = '<div class="posts">';
    foreach ($posts as $p) {
        $c = is_array($p->content) ? $p->content : array();
        $img = $p->featured_image ?: (isset($c['hero_image']) ? $c['hero_image'] : '');
        $text = trim((string)$p->excerpt) !== '' ? $p->excerpt : (isset($c['hero_text']) ? $c['hero_text'] : '');
        $h .= '<article class="post-card"><a class="pc-img" href="' . hc_e(hc_url($p->slug)) . '" tabindex="-1" aria-hidden="true">';
        if ($img) { $h .= '<img src="' . hc_e(hc_img($img)) . '" alt="' . hc_e($p->title) . '" loading="lazy" decoding="async" width="600" height="340">'; }
        $h .= '</a><div class="pc-body">';
        if ($p->category) { $h .= '<a class="post-cat" href="' . hc_e(hc_url('category/' . $p->category->slug)) . '">' . hc_e($p->category->name) . '</a>'; }
        $h .= '<h3><a href="' . hc_e(hc_url($p->slug)) . '">' . hc_e($p->title) . '</a></h3>';
        if ($text !== '') { $h .= '<p>' . hc_e(mb_strimwidth(strip_tags($text), 0, 150, '…')) . '</p>'; }
        $h .= '<div class="pc-foot"><time>' . hc_e(($p->published_at ?: $p->created_at)->format('d M Y')) . '</time><a href="' . hc_e(hc_url($p->slug)) . '">Read more →</a></div>';
        $h .= '</div></article>';
    }
    return $h . '</div>';
}
