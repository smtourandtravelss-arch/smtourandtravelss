<?php
/* =====================================================================
   PAGE BUILDER SECTIONS — every section of the website design.
   Each block = ['type' => '…', 'data' => […]] (Admin → Pages → sections)
   ===================================================================== */

/* ---------------------------------------------------------------------
   SERVICE CARDS (Admin → Services)
   --------------------------------------------------------------------- */
/** Icon for a spec chip on a cab card ("7+1 Seats" → seat, "4 Bags" → bag, "AC" → snow …). */
function hc_spec_icon($text)
{
    $t = strtolower((string)$text);
    if (preg_match('/seat|passeng|person|pax|sitter/', $t)) { return 'seat'; }
    if (preg_match('/bag|luggage|carrier|boot/', $t)) { return 'bag'; }
    if (preg_match('/\bac\b|a\/c|air.?con/', $t)) { return 'snow'; }
    if (preg_match('/decor|wedding|flower/', $t)) { return 'heart'; }
    if (preg_match('/driver|chauffeur/', $t)) { return 'user'; }
    if (preg_match('/gps|track/', $t)) { return 'pin'; }
    return 'tick';
}

/** One cab card (Admin → Cabs & Fares). */
function hc_card_html($card, $showCat = true)
{
    $title = $card['title'];
    $cat = isset($card['category']) ? trim((string)$card['category']) : '';
    $img = !empty($card['image']) ? hc_img($card['image']) : '';
    $alt = !empty($card['image_alt']) ? $card['image_alt'] : $title . ' cab' . (s('city', '') !== '' ? ' in ' . s('city') : '');
    $price = hc_card_price_text($card);
    $btnText = !empty($card['button_text']) ? $card['button_text'] : s('buttons.card_book_text', 'Book Now');
    $flag = isset($card['badge']) ? trim((string)$card['badge']) : '';
    $tag = isset($card['label']) ? trim((string)$card['label']) : '';

    $h = '<article class="card cab rv' . ($flag !== '' ? ' is-hl' : '') . '" data-cat="' . hc_e($cat) . '">';
    if ($flag !== '') { $h .= '<span class="cab-flag">' . hc_e($flag) . '</span>'; }
    // real photos (jpg / webp) fill the picture area; drawings and cut-outs (svg / png) sit on the tint
    $isPhoto = $img !== '' && preg_match('/\.(jpe?g|webp)(\?|$)/i', $img);
    $h .= '<div class="cab-img' . ($isPhoto ? ' is-photo' : '') . '">' . ($img !== '' ? '<img src="' . hc_e($img) . '" alt="' . hc_e($alt) . '" loading="lazy" width="520" height="' . ($isPhoto ? '330' : '212') . '">' : hc_icon('car')) . '</div>';
    $h .= '<div class="body"><div class="cab-title"><h3>' . hc_e($title) . '</h3>';
    if ($tag !== '') { $h .= '<span class="tag">' . hc_e($tag) . '</span>'; }
    elseif ($showCat && $cat !== '') { $h .= '<span class="tag">' . hc_e($cat) . '</span>'; }
    $h .= '</div>';
    if (!empty($card['subtitle'])) { $h .= '<p class="cab-models">' . hc_e($card['subtitle']) . '</p>'; }
    $feat = hc_strings(isset($card['features']) ? $card['features'] : array());
    if ($feat) {
        $h .= '<ul class="specs">';
        foreach ($feat as $f) { $h .= '<li>' . hc_icon(hc_spec_icon($f)) . hc_e($f) . '</li>'; }
        $h .= '</ul>';
    }
    if (!empty($card['text'])) { $h .= '<p class="best">' . hc_e($card['text']) . '</p>'; }

    $hasPrice = isset($card['price']) && trim((string)$card['price']) !== '';
    $isNum = $hasPrice && is_numeric(str_replace(array(',', ' '), '', (string)$card['price']));
    $h .= '<div class="cab-foot"><div class="price">';
    if ($hasPrice) {
        $h .= '<small>' . hc_e(!empty($card['price_prefix']) ? $card['price_prefix'] : ($isNum ? s('card.price_label', 'Starting') : 'Fare')) . '</small>'
            . '<b' . ($isNum ? '' : ' class="quote"') . '>' . hc_e(hc_price($card['price'])) . '</b>'
            . (!empty($card['old_price']) ? '<s>' . hc_e(hc_price($card['old_price'])) . '</s>' : '')
            . (!empty($card['price_note']) && $isNum ? '<span>' . hc_e($card['price_note']) . '</span>' : '');
    }
    $h .= '</div><div class="acts">';
    if (on(s('buttons.card_show_whatsapp', true))) {
        $h .= '<a href="' . hc_e(hc_wa(hc_card_name($card))) . '" target="_blank" rel="noopener" class="icon-btn wa" data-track="whatsapp" aria-label="' . hc_e(s('buttons.card_whatsapp_text', 'WhatsApp') . ' – ' . $title) . '">' . hc_icon('wa') . '</a>';
    }
    if (on(s('buttons.card_show_call', false))) {
        $h .= '<a href="' . hc_e(hc_tel()) . '" class="icon-btn call" data-track="call" aria-label="' . hc_e(s('buttons.card_call_text', 'Call')) . '">' . hc_icon('phone') . '</a>';
    }
    $meta = 'Selected: ' . $title . ($isNum && $price !== '' ? ' · ' . $price : '');
    $h .= '<button type="button" class="btn btn-book btn-sm js-popup"' . hc_popup_attrs($title, $cat, array('service' => hc_card_value($card), 'meta' => $meta)) . '>' . hc_e($btnText) . '</button>';
    $h .= '</div></div></div></article>';
    return $h;
}

