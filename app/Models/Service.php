<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    /** Same array shape the website templates expect. */
    public function toCard(): array
    {
        $price = [
            'price_prefix' => $this->price_prefix,
            'price' => $this->price,
            'old_price' => $this->old_price,
            'price_note' => $this->price_note,
        ];

        // Price mode: "request" shows the master "On Request" text, "hide" shows no price at all.
        $mode = $this->price_mode ?: 'price';
        if ($mode === 'hide') {
            $price = array_fill_keys(array_keys($price), null);
        } elseif ($mode === 'request') {
            $master = (array) Setting::get('card', []);
            $price = [
                'price_prefix' => $this->price_prefix,
                'price' => ($master['request_text'] ?? '') !== '' ? $master['request_text'] : 'On Request',
                'old_price' => null,
                'price_note' => $this->price_note ?: ($master['request_note'] ?? 'call for best price'),
            ];
        }

        return [
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'image' => $this->image,
            'image_alt' => $this->image_alt,
            'icon' => $this->icon,
            'badge' => $this->badge,
            'label' => $this->label,
            'category' => $this->category,
            'text' => $this->text,
            'features' => array_values(array_filter((array) $this->features, fn ($f) => trim((string) $f) !== '')),
            ...$price,
            'button_text' => $this->button_text,
            'usp_columns' => $this->usp_columns,
            'order' => $this->sort_order,
            'show' => $this->is_active,
        ];
    }
}
