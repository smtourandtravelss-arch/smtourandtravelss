<?php
/* =====================================================================
   SHARED LAYOUT — head, top bar, header, inner-page banner, CTA banner,
   footer, floating buttons, mobile bar, booking popup.
   Text comes from Admin → Site Settings.
   ===================================================================== */

/** Appliance of the current page (Admin → Pages → "Appliance of this page"). */
function hc_page_app()
{
    return trim((string)c('appliance', ''));
}

function hc_page_title()
{
    $name = s('business_name', 'Website');
    if (c('seo_title', '') !== '') { return c('seo_title'); }
    $h = hc_plain_title(c('page_name', '') !== '' ? c('page_name') : c('hero_title', ''));
    return $h !== '' ? $h . ' | ' . $name : $name;
}

/** First big banner photo of the page (for preload, share image and popup). */
function hc_page_hero_image()
{
    if (c('hero_image', '') !== '') { return c('hero_image'); }
    foreach (hc_list(c('blocks', array())) as $b) {
        if (is_array($b) && ($b['type'] ?? '') === 'hero_form' && !empty($b['data']['image']) && (!isset($b['data']['show']) || on($b['data']['show']))) {
            return hc_fill($b['data']['image']);
        }
    }
    return '';
}

/** :root colour / size variables from Site Settings. */
function hc_vars()
{
    $col = s('colors', array());
    $btn = s('buttons', array());
    $ty = s('thankyou', array());
    $f = s('floating', array());
    $v = function ($arr, $k, $d) {
        $x = is_array($arr) && isset($arr[$k]) ? trim((string)$arr[$k]) : '';
        return $x !== '' && !preg_match('/[;{}<>"\']/', $x) ? $x : $d;
    };
    $num = function ($k, $d) use ($f) { $x = is_array($f) && isset($f[$k]) ? (int)preg_replace('/[^0-9]/', '', (string)$f[$k]) : 0; return ($x > 0 ? $x : $d) . 'px'; };
    $speed = array('slow' => '3s', 'normal' => '2s', 'fast' => '1.2s');
    $sp = is_array($f) && isset($f['animation_speed'], $speed[$f['animation_speed']]) ? $speed[$f['animation_speed']] : '2s';

    $css = '--blue:' . $v($col, 'primary', '#1D74C9') . ';--navy:' . $v($col, 'dark', '#0F1B33') . ';--cta:' . $v($col, 'accent', '#FF7A1A') . ';'
        . '--soft:' . $v($col, 'light', '#F3F7FC') . ';'
        . '--btn-book-bg:' . $v($btn, 'main_bg', '#FF7A1A') . ';--btn-book-color:' . $v($btn, 'main_color', '#FFFFFF') . ';'
        . '--btn-call-bg:' . $v($btn, 'call_bg', '#0F1B33') . ';--btn-call-color:' . $v($btn, 'call_color', '#FFFFFF') . ';'
        . '--btn-wa-bg:' . $v($btn, 'whatsapp_bg', '#25D366') . ';--btn-wa-color:' . $v($btn, 'whatsapp_color', '#FFFFFF') . ';'
        . '--btn-submit-bg:' . $v($btn, 'submit_bg', '#FF7A1A') . ';--btn-submit-color:' . $v($btn, 'submit_color', '#FFFFFF') . ';'
        . '--btn-popup-bg:' . $v($btn, 'popup_bg', '#FF7A1A') . ';--btn-popup-color:' . $v($btn, 'popup_color', '#FFFFFF') . ';'
        . '--btn-ty-bg:' . $v($ty, 'call_bg', '#FF7A1A') . ';--btn-ty-color:' . $v($ty, 'call_color', '#FFFFFF') . ';'
        . '--fb-size:' . $num('size', 58) . ';--fb-msize:' . $num('mobile_size', 52) . ';--fb-icon:' . $num('icon_size', 27) . ';'
        . '--fb-gap:' . $num('gap', 12) . ';--fb-x:' . $num('position_x', 20) . ';--fb-y:' . $num('position_y', 20) . ';'
        . '--fb-call:' . $v($f, 'call_color', '#1D74C9') . ';--fb-wa:' . $v($f, 'whatsapp_color', '#25D366') . ';--fb-speed:' . $sp . ';'
        . hc_card_vars();

    return '<style>:root{' . $css . '}</style>';
}

