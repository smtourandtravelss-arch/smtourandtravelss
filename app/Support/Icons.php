<?php

namespace App\Support;

/** Icons of the website design (SVG sprite in resources/site/icons.php). */
class Icons
{
    /** Small line icons (features, steps, why-us, cards). */
    public const SMALL = [
        'car' => 'Car / cab',
        'plane' => 'Airport / plane',
        'route' => 'Route / outstation',
        'train' => 'Railway station',
        'bus' => 'Bus / tempo traveller',
        'briefcase' => 'Corporate / office',
        'user' => 'Driver / person',
        'seat' => 'Seats / passengers',
        'bag' => 'Luggage',
        'loop' => 'Round trip',
        'sparkle' => 'Clean / sparkle',
        'headset' => 'Support 24x7',
        'award' => 'Award / trusted',
        'building' => 'Company / office building',
        'tick' => 'Simple tick',
        'home' => 'Home / doorstep',
        'rupee' => 'Rupee / price',
        'parts' => 'Spare parts (gear)',
        'clock' => 'Clock / quick',
        'tool' => 'Tools / repair',
        'chat' => 'Chat / booking',
        'search' => 'Search / check',
        'check' => 'Tick',
        'shield' => 'Shield / warranty',
        'phone' => 'Phone',
        'wa' => 'WhatsApp',
        'cal' => 'Calendar',
        'spin' => 'Spin / drum',
        'motor' => 'Motor',
        'wm' => 'Washing machine (small)',
        'fridge' => 'Fridge (small)',
        'snow' => 'Snow / cooling',
        'compressor' => 'Compressor',
        'gas' => 'Gas cylinder',
        'install' => 'Installation',
        'alert' => 'Alert / problem',
        'pin' => 'Location pin',
        'mail' => 'Email',
        'lock' => 'Lock / safe',
        'star' => 'Star',
        'heart' => 'Heart',
        'bolt' => 'Lightning / fast',
        'users' => 'People / team',
        'google' => 'Google',
    ];

    /** Big appliance drawings (appliance / type cards). */
    public const BIG = [
        't-front' => 'Front load washing machine',
        't-top' => 'Top load washing machine',
        't-semi' => 'Semi-automatic washing machine',
        't-auto' => 'Fully automatic washing machine',
        't-single' => 'Single door fridge',
        't-double' => 'Double door fridge',
        't-side' => 'Side-by-side fridge',
        't-french' => 'French / multi-door fridge',
        'ac' => 'Air conditioner',
        'tv' => 'Television',
        'micro' => 'Microwave',
    ];

    public static function small(): array
    {
        return self::SMALL;
    }

    public static function all(): array
    {
        return self::BIG + self::SMALL;
    }

    public static function exists(string $name): bool
    {
        return isset(self::SMALL[$name]) || isset(self::BIG[$name])
            || in_array($name, ['menu', 'left', 'right', 'facebook', 'instagram', 'youtube', 'linkedin', 'x', 'arrow', 'star-o', 'close'], true);
    }
}
