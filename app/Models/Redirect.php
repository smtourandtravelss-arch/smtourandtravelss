<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['is_active' => 'boolean'];

    /** Makes "/old-page/", "old-page" and "/Old-Page" all the same key. */
    public static function normalize(string $path): string
    {
        $path = '/'.trim(strtolower(parse_url(trim($path), PHP_URL_PATH) ?: $path), '/');

        return $path === '' ? '/' : $path;
    }
}