/** Service-card master style → CSS variables (only filled-in values). */
function hc_card_vars()
{
    $cd = s('card', array());
    if (!is_array($cd)) { return ''; }
    $map = array(
        '--card-radius' => isset($cd['radius']) && is_numeric($cd['radius']) ? max(0, (float)$cd['radius']) . 'px' : '',
        '--tag-bg' => isset($cd['badge_bg']) ? (string)$cd['badge_bg'] : '',
        '--tag-color' => isset($cd['badge_color']) ? (string)$cd['badge_color'] : '',
        '--cat-bg' => isset($cd['label_bg']) ? (string)$cd['label_bg'] : '',
        '--cat-color' => isset($cd['label_color']) ? (string)$cd['label_color'] : '',
        '--card-ic-bg' => isset($cd['icon_bg']) ? (string)$cd['icon_bg'] : '',
    );
    $out = '';
    foreach ($map as $k => $val) {
        if ($val === '' || preg_match('/[;{}<>"\']/', $val)) { continue; }
        $out .= $k . ':' . $val . ';';
    }
    return $out;
}

/** Kept for the admin section previews. */
function hc_vars_style()
{
    return hc_vars();
}

function hc_head()
{
    $page = $GLOBALS['HC']['page'];
    $name = s('business_name', 'Website');
    $title = hc_page_title();
    $desc = c('seo_description', '');
    $canon = c('canonical_url', '') !== '' ? c('canonical_url') : ($page === 'home' ? hc_abs('') : hc_abs($page));
    $ogTitle = c('og_title', '') !== '' ? c('og_title') : $title;
    $ogDesc = c('og_description', '') !== '' ? c('og_description') : $desc;
    $hero = hc_page_hero_image();
    $ogImg = c('og_image', '');
    if ($ogImg === '') { $ogImg = $hero; }
    if ($ogImg === '') { $ogImg = s('default_og_image', ''); }
    if ($ogImg === '') { $ogImg = s('logo', ''); }
    $index = on(c('robots_index', true)) && !on(c('noindex', false));
    $follow = on(c('robots_follow', true)) && $index;

    echo '<!doctype html>' . "\n" . '<html lang="en-IN">' . "\n" . '<head>' . "\n" . '<meta charset="utf-8">' . "\n";
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">' . "\n";
    echo '<script>document.documentElement.className+=" js"</script>' . "\n";
    echo '<title>' . hc_e($title) . '</title>' . "\n";
    if ($desc !== '') { echo '<meta name="description" content="' . hc_e($desc) . '">' . "\n"; }
    echo '<meta name="robots" content="' . ($index ? 'index' : 'noindex') . ', ' . ($follow ? 'follow' : 'nofollow') . ($index ? ', max-image-preview:large' : '') . '">' . "\n";
    if ($index) { echo '<link rel="canonical" href="' . hc_e($canon) . '">' . "\n"; }
    if (s('google_site_verification', '') !== '') { echo '<meta name="google-site-verification" content="' . hc_e(s('google_site_verification')) . '">' . "\n"; }
    if (s('bing_site_verification', '') !== '') { echo '<meta name="msvalidate.01" content="' . hc_e(s('bing_site_verification')) . '">' . "\n"; }
    echo '<meta property="og:type" content="website"><meta property="og:locale" content="en_IN"><meta property="og:site_name" content="' . hc_e($name) . '">' . "\n";
    echo '<meta property="og:title" content="' . hc_e($ogTitle) . '">' . "\n";
    if ($ogDesc !== '') { echo '<meta property="og:description" content="' . hc_e($ogDesc) . '">' . "\n"; }
    echo '<meta property="og:url" content="' . hc_e($canon) . '">' . "\n";
    if ($ogImg) { echo '<meta property="og:image" content="' . hc_e(hc_abs_img($ogImg)) . '">' . "\n"; }
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    if ($hero !== '') { echo '<link rel="preload" as="image" href="' . hc_e(hc_img($hero)) . '" fetchpriority="high">' . "\n"; }
    echo '<meta name="theme-color" content="' . hc_e(s('colors.dark', '#0F1B33')) . '">' . "\n";
    $fav = s('favicon', '') !== '' ? s('favicon') : s('logo', '');
    if ($fav !== '') { echo '<link rel="icon" href="' . hc_e(hc_img($fav)) . '">' . "\n"; }
    if (s('apple_touch_icon', '') !== '') { echo '<link rel="apple-touch-icon" href="' . hc_e(hc_img(s('apple_touch_icon'))) . '">' . "\n"; }
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n" . '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    echo '<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">' . "\n";
    echo '<link rel="stylesheet" href="' . hc_e(hc_asset('assets/site/style.css')) . '">' . "\n";
    echo hc_vars() . "\n";

    hc_schema();
    if (c('head_code', '') !== '' && empty($GLOBALS['HC']['preview'])) { echo "\n" . c('head_code') . "\n"; }
    echo hc_tracking('head');
    if ($page === 'thank-you') { echo hc_tracking('thankyou'); }
    echo "\n</head>\n";
}