function hc_cards_grid($filter = false, $limit = 0, $category = '', $showCat = null)
{
    static $n = 0;
    $n++;
    $cards = hc_cards_of($category);
    if ($limit > 0) { $cards = array_slice($cards, 0, $limit); }
    if (!$cards) { return ''; }
    if ($showCat === null) { $showCat = trim((string)$category) === ''; }
    $cats = array();
    foreach ($cards as $cd) {
        if (!empty($cd['category']) && !in_array(trim($cd['category']), $cats, true)) { $cats[] = trim($cd['category']); }
    }
    $h = '';
    if ($filter && count($cats) >= 2) {
        $h .= '<div class="filters" role="group" aria-label="Filter" data-for="cards' . $n . '"><button type="button" class="on" data-filter="">' . hc_e(s('card.filter_all_text', 'All')) . '</button>';
        foreach ($cats as $ct) { $h .= '<button type="button" data-filter="' . hc_e($ct) . '">' . hc_e($ct) . '</button>'; }
        $h .= '</div>';
    }
    $h .= '<div class="cards" id="cards' . $n . '">';
    foreach ($cards as $cd) { $h .= hc_card_html($cd, $showCat); }
    return $h . '</div>';
}

/* ---------------------------------------------------------------------
   HEADINGS & SMALL PIECES
   --------------------------------------------------------------------- */
function hc_head_html($small, $heading, $text, $center = true, $rv = true)
{
    $small = hc_fill((string)$small);
    $heading = hc_fill((string)$heading);
    $text = hc_fill((string)$text);
    if ($small === '' && $heading === '' && trim($text) === '') { return ''; }
    $h = '<div class="sec-head' . ($center ? '' : ' left') . ($rv ? ' rv' : '') . '">';
    if ($small !== '') { $h .= '<span class="eyebrow">' . hc_e($small) . '</span>'; }
    if ($heading !== '') { $h .= '<h2>' . hc_title($heading) . '</h2>'; }
    $h .= hc_paras($text);
    return $h . '</div>';
}

/** Side heading (no centring) used beside photos: eyebrow + h2 + muted paragraph. */
function hc_side_head($d, $textStyle = true)
{
    $h = '';
    if (hc_bd($d, 'small') !== '') { $h .= '<span class="eyebrow">' . hc_e(hc_bd($d, 'small')) . '</span>'; }
    if (hc_bd($d, 'heading') !== '') { $h .= '<h2>' . hc_title(hc_bd($d, 'heading')) . '</h2>'; }
    if (hc_bd($d, 'text') !== '') { $h .= hc_paras(hc_bd($d, 'text'), 'muted', $textStyle ? ' style="margin-top:14px;font-size:17px"' : ''); }
    return $h;
}

function hc_ticks_html($items, $class = 'ticks')
{
    $list = hc_strings($items);
    if (!$list) { return ''; }
    $h = '<ul class="' . $class . '">';
    foreach ($list as $p) { $h .= '<li>' . hc_icon('check') . hc_e(hc_fill($p)) . '</li>'; }
    return $h . '</ul>';
}

function hc_stats_html($items, $dark = false)
{
    $h = '';
    foreach (hc_list($items) as $st) {
        $val = hc_i($st, 'value', 0);
        if ($val === '') { continue; }
        $h .= '<div class="stat rv"><b>' . hc_e($val) . '</b><span>' . hc_e(hc_i($st, 'label', 1)) . '</span></div>';
    }
    if ($h === '') { return ''; }
    return '<section class="stats' . ($dark ? ' dark' : '') . '"><div class="wrap"><div class="stats-grid">' . $h . '</div></div></section>';
}

function hc_faq_html($items)
{
    $h = '<div class="faq rv">';
    $i = 0;
    foreach (hc_list($items) as $f) {
        $q = hc_i($f, 'question', 0);
        if ($q === '') { continue; }
        $h .= '<details' . ($i === 0 ? ' open' : '') . '><summary>' . hc_e(hc_fill($q)) . '</summary>' . hc_paras(hc_fill(hc_i($f, 'answer', 1))) . '</details>';
        $i++;
    }
    return $h . '</div>';
}

function hc_map_html($embed)
{
    $embed = trim((string)$embed);
    if (preg_match('~src="([^"]+)"~i', $embed, $m)) { $embed = html_entity_decode($m[1]); }
    if ($embed === '' || !preg_match('~^https://~i', $embed)) { return ''; }
    return '<div class="map"><iframe src="' . hc_e($embed) . '" title="Map – ' . hc_e(s('business_name')) . '" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe></div>';
}

function hc_youtube_id($url)
{
    if (preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|shorts/|live/))([A-Za-z0-9_-]{6,})~', (string)$url, $m)) { return $m[1]; }
    return '';
}

/* ---------------------------------------------------------------------
   BLOCK HELPERS
   --------------------------------------------------------------------- */
function hc_blocks($blocks)
{
    $out = '';
    foreach (hc_list($blocks) as $i => $b) {
        if (!is_array($b) || empty($b['type'])) { continue; }
        $d = isset($b['data']) && is_array($b['data']) ? $b['data'] : array();
        if (isset($d['show']) && !on($d['show'])) { continue; }
        $fn = 'hc_block_' . preg_replace('/[^a-z0-9_]/', '', $b['type']);
        if (function_exists($fn)) { $out .= "\n" . $fn($d, $i); }
    }
    return $out;
}

