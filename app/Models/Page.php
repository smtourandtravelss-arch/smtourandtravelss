<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Page extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'content' => 'array',
        'is_published' => 'boolean',
        'is_system' => 'boolean',
        'robots_index' => 'boolean',
        'robots_follow' => 'boolean',
        'in_sitemap' => 'boolean',
        'published_at' => 'datetime',
    ];

    /** Designs a normal "Page" can use. */
    public const TEMPLATES = [
        'builder' => 'Page Builder (sections)',
        'home' => 'Home Page (sections, no top banner)',
        'listing' => 'All Services (cards) design',
        'legal' => 'Long Text (Privacy / Terms) design',
        'thank-you' => 'Thank You Page design',
        'blog' => 'Blog list design',
    ];

    /** Every design incl. the ones used by Landing Pages and Blog posts (for labels). */
    public const ALL_TEMPLATES = self::TEMPLATES + [
        'landing' => 'Landing Page (own header & footer)',
        'post' => 'Blog Post',
    ];

    public const TYPES = ['page' => 'Page', 'post' => 'Blog Post', 'landing' => 'Landing Page'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function url(): string
    {
        return $this->slug === 'home' ? url('/') : url($this->slug);
    }
}