function hc_json_ld($data)
{
    echo '<script type="application/ld+json">' . json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}

function hc_schema()
{
    $page = $GLOBALS['HC']['page'];
    $isPost = c('page_type', '') === 'post';
    $biz = s('schema_type', 'LocalBusiness');
    $type = c('schema_type', '') !== '' ? c('schema_type') : ($page === 'home' ? $biz : ($isPost ? 'Article' : 'WebPage'));

    // Business details (home page, or any page set to a business type)
    if ($page === 'home' || !in_array($type, array('WebPage', 'AboutPage', 'ContactPage', 'Article', 'Service', 'FAQPage', 'none'), true)) {
        $data = array(
            '@context' => 'https://schema.org',
            '@type' => $page === 'home' ? $biz : $type,
            'name' => s('business_name'),
            'description' => s('footer_about', '') !== '' ? s('footer_about') : c('seo_description', ''),
            'url' => hc_abs(''),
            'telephone' => str_replace('tel:', '', hc_tel()),
            'email' => s('email'),
        );
        if (s('logo', '') !== '') { $data['logo'] = hc_abs_img(s('logo')); $data['image'] = hc_abs_img(s('logo')); }
        $addr = array('@type' => 'PostalAddress');
        foreach (array('streetAddress' => s('schema_street', ''), 'addressLocality' => s('city', ''), 'addressRegion' => s('state', ''), 'postalCode' => s('pincode', '')) as $k => $val) {
            if (trim((string)$val) !== '') { $addr[$k] = $val; }
        }
        $addr['addressCountry'] = 'IN';
        $data['address'] = $addr;
        if (s('schema_area_served', '') !== '') { $data['areaServed'] = s('schema_area_served'); }
        if (s('schema_opening_hours', '') !== '') { $data['openingHours'] = s('schema_opening_hours'); }
        if (s('price_range', '') !== '') { $data['priceRange'] = s('price_range'); }
        $same = array_values(array_filter(hc_list(s('social', array()))));
        if ($same) { $data['sameAs'] = $same; }
        hc_json_ld($data);
    } elseif ($type !== 'none') {
        $data = array(
            '@context' => 'https://schema.org',
            '@type' => $type,
            'name' => hc_page_title(),
            'url' => hc_abs($page),
            'description' => c('seo_description', ''),
        );
        if ($type === 'Service') {
            $data['provider'] = array('@type' => $biz, 'name' => s('business_name'), 'telephone' => str_replace('tel:', '', hc_tel()));
            if (s('schema_area_served', '') !== '') { $data['areaServed'] = s('schema_area_served'); }
        }
        if ($type === 'Article' || $type === 'BlogPosting') {
            $data['headline'] = hc_plain_title(c('page_name', ''));
            if (c('post_image', '') !== '') { $data['image'] = hc_abs_img(c('post_image')); }
            if (c('post_date_iso', '') !== '') { $data['datePublished'] = c('post_date_iso'); }
            $data['dateModified'] = c('post_modified_iso', '') !== '' ? c('post_modified_iso') : c('post_date_iso', '');
            $data['author'] = array('@type' => 'Organization', 'name' => s('business_name'));
            $data['publisher'] = array('@type' => 'Organization', 'name' => s('business_name'), 'logo' => array('@type' => 'ImageObject', 'url' => hc_abs_img(s('logo'))));
            $data['mainEntityOfPage'] = hc_abs($page);
        }
        hc_json_ld($data);
    }

    // Breadcrumb (inner pages)
    if ($page !== 'home' && $page !== '404' && $page !== 'thank-you') {
        hc_json_ld(array(
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array(
                array('@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => hc_abs('')),
                array('@type' => 'ListItem', 'position' => 2, 'name' => hc_plain_title(c('page_name', '') !== '' ? c('page_name') : c('hero_title', '')), 'item' => hc_abs($page)),
            ),
        ));
    }

    // FAQ (from any FAQ section on the page)
    $faq = isset($GLOBALS['HC']['faq']) ? $GLOBALS['HC']['faq'] : array();
    if ($faq) {
        $q = array();
        foreach ($faq as $f) {
            $q[] = array('@type' => 'Question', 'name' => $f[0], 'acceptedAnswer' => array('@type' => 'Answer', 'text' => strip_tags($f[1])));
        }
        hc_json_ld(array('@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $q));
    }

    // Custom schema markup pasted in Admin → Advanced (validated, re-encoded so it can never break the page)
    $custom = trim((string)c('custom_schema', ''));
    if ($custom !== '') {
        $custom = trim(preg_replace('~^\s*<script[^>]*>|</script>\s*$~i', '', $custom));
        $decoded = json_decode($custom, true);
        if ($decoded !== null) {
            $items = (isset($decoded[0]) && is_array($decoded[0])) ? $decoded : array($decoded);
            foreach ($items as $item) { hc_json_ld($item); }
        }
    }
}

function hc_logo($class = 'logo', $h = 62)
{
    $name = s('business_name', 'Website');
    $tag = hc_landing() ? 'span' : 'a';
    $out = '<' . $tag . ($tag === 'a' ? ' href="' . hc_e(hc_url('')) . '"' : '') . ' class="' . $class . '" aria-label="' . hc_e($name) . '">';
    if (s('logo', '') !== '') {
        $out .= '<img src="' . hc_e(hc_img(s('logo'))) . '" alt="' . hc_e($name) . '" width="' . round($h * 138 / 62) . '" height="' . $h . '">';
    } else {
        $out .= '<span class="logo-mark">' . hc_icon('car') . '</span>';
    }
    if (s('logo', '') === '' || on(s('show_name_with_logo', false))) {
        $text = s('logo_text', '') !== '' ? s('logo_text') : $name;
        $out .= '<span class="logo-txt"><b>' . hc_title($text) . '</b>' . (s('tagline', '') !== '' ? '<small>' . hc_e(s('tagline')) . '</small>' : '') . '</span>';
    }
    return $out . '</' . $tag . '>';
}

function hc_menu()
{
    $page = $GLOBALS['HC']['page'];
    $items = hc_list(s('menu', array()));
    if ($page === 'home' && count(hc_list(s('home_menu', array())))) { $items = hc_list(s('home_menu', array())); }
    $h = '';
    foreach ($items as $m) {
        $label = hc_i($m, 'label', 0);
        if ($label === '') { continue; }
        $link = trim((string)hc_i($m, 'link', 1));
        $slug = trim($link, '/');
        $isActive = $link !== '' && $link[0] === '#' ? false : (($slug === '' || $slug === 'home') ? false : $slug === $page);
        $h .= '<a href="' . hc_e(hc_url($link)) . '"' . ($isActive ? ' class="active"' : '') . '>' . hc_e($label) . '</a>';
    }
    return $h;
}

function hc_layout_start()
{
    hc_head();
    $page = $GLOBALS['HC']['page'];
    $app = hc_page_app();
    $mbar = on(s('mobile_bar.enabled', true));
    echo '<body class="page-' . hc_e($page) . ($mbar ? ' has-mbar' : '') . '">' . "\n";
    echo hc_tracking('bodystart');
    echo hc_sprite() . "\n";
    echo '<a class="skip" href="#main">Skip to content</a>' . "\n";

    if (on(s('topbar_show', true))) {
        echo '<div class="topbar"><div class="wrap"><div class="tb-left">';
        if (s('topbar_text', '') !== '') { echo '<span><i class="dot"></i>' . hc_e(hc_fill(s('topbar_text'))) . '</span>'; }
        if (on(s('topbar_email_show', true)) && s('email', '') !== '') { echo '<span class="tb-hide">' . hc_icon('mail') . '<a href="mailto:' . hc_e(s('email')) . '">' . hc_e(s('email')) . '</a></span>'; }
        if (on(s('topbar_hours_show', false)) && s('working_hours', '') !== '') { echo '<span class="tb-hide">' . hc_icon('clock') . hc_e(s('working_hours')) . '</span>'; }
        echo '</div>';
        if (on(s('topbar_phone_show', true))) {
            $label = trim((string)s('topbar_phone_label', ''));
            echo '<div class="tb-phone">' . hc_icon('phone') . ($label !== '' ? hc_e($label) . ' ' : '') . '<a href="' . hc_e(hc_tel()) . '" data-track="call">' . hc_e(s('mobile')) . '</a></div>';
        }
        echo '</div></div>' . "\n";
    }

    echo '<header class="site"><div class="wrap">' . hc_logo();
    // Main menu — a row on computers, a drawer from the right on phones (head + foot show only in the drawer)
    echo '<nav class="nav" id="nav" aria-label="Main menu">';
    echo '<div class="nav-head">' . hc_logo('logo') . '<button type="button" class="nav-close" aria-label="Close menu">' . hc_icon('close') . '</button></div>';
    echo '<div class="nav-links">' . hc_menu() . '</div>';
    echo '<div class="nav-foot">';
    echo '<button type="button" class="btn btn-book js-popup"' . hc_popup_attrs('', $app) . '>' . hc_icon('car') . hc_e(s('mobile_bar.main_text', 'Book Cab')) . '</button>';
    echo '<div class="nav-acts">' . hc_call_btn('btn btn-call', s('buttons.call_text', 'Call Now'));
    if (s('whatsapp_number', '') !== '') { echo hc_wa_btn('btn btn-wa'); }
    echo '</div><div class="nav-info">';
    if (s('working_hours', '') !== '') { echo '<span>' . hc_icon('clock') . hc_e(s('working_hours')) . '</span>'; }
    echo '<span>' . hc_icon('phone') . hc_e(s('mobile')) . '</span>';
    if (s('email', '') !== '') { echo '<span>' . hc_icon('mail') . hc_e(s('email')) . '</span>'; }
    echo '</div></div></nav>';
    echo '<div class="nav-backdrop" aria-hidden="true"></div>';
    echo '<div class="head-cta">';
    if (on(s('header_wa_show', true)) && s('whatsapp_number', '') !== '') {
        echo hc_wa_btn('btn btn-wa btn-sm head-wa', '', s('header_wa_text', 'WhatsApp'));
    }
    if (on(s('header_phone_show', true))) {
        echo '<a href="' . hc_e(hc_tel()) . '" class="btn btn-book btn-sm head-call" data-track="call">' . hc_icon('phone') . '<span>' . hc_e(s('header_phone_label', 'Call Now')) . '</span></a>';
    }
    if (on(s('header_button_show', false))) {
        echo '<button type="button" class="btn btn-call btn-sm head-book js-popup"' . hc_popup_attrs('', $app) . '>' . hc_e(s('header_button_text', 'Book Now')) . '</button>';
    }
    echo '<button type="button" class="burger" aria-label="Menu" aria-controls="nav" aria-expanded="false">' . hc_icon('menu') . '</button>';
    echo '</div></div></header>' . "\n";

    echo '<main id="main">' . "\n";
    if (!in_array(c('template', ''), array('home', 'thank-you'), true) && on(c('hero_show', true))) { hc_inner_hero(); }
}

/** Inner-page banner: photo, H1, line under it, breadcrumb. */
function hc_inner_hero($withTitle = true, $style = '')
{
    $name = c('page_name', '') !== '' ? c('page_name') : hc_plain_title(c('hero_title', ''));
    $img = c('hero_image', '') !== '' ? c('hero_image') : s('inner_hero_image', '');
    $text = c('hero_text', '');
    if ($text === '' && c('last_updated', '') !== '') { $text = 'Last updated: ' . c('last_updated'); }
    echo '<section class="ihero">' . ($img !== '' ? '<div class="bg"' . hc_bg($img) . '></div>' : '') . '<div class="wrap"' . $style . '>';
    if ($withTitle) {
        echo '<h1>' . hc_title(c('hero_title', '') !== '' ? c('hero_title') : $name) . '</h1>';
        if ($text !== '') { echo '<p>' . hc_e($text) . '</p>'; }
    }
    echo '<nav class="crumb" aria-label="Breadcrumb"><a href="' . hc_e(hc_url('')) . '">Home</a><span>&rsaquo;</span><b>' . hc_e($name) . '</b></nav>';
    echo '</div></section>' . "\n";
}

/**
 * Blue call-to-action banner.
 * $d = heading, text, call_text, book_text, appliance, image, space_top
 */
function hc_cta_band($d = array())
{
    $g = function ($k, $def = '') use ($d) { return isset($d[$k]) && trim((string)$d[$k]) !== '' ? hc_fill($d[$k]) : $def; };
    $heading = $g('heading', s('cta_heading', 'Need help today?'));
    $text = $g('text', s('cta_text', ''));
    $img = $g('image', s('cta_image', ''));
    $app = $g('appliance', hc_page_app());
    $top = !isset($d['space_top']) || on($d['space_top']);
    $h = '<section class="sec sec-cta"' . ($top ? '' : ' style="padding-top:0"') . '><div class="wrap"><div class="cta-band rv">';
    if ($img !== '') { $h .= '<div class="bg"' . hc_bg($img) . '></div>'; }
    $h .= '<div><h2>' . hc_title($heading) . '</h2>' . ($text !== '' ? '<p>' . hc_e($text) . '</p>' : '') . '</div>';
    $h .= '<div class="btns">';
    $call = $g('call_text', s('cta_call_text', 'Call Now'));
    if ($call !== '-') { $h .= hc_call_btn('btn btn-call btn-lg', $call); }
    $wa = $g('wa_text', s('cta_wa_text', 'WhatsApp'));
    if ($wa !== '-') { $h .= hc_wa_btn('btn btn-wa btn-lg', '', $wa); }
    $book = $g('book_text', s('cta_book_text', '-'));
    if ($book !== '-') { $h .= '<button type="button" class="btn btn-white btn-lg js-popup"' . hc_popup_attrs('', $app) . '>' . hc_e($book) . '</button>'; }
    return $h . '</div></div></div></section>' . "\n";
}

function hc_cta_banner()
{
    echo hc_cta_band(array(
        'heading' => c('cta_heading', ''),
        'text' => c('cta_text', ''),
        'space_top' => c('cta_space_top', false),
    ));
}

function hc_layout_end($showCta = true)
{
    // Extra sections added from Admin → Pages → "Extra Sections"
    echo hc_blocks(c('extra_blocks', array()));

    if ($showCta) { hc_cta_banner(); }
    echo '</main>' . "\n";

    // Footer
    echo '<footer class="site"><div class="wrap"><div class="f-grid"><div>';
    if (s('logo', '') !== '') {
        echo '<div class="f-logo"><img src="' . hc_e(hc_img(s('logo'))) . '" alt="' . hc_e(s('business_name')) . '" width="116" height="52" loading="lazy"></div>';
    } else {
        echo '<div class="f-brand">' . hc_logo('logo light') . '</div>';
    }
    echo hc_paras(hc_fill(s('footer_about', '')));
    if (s('footer_disclaimer', '') !== '') { echo '<p class="disclaimer">' . hc_e(hc_fill(s('footer_disclaimer'))) . '</p>'; }
    $soc = '';
    foreach ((is_array(s('social', array())) ? s('social', array()) : array()) as $net => $url) {
        if (trim((string)$url) === '') { continue; }
        $soc .= '<a href="' . hc_e($url) . '" target="_blank" rel="noopener" aria-label="' . hc_e(ucfirst($net)) . '">' . hc_icon($net) . '</a>';
    }
    if ($soc !== '') { echo '<div class="social">' . $soc . '</div>'; }
    echo '</div>';

    $col = function ($title, $links) {
        $items = '';
        foreach (hc_list($links) as $l) {
            $label = hc_i($l, 'label', 0);
            if ($label !== '') { $items .= '<li><a href="' . hc_e(hc_url(hc_i($l, 'link', 1))) . '">' . hc_e($label) . '</a></li>'; }
        }
        return $items === '' ? '' : '<div><h4>' . hc_e($title) . '</h4><ul>' . $items . '</ul></div>';
    };
    echo $col(s('footer_quick_title', 'Quick Links'), s('footer_quick_links', array()));
    echo $col(s('footer_info_title', 'Company'), s('footer_info_links', array()));

    echo '<div><h4>' . hc_e(s('footer_contact_title', 'Contact')) . '</h4><ul class="f-contact">';
    echo '<li>' . hc_icon('phone') . '<a href="' . hc_e(hc_tel()) . '" data-track="call">' . hc_e(s('mobile')) . '</a></li>';
    if (s('mobile_2', '') !== '') { echo '<li>' . hc_icon('phone') . '<a href="' . hc_e(hc_tel_for(s('mobile_2'))) . '" data-track="call">' . hc_e(s('mobile_2')) . '</a></li>'; }
    if (s('whatsapp_number', '') !== '') { echo '<li>' . hc_icon('wa') . '<a href="' . hc_e(hc_wa()) . '" target="_blank" rel="noopener" data-track="whatsapp">WhatsApp: ' . hc_e(hc_wa_short()) . '</a></li>'; }
    if (s('email', '') !== '') { echo '<li>' . hc_icon('mail') . '<a href="mailto:' . hc_e(s('email')) . '">' . hc_e(s('email')) . '</a></li>'; }
    if (s('address', '') !== '') { echo '<li>' . hc_icon('pin') . '<span>' . hc_e(s('address')) . '</span></li>'; }
    if (on(s('footer_hours_show', false)) && s('working_hours', '') !== '') { echo '<li>' . hc_icon('clock') . '<span>' . hc_e(s('working_hours')) . '</span></li>'; }
    echo '</ul></div></div>';

    // "Popular searches" row of keyword links
    $tags = array();
    foreach (hc_list(s('footer_tags', array())) as $l) {
        $label = hc_i($l, 'label', 0);
        if ($label !== '') { $tags[] = '<a href="' . hc_e(hc_url(hc_i($l, 'link', 1))) . '">' . hc_e($label) . '</a>'; }
    }
    if ($tags) {
        echo '<div class="f-tags">' . (s('footer_tags_title', '') !== '' ? '<b>' . hc_e(s('footer_tags_title')) . '</b>' : '') . implode('', $tags) . '</div>';
    }
    $credit = '';
    if (trim((string)s('footer_credit_text', '')) !== '') {
        $credit = trim((string)s('footer_credit_link', '')) !== ''
            ? '<a href="' . hc_e(hc_url(s('footer_credit_link'))) . '">' . hc_e(s('footer_credit_text')) . '</a>'
            : hc_e(s('footer_credit_text'));
    }
    echo '<div class="f-bottom"><span>' . hc_e(hc_fill(s('copyright', '© {year} {business}. All rights reserved.'))) . '</span>' . ($credit !== '' ? '<span class="f-credit">' . $credit . '</span>' : '') . '</div>';
    echo '</div></footer>' . "\n";

    hc_floating();
    hc_mobile_bar();
    hc_popup();
    hc_scripts();
}

function hc_scripts()
{
    echo '<script>window.SITE=' . json_encode(array('sending' => s('buttons.sending_text', 'Sending…'), 'offline' => s('form_offline_text', 'Could not send. Please check your internet connection, or call us directly.')), JSON_UNESCAPED_UNICODE) . ';</script>' . "\n";
    echo '<script src="' . hc_e(hc_asset('assets/site/main.js')) . '" defer></script>' . "\n";
    echo hc_tracking('bodyend');
    echo '</body>' . "\n" . '</html>';
}

function hc_floating()
{
    $f = s('floating', array());
    $g = function ($k, $d) use ($f) { return is_array($f) && isset($f[$k]) ? $f[$k] : $d; };
    if (!on($g('floating_enabled', true))) { return; }
    $call = on($g('call_enabled', true));
    $wa = on($g('whatsapp_enabled', true));
    if (!$call && !$wa) { return; }

    $cls = 'float side-' . ($g('position_side', 'right') === 'left' ? 'left' : 'right') . ' ' . ($g('layout', 'vertical') === 'horizontal' ? 'horizontal' : 'vertical');
    $anim = preg_replace('/[^a-z]/', '', (string)$g('animation', 'pulse'));
    if ($anim !== '' && $anim !== 'none') { $cls .= ' fb-' . $anim; }
    if (!on($g('show_on_mobile', false))) { $cls .= ' fb-hide-mobile'; }
    if (!on($g('show_on_desktop', true))) { $cls .= ' fb-hide-desktop'; }
    $tip = on($g('tooltip_enabled', true));

    echo '<div class="' . $cls . '">';
    if ($wa) {
        echo '<a href="' . hc_e(hc_wa()) . '" target="_blank" rel="noopener" class="f-wa" aria-label="WhatsApp" data-track="whatsapp">' . hc_icon('wa') . ($tip ? '<span class="tip">' . hc_e($g('whatsapp_tooltip_text', 'Chat on WhatsApp')) . '</span>' : '') . '</a>';
    }
    if ($call) {
        echo '<a href="' . hc_e(hc_tel()) . '" class="f-call" aria-label="Call" data-track="call">' . hc_icon('phone') . ($tip ? '<span class="tip">' . hc_e($g('call_tooltip_text', 'Call Now')) . '</span>' : '') . '</a>';
    }
    echo '</div>' . "\n";
}

function hc_mobile_bar()
{
    if (!on(s('mobile_bar.enabled', true))) { return; }
    echo '<div class="mbar">'
        . '<a href="' . hc_e(hc_tel()) . '" class="m-call" data-track="call">' . hc_icon('phone') . hc_e(s('mobile_bar.call_text', 'Call')) . '</a>'
        . '<a href="' . hc_e(hc_wa()) . '" target="_blank" rel="noopener" class="m-wa" data-track="whatsapp">' . hc_icon('wa') . hc_e(s('mobile_bar.whatsapp_text', 'WhatsApp')) . '</a>'
        . '<button type="button" class="m-book js-popup"' . hc_popup_attrs('', hc_page_app()) . '>' . hc_e(s('mobile_bar.main_text', 'Book Now')) . '</button>'
        . '</div>' . "\n";
}

function hc_popup()
{
    echo '<div class="modal" id="modal" aria-hidden="true"><div class="box" role="dialog" aria-modal="true" aria-labelledby="m-title">';
    echo '<div class="m-head">';
    echo '<h3 id="m-title">' . hc_e(s('popup_heading', 'Book Your Cab')) . '</h3>';
    echo '<p class="m-meta" data-default="' . hc_e(s('popup_small', '')) . '">' . hc_e(s('popup_small', '')) . '</p>';
    echo '<button type="button" class="m-close" aria-label="Close">' . hc_icon('close') . '</button></div>';
    echo '<div class="m-body">' . hc_form('popup-form', 'Popup form', true) . '</div>';
    echo '</div></div>' . "\n";
}

/* =====================================================================
   LANDING PAGE CHROME — own header + footer (Site Settings → Landing Pages).
   No menu, no site footer links, logo is not a link.
   ===================================================================== */
function hc_ls($key, $default = '')
{
    return s('landing.' . $key, $default);
}

function hc_tel_for($number)
{
    $d = hc_digits($number);
    if (strlen($d) === 11 && $d[0] === '0') { $d = substr($d, 1); }
    if (strlen($d) === 10) { $d = '91' . $d; }
    return 'tel:+' . $d;
}

function hc_landing_start()
{
    hc_head();
    $page = $GLOBALS['HC']['page'];
    $phone = c('lp_phone', '') !== '' ? c('lp_phone') : s('mobile');
    $tel = c('lp_phone', '') !== '' ? hc_tel_for($phone) : hc_tel();
    $enqText = c('lp_button_text', '') !== '' ? c('lp_button_text') : hc_ls('enq_text', 'Book Now');
    $item = c('lp_enquiry_item', '');

    $showCall = on(c('lp_show_call', true));
    $showEnq = on(c('lp_show_enquiry', true));
    $vis = function ($d, $m) { return ($d ? '' : ' lp-nod') . ($m ? '' : ' lp-nom'); };

    $style = '';
    if (hc_ls('header_bg', '') !== '') { $style .= 'background:' . hc_e(hc_ls('header_bg')) . ';'; }
    if (hc_ls('header_color', '') !== '') { $style .= 'color:' . hc_e(hc_ls('header_color')) . ';'; }

    $mbar = on(hc_ls('mobile_bar', true));
    echo '<body class="page-landing page-' . hc_e($page) . ($mbar ? ' has-mbar' : '') . '">' . "\n";
    echo hc_tracking('bodystart');
    echo hc_sprite() . "\n";
    echo '<header class="lp-header' . (on(hc_ls('sticky', true)) ? ' sticky' : '') . '"' . ($style ? ' style="' . $style . '"' : '') . '><div class="wrap lp-bar">';
    echo hc_logo();
    echo '<div class="lp-actions">';
    if ($showCall) {
        echo '<a class="btn btn-call' . $vis(on(hc_ls('call_desktop', true)), on(hc_ls('call_mobile', true))) . '" href="' . hc_e($tel) . '" data-track="call">'
            . hc_icon('phone') . hc_e(hc_ls('call_text', 'Call Now')) . (on(hc_ls('call_show_number', false)) ? ' ' . hc_e($phone) : '') . '</a>';
    }
    if ($showEnq) {
        echo '<button type="button" class="btn btn-book js-popup' . $vis(on(hc_ls('enq_desktop', true)), on(hc_ls('enq_mobile', true))) . '"' . hc_popup_attrs($item, hc_page_app()) . '>' . hc_e($enqText) . '</button>';
    }
    echo '</div></div></header>' . "\n";
    echo '<main id="main">' . "\n";
}

function hc_landing_end()
{
    echo '</main>' . "\n";

    if (on(c('lp_show_footer', true)) && on(hc_ls('footer_show', true))) {
        $style = '';
        if (hc_ls('footer_bg', '') !== '') { $style .= 'background:' . hc_e(hc_ls('footer_bg')) . ';'; }
        if (hc_ls('footer_color', '') !== '') { $style .= 'color:' . hc_e(hc_ls('footer_color')) . ';'; }
        echo '<footer class="lp-footer"' . ($style ? ' style="' . $style . '"' : '') . '><div class="wrap">';
        if (trim((string)hc_ls('footer_text', '')) !== '') { echo '<p class="lp-copy">' . hc_e(hc_fill(hc_ls('footer_text'))) . '</p>'; }
        if (trim((string)hc_ls('footer_note', '')) !== '') { echo '<p class="lp-note">' . hc_e(hc_fill(hc_ls('footer_note'))) . '</p>'; }
        echo '</div></footer>' . "\n";
    }

    // Optional bottom bar on mobile (Call | WhatsApp | Book)
    if (on(hc_ls('mobile_bar', true))) {
        $phone = c('lp_phone', '') !== '' ? hc_tel_for(c('lp_phone')) : hc_tel();
        $b = '';
        if (on(hc_ls('mb_call', true))) { $b .= '<a href="' . hc_e($phone) . '" class="m-call" data-track="call">' . hc_icon('phone') . hc_e(hc_ls('call_text', 'Call Now')) . '</a>'; }
        if (on(hc_ls('mb_wa', true))) { $b .= '<a href="' . hc_e(hc_wa()) . '" target="_blank" rel="noopener" class="m-wa" data-track="whatsapp">' . hc_icon('wa') . 'WhatsApp</a>'; }
        if (on(hc_ls('mb_enq', true))) { $b .= '<button type="button" class="m-book js-popup"' . hc_popup_attrs('', hc_page_app()) . '>' . hc_icon('cal') . hc_e(hc_ls('enq_text', 'Book Now')) . '</button>'; }
        if ($b !== '') { echo '<div class="mbar">' . $b . '</div>' . "\n"; }
    }

    if (on(hc_ls('floating', false))) { hc_floating(); }
    hc_popup();
    hc_scripts();
}