function hc_bd($d, $k, $def = '')
{
    return isset($d[$k]) && $d[$k] !== null && $d[$k] !== '' ? hc_fill($d[$k]) : $def;
}

function hc_anchor_attr($d, $default = '')
{
    $anchor = trim((string)hc_bd($d, 'anchor', $default));
    $anchor = preg_replace('/[^a-z0-9_-]/i', '', $anchor);
    return $anchor !== '' ? ' id="' . hc_e($anchor) . '"' : '';
}

function hc_sec_open($d, $extraClass = '', $id = '', $defaultBg = 'white')
{
    $bg = hc_bd($d, 'background', $defaultBg);
    $cls = 'sec' . ($bg === 'light' ? ' sec-soft' : '') . ($extraClass ? ' ' . $extraClass : '');
    return '<section class="' . $cls . '"' . hc_anchor_attr($d, $id) . '>';
}

function hc_d_head($d, $center = true)
{
    return hc_head_html(hc_bd($d, 'small'), hc_bd($d, 'heading'), hc_bd($d, 'text'), $center);
}

/** Appliance used by the buttons of a section (section value, else the page's appliance). */
function hc_d_app($d)
{
    return hc_bd($d, 'appliance', hc_page_app());
}

/* ---------------------------------------------------------------------
   DESIGN SECTIONS
   --------------------------------------------------------------------- */

/** Big banner with booking form. */
function hc_block_hero_form($d, $i = 0)
{
    $app = hc_d_app($d);
    $form = on(hc_bd($d, 'form_show', true));
    $h = '<section class="hero' . ($form ? '' : ' no-form') . '"' . hc_anchor_attr($d) . '>';
    if (hc_bd($d, 'image') !== '') { $h .= '<div class="hero-bg"' . hc_bg(hc_bd($d, 'image')) . '></div>'; }
    $h .= '<div class="wrap"><div>';
    if (hc_bd($d, 'pill') !== '') { $h .= '<div class="hero-pill"><i class="dot"></i>' . hc_e(hc_bd($d, 'pill')) . '</div>'; }
    $h .= '<h1>' . hc_hero_title(hc_bd($d, 'heading', s('business_name'))) . '</h1>';
    $h .= hc_paras(hc_bd($d, 'text'), 'lead');
    $h .= hc_ticks_html(hc_bd($d, 'points', array()));
    $btns = '';
    if (on(hc_bd($d, 'show_call', true))) { $btns .= hc_call_btn('btn btn-book btn-lg', hc_bd($d, 'call_text', 'Call {phone}')); }
    if (on(hc_bd($d, 'show_wa', true))) {
        $msg = hc_bd($d, 'wa_message');
        $btns .= $msg !== '' ? hc_wa_btn('btn btn-wa btn-lg', '', hc_bd($d, 'wa_text', 'WhatsApp Us'), $msg)
            : hc_wa_btn('btn btn-wa btn-lg', $app, hc_bd($d, 'wa_text', 'WhatsApp Us'));
    }
    if (hc_bd($d, 'button_text') !== '') { $btns .= hc_button(hc_bd($d, 'button_text'), hc_bd($d, 'button_link', 'popup'), 'btn btn-white btn-lg', '', $app); }
    if ($btns !== '') { $h .= '<div class="hero-btns">' . $btns . '</div>'; }
    $types = hc_strings(hc_bd($d, 'types', array()));
    if ($types) {
        $h .= '<div class="hero-types">';
        foreach ($types as $t) { $h .= '<span>' . hc_e($t) . '</span>'; }
        $h .= '</div>';
    }
    $h .= '</div>';
    if ($form) {
        $h .= '<div class="form-card" id="' . ($i === 0 ? 'book' : 'book' . $i) . '">';
        if (hc_bd($d, 'form_badge') !== '') { $h .= '<span class="badge">' . hc_e(hc_bd($d, 'form_badge')) . '</span>'; }
        if (hc_bd($d, 'form_heading') !== '') { $h .= '<h3>' . hc_e(hc_bd($d, 'form_heading')) . '</h3>'; }
        if (hc_bd($d, 'form_text') !== '') { $h .= '<p class="sub">' . hc_e(hc_bd($d, 'form_text')) . '</p>'; }
        $h .= hc_form('', hc_bd($d, 'form_name', 'Page form'), false, hc_bd($d, 'enquiry_item'), hc_bd($d, 'submit_text', null), $app, hc_bd($d, 'trip'));
        $h .= '</div>';
    }
    return $h . '</div></section>';
}

/** White strip of trust badges (sits over the bottom of the banner). */
function hc_block_features($d)
{
    $h = '';
    $n = 0;
    foreach (hc_list(hc_bd($d, 'items', array())) as $f) {
        if (hc_i($f, 'title') === '') { continue; }
        $n++;
        $h .= '<div class="feat"><div class="ic">' . hc_icon(hc_i($f, 'icon', null, 'check')) . '</div><div><b>' . hc_e(hc_i($f, 'title')) . '</b>'
            . (hc_i($f, 'text') !== '' ? '<small>' . hc_e(hc_i($f, 'text')) . '</small>' : '') . '</div></div>';
    }
    if ($h === '') { return ''; }
    return '<div class="features"' . hc_anchor_attr($d) . '><div class="wrap" style="--n:' . $n . '">' . $h . '</div></div>';
}

