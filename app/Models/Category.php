<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $guarded = ['id'];

    public function posts(): HasMany
    {
        return $this->hasMany(Page::class)->where('type', 'post');
    }

    public function url(): string
    {
        return url('category/'.$this->slug);
    }
}