/** Service tiles (icon + title + text + link). Big = centred drawings. */
function hc_block_types($d)
{
    $big = on(hc_bd($d, 'big', false));
    $h = hc_sec_open($d) . '<div class="wrap">' . hc_d_head($d) . '<div class="type-grid' . ($big ? ' big' : '') . '">';
    foreach (hc_list(hc_bd($d, 'items', array())) as $t) {
        $title = hc_i($t, 'title');
        if ($title === '') { continue; }
        $inner = '<span class="ic-box">' . hc_icon(hc_i($t, 'icon', null, 'car')) . '</span><h4>' . hc_e($title) . '</h4>' . (hc_i($t, 'text') !== '' ? '<p>' . hc_e(hc_fill(hc_i($t, 'text'))) . '</p>' : '');
        $link = hc_landing_link(trim((string)hc_i($t, 'link')));
        $more = hc_i($t, 'link_text') !== '' ? hc_i($t, 'link_text') : s('buttons.more_text', 'Book now');
        $moreHtml = '<span class="more">' . hc_e($more) . hc_icon('arrow') . '</span>';
        if ($link === 'popup') {
            $h .= '<button type="button" class="type rv js-popup"' . hc_popup_attrs('', '', array('trip' => hc_i($t, 'trip'), 'service' => hc_i($t, 'car'), 'meta' => $title)) . '>' . $inner . $moreHtml . '</button>';
            continue;
        }
        if ($link !== '') {
            $h .= '<a href="' . hc_e(hc_url($link)) . '" class="type rv">' . $inner . $moreHtml . '</a>';
        } else {
            $h .= '<div class="type rv">' . $inner . '</div>';
        }
    }
    return $h . '</div></div></section>';
}

/** Service cards from Admin → Services. */
function hc_block_services($d)
{
    $cat = hc_bd($d, 'category');
    $grid = hc_cards_grid(on(hc_bd($d, 'filter', false)), (int)hc_bd($d, 'limit', 0), $cat, on(hc_bd($d, 'show_category', $cat === '')));
    if ($grid === '') { return ''; }
    $h = hc_sec_open($d, '', 'services') . '<div class="wrap">' . hc_d_head($d) . $grid;
    if (hc_bd($d, 'button_text') !== '') { $h .= '<div class="center mt">' . hc_button(hc_bd($d, 'button_text'), hc_bd($d, 'button_link', 'popup'), 'btn btn-book btn-lg', '', hc_d_app($d)) . '</div>'; }
    return $h . '</div></section>';
}

/** Photo + "Sound familiar?" problem chips. */
function hc_block_problems($d)
{
    $alt = hc_bd($d, 'image_alt', hc_plain_title(hc_bd($d, 'heading')));
    $h = hc_sec_open($d) . '<div class="wrap prob-grid"><div class="prob-img rv">';
    if (hc_bd($d, 'image') !== '') { $h .= '<img src="' . hc_e(hc_img(hc_bd($d, 'image'))) . '" alt="' . hc_e($alt) . '" loading="lazy" width="900" height="1100">'; }
    if (hc_bd($d, 'alert_title') !== '') {
        $h .= '<div class="float-card"><div class="ic">' . hc_icon('alert') . '</div><div><b>' . hc_e(hc_bd($d, 'alert_title')) . '</b>' . (hc_bd($d, 'alert_text') !== '' ? '<small>' . hc_e(hc_bd($d, 'alert_text')) . '</small>' : '') . '</div></div>';
    }
    $h .= '</div><div class="rv">' . hc_side_head($d);
    $chips = hc_strings(hc_bd($d, 'items', array()));
    if ($chips) {
        $h .= '<div class="chips">';
        foreach ($chips as $c) { $h .= '<span class="chip">' . hc_icon('alert') . hc_e($c) . '</span>'; }
        $h .= '</div>';
    }
    if (hc_bd($d, 'button_text') !== '') { $h .= hc_button(hc_bd($d, 'button_text'), hc_bd($d, 'button_link', 'popup'), 'btn btn-book btn-lg', '', hc_d_app($d), 'cal'); }
    return $h . '</div></div></section>';
}

/** Dark "How it works" steps. */
function hc_block_steps($d)
{
    $h = '<section class="sec steps-sec"' . hc_anchor_attr($d, 'how') . '><div class="wrap">' . hc_d_head($d) . '<div class="steps">';
    $n = 0;
    foreach (hc_list(hc_bd($d, 'items', array())) as $st) {
        if (hc_i($st, 'title', 1) === '') { continue; }
        $n++;
        $h .= '<div class="step rv">' . (hc_i($st, 'icon', 0) !== '' ? '<div class="ic">' . hc_icon(hc_i($st, 'icon', 0)) . '</div>' : '')
            . '<div class="num">' . str_pad((string)$n, 2, '0', STR_PAD_LEFT) . '</div><h3>' . hc_e(hc_i($st, 'title', 1)) . '</h3><p>' . hc_e(hc_fill(hc_i($st, 'text', 2))) . '</p></div>';
    }
    return $h . '</div></div></section>';
}

/** Why choose us — dark grid of points, or 2 photos + badge + points when a photo is set. */
function hc_block_why($d)
{
    if (hc_bd($d, 'image') === '') {
        $h = '<section class="sec why-dark"' . hc_anchor_attr($d, 'why') . '><div class="wrap">' . hc_d_head($d) . '<div class="why-cards">';
        foreach (hc_list(hc_bd($d, 'items', array())) as $f) {
            if (hc_i($f, 'title', 1) === '') { continue; }
            $h .= '<div class="why-card rv"><div class="ic">' . hc_icon(hc_i($f, 'icon', 0, 'check')) . '</div><h3>' . hc_e(hc_i($f, 'title', 1)) . '</h3><p>' . hc_e(hc_fill(hc_i($f, 'text', 2))) . '</p></div>';
        }
        $h .= '</div>';
        if (hc_bd($d, 'button_text') !== '') { $h .= '<div class="center mt">' . hc_button(hc_bd($d, 'button_text'), hc_bd($d, 'button_link', 'popup'), 'btn btn-book btn-lg', '', hc_d_app($d)) . '</div>'; }
        return $h . '</div></section>';
    }
    $alt = hc_bd($d, 'image_alt', s('business_name'));
    $h = hc_sec_open($d, '', 'why') . '<div class="wrap why-grid"><div class="why-img rv">';
    if (hc_bd($d, 'image') !== '') { $h .= '<div class="main"><img src="' . hc_e(hc_img(hc_bd($d, 'image'))) . '" alt="' . hc_e($alt) . '" loading="lazy" width="1000" height="1100"></div>'; }
    if (hc_bd($d, 'small_image') !== '') { $h .= '<div class="small"><img src="' . hc_e(hc_img(hc_bd($d, 'small_image'))) . '" alt="" loading="lazy" width="500" height="500"></div>'; }
    if (hc_bd($d, 'badge_big') !== '') { $h .= '<div class="badge"><b>' . hc_e(hc_bd($d, 'badge_big')) . '</b>' . (hc_bd($d, 'badge_text') !== '' ? '<small>' . hc_e(hc_bd($d, 'badge_text')) . '</small>' : '') . '</div>'; }
    $h .= '</div><div class="rv">' . hc_side_head($d) . '<div class="why-list">';
    foreach (hc_list(hc_bd($d, 'items', array())) as $f) {
        if (hc_i($f, 'title', 1) === '') { continue; }
        $h .= '<div class="why-item"><div class="ic">' . hc_icon(hc_i($f, 'icon', 0, 'check')) . '</div><div><h4>' . hc_e(hc_i($f, 'title', 1)) . '</h4><p>' . hc_e(hc_fill(hc_i($f, 'text', 2))) . '</p></div></div>';
    }
    $h .= '</div>';
    if (hc_bd($d, 'button_text') !== '') { $h .= '<div class="mt">' . hc_button(hc_bd($d, 'button_text'), hc_bd($d, 'button_link', 'popup'), 'btn btn-book btn-lg', '', hc_d_app($d)) . '</div>'; }
    return $h . '</div></div></section>';
}

/** Brand names strip. */
function hc_block_brands($d)
{
    $names = hc_strings(hc_bd($d, 'items', array()));
    if (!$names) { return ''; }
    $h = '<section class="brands"' . hc_anchor_attr($d) . '><div class="wrap rv">';
    if (hc_bd($d, 'heading') !== '') { $h .= '<h3>' . hc_e(hc_bd($d, 'heading')) . '</h3>'; }
    $h .= '<div class="brand-row">';
    foreach ($names as $b) { $h .= '<span>' . hc_e($b) . '</span>'; }
    $h .= '</div>';
    if (hc_bd($d, 'note') !== '') { $h .= '<small>' . hc_e(hc_bd($d, 'note')) . '</small>'; }
    return $h . '</div></section>';
}

/** Customer reviews slider (only real reviews, please). */
function hc_block_testimonials($d)
{
    $cards = '';
    foreach (hc_list(hc_bd($d, 'items', array())) as $t) {
        $name = hc_i($t, 'name', 0);
        if ($name === '' || hc_i($t, 'text', 2) === '') { continue; }
        $words = preg_split('/\s+/', trim($name));
        $ini = '';
        foreach (array_slice($words, 0, 2) as $w) { $ini .= mb_substr($w, 0, 1, 'UTF-8'); }
        $r = (int)hc_i($t, 'rating', null, 5);
        $r = $r < 1 || $r > 5 ? 5 : $r;
        $cards .= '<div class="review"><span class="q">&rdquo;</span><div class="stars" aria-label="' . $r . ' out of 5">' . str_repeat('★', $r) . '<span>' . str_repeat('★', 5 - $r) . '</span></div>'
            . '<p>' . hc_e(hc_i($t, 'text', 2)) . '</p><div class="who"><span class="av">' . hc_e(mb_strtoupper($ini, 'UTF-8')) . '</span><div><b>' . hc_e($name) . '</b>'
            . (hc_i($t, 'area', 1) !== '' ? '<small>' . hc_e(hc_i($t, 'area', 1)) . '</small>' : '') . '</div></div></div>';
    }
    if ($cards === '') { return ''; }
    $h = hc_sec_open($d, '', 'reviews') . '<div class="wrap">' . hc_d_head($d);
    $h .= '<div class="rv-slider rv"><div class="rv-track">' . $cards . '</div>';
    $h .= '<div class="rv-nav"><button type="button" class="rv-prev" aria-label="Previous">' . hc_icon('left') . '</button><button type="button" class="rv-next" aria-label="Next">' . hc_icon('right') . '</button></div></div>';
    if (hc_bd($d, 'button_text') !== '' && hc_bd($d, 'button_link') !== '') {
        $h .= '<div class="rv-more">' . hc_button(hc_bd($d, 'button_text'), hc_bd($d, 'button_link'), 'btn btn-ghost', '', '', 'google') . '</div>';
    }
    return $h . '</div></section>';
}

/** FAQ with side heading + "Still have a question?" box. */
function hc_block_faq($d)
{
    $faq = hc_faq_html(hc_bd($d, 'items', array()));
    $h = hc_sec_open($d, '', 'faq', 'light') . '<div class="wrap faq-grid"><div class="faq-side rv">';
    if (hc_bd($d, 'small') !== '') { $h .= '<span class="eyebrow">' . hc_e(hc_bd($d, 'small')) . '</span>'; }
    if (hc_bd($d, 'heading') !== '') { $h .= '<h2>' . hc_title(hc_bd($d, 'heading')) . '</h2>'; }
    $h .= hc_paras(hc_bd($d, 'text'));
    if (on(hc_bd($d, 'side_call', true))) { $h .= '<div class="faq-call">' . hc_call_btn('btn btn-call', hc_bd($d, 'side_call_text', '{phone}')) . '</div>'; }
    $h .= '</div>' . $faq . '</div>';
    if (on(hc_bd($d, 'help_show', true))) {
        $h .= '<div class="wrap"><div class="help-box rv"><div class="t"><b>' . hc_e(hc_bd($d, 'help_title', 'Still have a question?')) . '</b>'
            . (hc_bd($d, 'help_text') !== '' ? '<span>' . hc_e(hc_bd($d, 'help_text')) . '</span>' : '') . '</div><div class="btns">'
            . hc_call_btn('btn btn-book btn-lg', hc_bd($d, 'help_call_text', '{phone}'))
            . hc_wa_btn('btn btn-wa btn-lg', '', hc_bd($d, 'help_wa_text', 'WhatsApp'))
            . '</div></div></div>';
    }
    return $h . '</section>';
}

/** Blue call-to-action banner. */
function hc_block_cta($d)
{
    return hc_cta_band($d);
}

/** One "photo + text" row (used by the rows section). */
function hc_split_row($r, $right = false)
{
    $img = hc_bd($r, 'image');
    $alt = hc_bd($r, 'image_alt', hc_plain_title(hc_bd($r, 'heading')));
    $h = '<div class="split' . ($right ? ' rev' : '') . '">';
    if ($img !== '') { $h .= '<div class="split-img rv"><img src="' . hc_e(hc_img($img)) . '" alt="' . hc_e($alt) . '" loading="lazy" width="1000" height="800"></div>'; }
    $h .= '<div class="rv">';
    if (hc_bd($r, 'small') !== '') { $h .= '<span class="eyebrow">' . hc_e(hc_bd($r, 'small')) . '</span>'; }
    if (hc_bd($r, 'heading') !== '') { $h .= '<h2>' . hc_title(hc_bd($r, 'heading')) . '</h2>'; }
    $h .= hc_paras(hc_bd($r, 'text'));
    $pts = hc_strings(hc_bd($r, 'points', array()));
    if ($pts) {
        $h .= '<ul class="tick-list">';
        foreach ($pts as $p) { $h .= '<li>' . hc_icon('check') . hc_e($p) . '</li>'; }
        $h .= '</ul>';
    }
    if (hc_bd($r, 'button_text') !== '') { $h .= '<div class="mt">' . hc_button(hc_bd($r, 'button_text'), hc_bd($r, 'button_link', 'popup'), 'btn btn-book btn-lg', '', hc_page_app()) . '</div>'; }
    return $h . '</div></div>';
}

/** Several photo + text rows in one section. */
function hc_block_split($d)
{
    $rows = '';
    foreach (hc_list(hc_bd($d, 'rows', array())) as $r) {
        if (!is_array($r)) { continue; }
        $rows .= hc_split_row($r, hc_i($r, 'image_position') === 'right');
    }
    if ($rows === '') { return ''; }
    return hc_sec_open($d) . '<div class="wrap">' . hc_d_head($d) . $rows . '</div></section>';
}

/** Single photo + text. */
function hc_block_image_text($d)
{
    return hc_sec_open($d) . '<div class="wrap">' . hc_split_row($d, hc_bd($d, 'image_position', 'left') === 'right') . '</div></section>';
}

function hc_block_heading_text($d)
{
    $center = hc_bd($d, 'align', 'center') !== 'left';
    $h = hc_sec_open($d) . '<div class="wrap">' . hc_d_head($d, $center);
    if (hc_bd($d, 'button_text') !== '') {
        $h .= '<div class="' . ($center ? 'center' : '') . '" style="margin-top:-24px">' . hc_button(hc_bd($d, 'button_text'), hc_bd($d, 'button_link', 'popup'), 'btn btn-book btn-lg', '', hc_d_app($d)) . '</div>';
    }
    return $h . '</div></section>';
}

function hc_block_rich_text($d)
{
    $h = hc_sec_open($d) . '<div class="wrap"><div class="prose">';
    if (hc_bd($d, 'heading') !== '') { $h .= hc_head_html(hc_bd($d, 'small'), hc_bd($d, 'heading'), '', false, false); }
    $h .= hc_rich(hc_bd($d, 'body'));
    return $h . '</div></div></section>';
}

function hc_block_stats($d)
{
    return hc_stats_html(hc_bd($d, 'items', array()), hc_bd($d, 'style', 'light') === 'dark');
}

/** Boxes with an icon, title and text (any number). */
function hc_block_icon_boxes($d)
{
    $h = hc_sec_open($d) . '<div class="wrap">' . hc_d_head($d) . '<div class="type-grid">';
    foreach (hc_list(hc_bd($d, 'items', array())) as $b) {
        if (hc_i($b, 'title', 1) === '') { continue; }
        $h .= '<div class="type rv"><div class="ic-box">' . hc_icon(hc_i($b, 'icon', 0, 'check')) . '</div><h4>' . hc_e(hc_i($b, 'title', 1)) . '</h4>' . hc_paras(hc_fill(hc_i($b, 'text', 2))) . '</div>';
    }
    return $h . '</div></div></section>';
}

function hc_block_photo_cards($d)
{
    $h = hc_sec_open($d) . '<div class="wrap">' . hc_d_head($d) . '<div class="photo-cards">';
    foreach (hc_list(hc_bd($d, 'items', array())) as $b) {
        $title = hc_i($b, 'title');
        $tag = hc_box_link(hc_i($b, 'link'), $title, 'pcard rv');
        $h .= $tag[0];
        if (hc_i($b, 'image') !== '') {
            $h .= '<img src="' . hc_e(hc_img(hc_i($b, 'image'))) . '" alt="' . hc_e(hc_i($b, 'image_alt') !== '' ? hc_i($b, 'image_alt') : $title) . '" loading="lazy" width="700" height="440">';
        }
        $h .= '<span class="pc-txt"><b>' . hc_e($title) . '</b>' . (hc_i($b, 'text') !== '' ? '<small>' . hc_e(hc_i($b, 'text')) . '</small>' : '') . '</span>' . $tag[1];
    }
    return $h . '</div></div></section>';
}

function hc_block_areas($d)
{
    $h = hc_sec_open($d) . '<div class="wrap">' . hc_d_head($d) . '<div class="areas rv">';
    foreach (hc_strings(hc_bd($d, 'items', array())) as $a) { $h .= '<span>' . hc_icon('pin') . hc_e($a) . '</span>'; }
    return $h . '</div></div></section>';
}

/** Dark section: text + contact list on the left, form on the right. */
function hc_block_form($d, $i = 0)
{
    $h = '<section class="sec final"' . hc_anchor_attr($d) . '><div class="wrap"><div class="txt rv">';
    if (hc_bd($d, 'small') !== '') { $h .= '<span class="eyebrow">' . hc_e(hc_bd($d, 'small')) . '</span>'; }
    if (hc_bd($d, 'heading') !== '') { $h .= '<h2>' . hc_title(hc_bd($d, 'heading')) . '</h2>'; }
    $h .= hc_paras(hc_bd($d, 'text'));
    if (on(hc_bd($d, 'show_contact', true))) {
        $h .= '<ul class="c-list"><li><span class="ci">' . hc_icon('phone') . '</span><span><small>Call / WhatsApp</small><a href="' . hc_e(hc_tel()) . '" data-track="call">' . hc_e(s('mobile')) . '</a></span></li>';
        if (s('email', '') !== '') { $h .= '<li><span class="ci">' . hc_icon('mail') . '</span><span><small>Email</small><a href="mailto:' . hc_e(s('email')) . '">' . hc_e(s('email')) . '</a></span></li>'; }
        if (s('address', '') !== '') { $h .= '<li><span class="ci">' . hc_icon('pin') . '</span><span><small>Address</small><b>' . hc_e(s('address')) . '</b></span></li>'; }
        $h .= '</ul>';
    }
    $h .= '</div><div class="form-card rv">';
    if (hc_bd($d, 'form_badge') !== '') { $h .= '<span class="badge">' . hc_e(hc_bd($d, 'form_badge')) . '</span>'; }
    $h .= '<h3>' . hc_e(hc_bd($d, 'form_heading', 'Book a Technician Visit')) . '</h3>';
    if (hc_bd($d, 'form_text') !== '') { $h .= '<p class="sub">' . hc_e(hc_bd($d, 'form_text')) . '</p>'; }
    $h .= hc_form('', hc_bd($d, 'form_name', 'Page form'), false, hc_bd($d, 'enquiry_item'), hc_bd($d, 'submit_text', null), hc_d_app($d), hc_bd($d, 'trip'));
    return $h . '</div></div></section>';
}

/** Contact boxes + text, buttons, map and form. */
function hc_block_contact($d)
{
    $h = hc_sec_open($d) . '<div class="wrap">';
    if (on(hc_bd($d, 'boxes_show', true))) {
        $h .= '<div class="c-boxes">';
        $h .= '<a class="c-box rv" href="' . hc_e(hc_tel()) . '" data-track="call"><div class="ic">' . hc_icon('phone') . '</div><h4>' . hc_e(s('mobile')) . '</h4><p>' . hc_e(hc_bd($d, 'call_note', 'Call for booking and questions')) . '</p></a>';
        $h .= '<a class="c-box wa rv" href="' . hc_e(hc_wa()) . '" target="_blank" rel="noopener" data-track="whatsapp"><div class="ic">' . hc_icon('wa') . '</div><h4>WhatsApp</h4><p>' . hc_e(hc_bd($d, 'wa_note', 'Send us a message on WhatsApp')) . '</p></a>';
        if (s('email', '') !== '') { $h .= '<a class="c-box rv" href="mailto:' . hc_e(s('email')) . '"><div class="ic">' . hc_icon('mail') . '</div><h4>' . hc_e(s('email')) . '</h4><p>' . hc_e(hc_bd($d, 'email_note', 'Write to us any time')) . '</p></a>'; }
        if (s('address', '') !== '') { $h .= '<div class="c-box rv"><div class="ic">' . hc_icon('pin') . '</div><h4>' . hc_e(s('address')) . '</h4><p>' . hc_e(hc_bd($d, 'address_note', '')) . '</p></div>'; }
        $h .= '</div>';
    }
    $h .= '<div class="c-grid"><div class="rv">' . hc_side_head($d);
    $h .= '<div class="hero-btns" style="margin-top:26px">' . hc_call_btn('btn btn-call btn-lg', hc_bd($d, 'call_text', 'Call Now')) . hc_wa_btn('btn btn-wa btn-lg', '', hc_bd($d, 'wa_text', 'WhatsApp')) . '</div>';
    if (on(hc_bd($d, 'hours_show', false)) && s('working_hours', '') !== '') { $h .= '<p class="hours">' . hc_icon('clock') . hc_e(s('working_hours')) . '</p>'; }
    if (on(hc_bd($d, 'map_show', true))) { $h .= hc_map_html(hc_bd($d, 'map_embed')); }
    $h .= '</div>';
    if (on(hc_bd($d, 'form_show', true))) {
        $h .= '<div class="c-form rv" id="book"><h3>' . hc_e(hc_bd($d, 'form_heading', 'Send Us Your Request')) . '</h3>';
        if (hc_bd($d, 'form_text') !== '') { $h .= '<p class="sub">' . hc_e(hc_bd($d, 'form_text')) . '</p>'; }
        $h .= hc_form('', hc_bd($d, 'form_name', 'Contact form'), false, hc_bd($d, 'enquiry_item'), hc_bd($d, 'submit_text', null), hc_d_app($d)) . '</div>';
    }
    return $h . '</div></div></section>';
}

function hc_block_map($d)
{
    $map = hc_map_html(hc_bd($d, 'map_embed'));
    if ($map === '') { return ''; }
    return hc_sec_open($d) . '<div class="wrap">' . hc_d_head($d) . '<div class="map-full rv">' . $map . '</div></div></section>';
}

/** Dark box with text and Book / WhatsApp buttons. */
function hc_block_custom_box($d)
{
    $item = hc_bd($d, 'enquiry_item');
    return '<section class="sec sec-tight"' . hc_anchor_attr($d) . '><div class="wrap"><div class="help-box rv" style="margin-top:0">'
        . '<div class="t"><b>' . hc_e(hc_bd($d, 'heading')) . '</b>' . (hc_bd($d, 'text') !== '' ? '<span>' . hc_e(hc_bd($d, 'text')) . '</span>' : '') . '</div>'
        . '<div class="btns">' . hc_button(hc_bd($d, 'button_text', 'Book Now'), hc_bd($d, 'button_link', 'popup'), 'btn btn-book btn-lg', $item, hc_d_app($d)) . hc_wa_btn('btn btn-wa btn-lg', $item) . '</div>'
        . '</div></div></section>';
}

function hc_block_gallery($d)
{
    $h = hc_sec_open($d) . '<div class="wrap">' . hc_d_head($d) . '<div class="hc-gallery">';
    foreach (hc_list(hc_bd($d, 'images', array())) as $img) {
        $path = is_array($img) ? hc_i($img, 'image') : $img;
        if ($path === '') { continue; }
        $alt = is_array($img) ? hc_i($img, 'alt') : '';
        $cap = is_array($img) ? hc_i($img, 'caption') : '';
        $h .= '<figure class="rv"><img src="' . hc_e(hc_img($path)) . '" alt="' . hc_e($alt !== '' ? $alt : $cap) . '" loading="lazy">' . ($cap !== '' ? '<figcaption>' . hc_e($cap) . '</figcaption>' : '') . '</figure>';
    }
    return $h . '</div></div></section>';
}

function hc_block_video($d)
{
    $id = hc_youtube_id(hc_bd($d, 'url'));
    if ($id === '') { return ''; }
    return hc_sec_open($d) . '<div class="wrap">' . hc_d_head($d)
        . '<div class="hc-video rv"><iframe src="https://www.youtube-nocookie.com/embed/' . hc_e($id) . '" title="' . hc_e(hc_plain_title(hc_bd($d, 'heading', 'Video'))) . '" loading="lazy" allow="accelerometer; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>'
        . '</div></section>';
}

function hc_block_html($d)
{
    $code = (string)hc_bd($d, 'code');
    if (on(hc_bd($d, 'wrap', true))) {
        return hc_sec_open($d) . '<div class="wrap">' . $code . '</div></section>';
    }
    return $code;
}

/** Collect FAQ questions on the page (for Google FAQ rich results). */
function hc_collect_faq($content)
{
    $out = array();
    foreach (array('blocks', 'extra_blocks') as $key) {
        foreach (hc_list($content[$key] ?? array()) as $b) {
            if (!is_array($b) || ($b['type'] ?? '') !== 'faq') { continue; }
            if (isset($b['data']['show']) && !on($b['data']['show'])) { continue; }
            foreach (hc_list($b['data']['items'] ?? array()) as $f) {
                if (hc_i($f, 'question', 0) !== '') { $out[] = array(hc_fill(hc_i($f, 'question', 0)), hc_fill(hc_i($f, 'answer', 1))); }
            }
        }
    }
    return $out;
}

/** Heading of a fixed page design (section{n}_small / _heading / _text). */
function hc_sec_head($n, $center = true)
{
    return hc_head_html(c('section' . $n . '_small', ''), c('section' . $n . '_heading', ''), c('section' . $n . '_text', ''), $center);
}

function hc_show($n)
{
    return on(c('section' . $n . '_show', true));
}
