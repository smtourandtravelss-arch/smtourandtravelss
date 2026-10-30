<?php
/* ---------------------------------------------------------------------
   EXTRA PAGE BUILDER SECTIONS (landing pages, offers, blog)
   --------------------------------------------------------------------- */

function hc_block_pricing($d)
{
    $h = hc_sec_open($d) . '<div class="wrap">' . hc_d_head($d) . '<div class="plans">';
    foreach (hc_list(hc_bd($d, 'items', array())) as $p) {
        $name = hc_i($p, 'name');
        if ($name === '') { continue; }
        $hl = on(isset($p['highlight']) ? $p['highlight'] : false);
        $h .= '<article class="plan rv' . ($hl ? ' hl' : '') . '">';
        if (hc_i($p, 'badge') !== '') { $h .= '<span class="plan-badge">' . hc_e(hc_i($p, 'badge')) . '</span>'; }
        $h .= '<h3>' . hc_e($name) . '</h3>';
        if (hc_i($p, 'price') !== '') {
            $h .= '<div class="plan-price"><b>' . hc_e(hc_price(hc_i($p, 'price'))) . '</b>' . (hc_i($p, 'note') !== '' ? '<small>' . hc_e(hc_i($p, 'note')) . '</small>' : '') . '</div>';
        }
        $lines = array_values(array_filter(array_map('trim', preg_split('/\r?\n/', hc_i($p, 'features')))));
        if ($lines) {
            $h .= '<ul class="ticks-sm">';
            foreach ($lines as $l) { $h .= '<li>' . hc_icon('check') . hc_e($l) . '</li>'; }
            $h .= '</ul>';
        }
        $link = hc_landing_link(hc_i($p, 'button_link') !== '' ? hc_i($p, 'button_link') : 'popup');
        $btnText = hc_i($p, 'button_text') !== '' ? hc_i($p, 'button_text') : 'Book Now';
        if ($link === 'popup') {
            // popup opens on the section's trip tab with this plan's car selected
            $item = hc_bd($d, 'enquiry_item') !== '' ? hc_bd($d, 'enquiry_item') : $name;
            $meta = $name . (hc_i($p, 'price') !== '' ? ' · ' . hc_price(hc_i($p, 'price')) . (hc_i($p, 'note') !== '' ? ' ' . hc_i($p, 'note') : '') : '');
            $h .= '<div class="plan-btn"><button type="button" class="btn ' . ($hl ? 'btn-book' : 'btn-ghost') . ' js-popup"'
                . hc_popup_attrs($item, hc_d_app($d), array('trip' => hc_bd($d, 'trip'), 'service' => hc_i($p, 'car'), 'meta' => $meta)) . '>' . hc_e($btnText) . '</button></div>';
        } else {
            $h .= '<div class="plan-btn">' . hc_button($btnText, $link, 'btn ' . ($hl ? 'btn-book' : 'btn-ghost'), $name, hc_d_app($d)) . '</div>';
        }
        $h .= '</article>';
    }
    $h .= '</div>';
    if (hc_bd($d, 'note') !== '') { $h .= '<p class="plans-note">' . hc_e(hc_bd($d, 'note')) . '</p>'; }
    return $h . '</div></section>';
}

function hc_block_logos($d)
{
    $items = array();
    foreach (hc_list(hc_bd($d, 'items', array())) as $it) {
        if (hc_i($it, 'image') !== '') { $items[] = $it; }
    }
    if (!$items) { return ''; }
    $h = hc_sec_open($d, 'sec-tight') . '<div class="wrap rv">';
    if (hc_bd($d, 'heading') !== '') { $h .= '<p class="logos-title">' . hc_e(hc_bd($d, 'heading')) . '</p>'; }
    $h .= '<div class="logos">';
    foreach ($items as $it) {
        $h .= '<img src="' . hc_e(hc_img(hc_i($it, 'image'))) . '" alt="' . hc_e(hc_i($it, 'alt')) . '" loading="lazy" height="56">';
    }
    return $h . '</div></div></section>';
}

function hc_block_countdown($d)
{
    $end = strtotime((string)hc_bd($d, 'ends_at'));
    if (!$end) { return ''; }
    $h = hc_sec_open($d) . '<div class="wrap">' . hc_d_head($d)
        . '<div class="countdown rv" data-end="' . hc_e(date('c', $end)) . '" data-ended="' . hc_e(hc_bd($d, 'ended_text', 'This offer has ended.')) . '">'
        . '<div><b data-u="d">00</b><small>Days</small></div><div><b data-u="h">00</b><small>Hours</small></div>'
        . '<div><b data-u="m">00</b><small>Minutes</small></div><div><b data-u="s">00</b><small>Seconds</small></div></div>';
    if (hc_bd($d, 'button_text') !== '') { $h .= '<div class="center mt">' . hc_button(hc_bd($d, 'button_text'), hc_bd($d, 'button_link', 'popup'), 'btn btn-book btn-lg', '', hc_d_app($d)) . '</div>'; }
    return $h . '</div></section>';
}

function hc_block_blog_posts($d)
{
    $posts = hc_posts(max(1, (int)hc_bd($d, 'limit', 3)), hc_bd($d, 'category_id', null) ?: null);
    if (!$posts) { return ''; }
    return hc_sec_open($d) . '<div class="wrap">' . hc_d_head($d) . hc_post_cards_html($posts) . '</div></section>';
}

/* ---------------------------------------------------------------------
   CAB SECTIONS
   --------------------------------------------------------------------- */

/** "One Way or Round Trip?" — 2+ trip cards, each button opens the popup on that trip tab. */
function hc_block_trip_options($d)
{
    $cards = '';
    foreach (hc_list(hc_bd($d, 'items', array())) as $t) {
        $title = hc_i($t, 'title');
        if ($title === '') { continue; }
        $hl = on(isset($t['highlight']) ? $t['highlight'] : false);
        $cards .= '<article class="trip-card rv' . ($hl ? ' featured' : '') . '">';
        if (hc_i($t, 'ribbon') !== '') { $cards .= '<span class="ribbon">' . hc_e(hc_i($t, 'ribbon')) . '</span>'; }
        $cards .= '<div class="trip-top"><span class="trip-ic">' . hc_icon(hc_i($t, 'icon', null, 'arrow')) . '</span><div><h3>' . hc_e($title) . '</h3>'
            . (hc_i($t, 'subtitle') !== '' ? '<p>' . hc_e(hc_fill(hc_i($t, 'subtitle'))) . '</p>' : '') . '</div></div>';
        $points = array_values(array_filter(array_map('trim', preg_split('/\r?\n/', (string)hc_i($t, 'points')))));
        if ($points) {
            $cards .= '<ul class="tick-list">';
            foreach ($points as $p) { $cards .= '<li>' . hc_icon('tick') . hc_e(hc_fill($p)) . '</li>'; }
            $cards .= '</ul>';
        }
        $btn = hc_i($t, 'button_text') !== '' ? hc_i($t, 'button_text') : 'Book ' . $title;
        $cards .= '<button type="button" class="btn ' . ($hl ? 'btn-book' : 'btn-call') . ' btn-block js-popup"' . hc_popup_attrs('', '', array('trip' => hc_i($t, 'trip'), 'meta' => $title)) . '>' . hc_e($btn) . hc_icon('arrow') . '</button>';
        $cards .= '</article>';
    }
    if ($cards === '') { return ''; }
    return hc_sec_open($d, '', 'trip', 'light') . '<div class="wrap">' . hc_d_head($d) . '<div class="trip-grid">' . $cards . '</div></div></section>';
}

/** Popular routes — each chip opens the popup with pickup + drop filled in. */
function hc_block_routes($d)
{
    $from = trim((string)hc_bd($d, 'from', s('city', '')));
    $trip = hc_bd($d, 'trip', '');
    $chips = '';
    foreach (hc_list(hc_bd($d, 'items', array())) as $r) {
        $to = trim((string)hc_i($r, 'to', 0));
        if ($to === '') { continue; }
        $name = ($from !== '' ? $from . ' → ' : '') . $to;
        $chips .= '<button type="button" class="route js-popup"' . hc_popup_attrs('', '', array('trip' => $trip, 'pickup' => $from, 'drop' => $to, 'meta' => 'Route: ' . $name)) . '>'
            . '<span>' . hc_e($name) . '</span>' . (hc_i($r, 'distance', 1) !== '' ? '<small>' . hc_e(hc_i($r, 'distance', 1)) . '</small>' : '') . '</button>';
    }
    if ($chips === '') { return ''; }
    $h = hc_sec_open($d, 'sec-routes', '', 'light') . '<div class="wrap">' . hc_d_head($d) . '<div class="routes rv">' . $chips . '</div>';
    if (hc_bd($d, 'note') !== '') { $h .= '<p class="routes-note">' . hc_e(hc_bd($d, 'note')) . '</p>'; }
    return $h . '</div></section>';
}

/**
 * Photo showcase — big photo + small photo + number badge on one side; heading, features,
 * client logos and booking buttons on the other (e.g. "Corporate Taxi").
 */
function hc_block_showcase($d)
{
    $right = hc_bd($d, 'image_position', 'left') === 'right';
    $h = hc_sec_open($d, 'showcase' . ($right ? ' rev' : '')) . '<div class="wrap sc-grid">';

    // photos
    $h .= '<div class="sc-media rv">';
    if (hc_bd($d, 'image') !== '') {
        $h .= '<div class="sc-main"><img src="' . hc_e(hc_img(hc_bd($d, 'image'))) . '" alt="' . hc_e(hc_bd($d, 'image_alt', hc_plain_title(hc_bd($d, 'heading')))) . '" loading="lazy" width="1000" height="667"></div>';
    }
    if (hc_bd($d, 'image_2') !== '') {
        $h .= '<div class="sc-small"><img src="' . hc_e(hc_img(hc_bd($d, 'image_2'))) . '" alt="' . hc_e(hc_bd($d, 'image_2_alt')) . '" loading="lazy" width="500" height="333"></div>';
    }
    if (hc_bd($d, 'badge_big') !== '') {
        $h .= '<div class="sc-badge">' . hc_icon(hc_bd($d, 'badge_icon', 'briefcase')) . '<div><b>' . hc_e(hc_bd($d, 'badge_big')) . '</b>' . (hc_bd($d, 'badge_text') !== '' ? '<small>' . hc_e(hc_bd($d, 'badge_text')) . '</small>' : '') . '</div></div>';
    }
    $h .= '</div>';

    // text
    $h .= '<div class="sc-body rv">' . hc_side_head($d, false);
    $feats = '';
    foreach (hc_list(hc_bd($d, 'items', array())) as $f) {
        if (hc_i($f, 'title', 1) === '') { continue; }
        $feats .= '<div class="sc-feat"><span class="ic">' . hc_icon(hc_i($f, 'icon', 0, 'check')) . '</span><div><h4>' . hc_e(hc_i($f, 'title', 1)) . '</h4>'
            . (hc_i($f, 'text', 2) !== '' ? '<p>' . hc_e(hc_fill(hc_i($f, 'text', 2))) . '</p>' : '') . '</div></div>';
    }
    if ($feats !== '') { $h .= '<div class="sc-feats">' . $feats . '</div>'; }

    $logos = '';
    foreach (hc_list(hc_bd($d, 'logos', array())) as $l) {
        if (hc_i($l, 'image') === '') { continue; }
        $logos .= '<span><img src="' . hc_e(hc_img(hc_i($l, 'image'))) . '" alt="' . hc_e(hc_i($l, 'alt')) . '" loading="lazy" height="34"></span>';
    }
    if ($logos !== '') {
        $h .= '<div class="sc-logos">' . (hc_bd($d, 'logos_title') !== '' ? '<small>' . hc_e(hc_bd($d, 'logos_title')) . '</small>' : '') . '<div>' . $logos . '</div></div>';
    }

    $btns = '';
    if (hc_bd($d, 'button_text') !== '') {
        $link = hc_landing_link(hc_bd($d, 'button_link', 'popup'));
        $item = hc_bd($d, 'enquiry_item');
        if ($link === 'popup') {
            $btns .= '<button type="button" class="btn btn-book btn-lg js-popup"' . hc_popup_attrs($item, '', array('trip' => hc_bd($d, 'trip'), 'meta' => $item !== '' ? $item : hc_plain_title(hc_bd($d, 'heading')))) . '>' . hc_e(hc_bd($d, 'button_text')) . hc_icon('arrow') . '</button>';
        } else {
            $btns .= hc_button(hc_bd($d, 'button_text'), $link, 'btn btn-book btn-lg');
        }
    }
    if (on(hc_bd($d, 'show_call', true))) { $btns .= hc_call_btn('btn btn-call btn-lg', hc_bd($d, 'call_text', 'Call {phone}')); }
    if (hc_bd($d, 'wa_text') !== '') { $btns .= hc_wa_btn('btn btn-wa btn-lg', hc_bd($d, 'enquiry_item'), hc_bd($d, 'wa_text')); }
    if ($btns !== '') { $h .= '<div class="sc-btns">' . $btns . '</div>'; }

    return $h . '</div></div></section>';
}

/** Trust factor — numbers, client logos and the company details from Site Settings. */
function hc_block_trust($d)
{
    $h = hc_sec_open($d, '', 'contact', 'light') . '<div class="wrap">' . hc_d_head($d);
    $stats = '';
    foreach (hc_list(hc_bd($d, 'stats', array())) as $st) {
        if (hc_i($st, 'value', 0) === '') { continue; }
        $stats .= '<div class="stat rv"><b>' . hc_e(hc_i($st, 'value', 0)) . '</b><span>' . hc_e(hc_i($st, 'label', 1)) . '</span></div>';
    }
    if ($stats !== '') { $h .= '<div class="stats-grid trust-stats">' . $stats . '</div>'; }

    $h .= '<div class="trust-grid">';
    // Left: clients
    if (on(hc_bd($d, 'clients_show', true))) {
        $many = count(hc_list(hc_bd($d, 'logos', array()))) > 9;
        $h .= '<div class="trust-card rv' . ($many ? ' wide' : '') . '"><h3>' . hc_icon('award') . hc_e(hc_bd($d, 'clients_title', 'Trusted by businesses')) . '</h3>';
        if (hc_bd($d, 'clients_text') !== '') { $h .= '<p class="muted">' . hc_e(hc_bd($d, 'clients_text')) . '</p>'; }
        $logos = '';
        foreach (hc_list(hc_bd($d, 'logos', array())) as $l) {
            if (hc_i($l, 'image') === '') { continue; }
            $logos .= '<span><img src="' . hc_e(hc_img(hc_i($l, 'image'))) . '" alt="' . hc_e(hc_i($l, 'alt')) . '" loading="lazy" height="44"></span>';
        }
        if ($logos !== '') { $h .= '<div class="client-logos">' . $logos . '</div>'; }
        $h .= '</div>';
    }
    // Right: company details
    $h .= '<div class="trust-card company rv"><h3>' . hc_icon('building') . hc_e(hc_bd($d, 'company_title', 'Company details')) . '</h3><ul class="info-list">';
    $legal = s('legal_name', '') !== '' ? s('legal_name') : s('business_name');
    $h .= '<li>' . hc_icon('building') . '<div><b>' . hc_e($legal) . '</b>' . (hc_bd($d, 'company_note') !== '' ? '<small>' . hc_e(hc_bd($d, 'company_note')) . '</small>' : '') . '</div></li>';
    if (s('address', '') !== '') { $h .= '<li>' . hc_icon('pin') . '<div><b>' . hc_e(hc_bd($d, 'address_label', 'Office address')) . '</b><small>' . hc_e(s('address')) . '</small></div></li>'; }
    $phones = '<a href="' . hc_e(hc_tel()) . '" data-track="call">' . hc_e(s('mobile')) . '</a>';
    if (s('mobile_2', '') !== '') { $phones .= ' · <a href="' . hc_e(hc_tel_for(s('mobile_2'))) . '" data-track="call">' . hc_e(s('mobile_2')) . '</a>'; }
    $h .= '<li>' . hc_icon('phone') . '<div><b>Phone</b><small>' . $phones . '</small></div></li>';
    if (s('email', '') !== '') { $h .= '<li>' . hc_icon('mail') . '<div><b>Email</b><small><a href="mailto:' . hc_e(s('email')) . '">' . hc_e(s('email')) . '</a></small></div></li>'; }
    if (s('working_hours', '') !== '') { $h .= '<li>' . hc_icon('clock') . '<div><b>Working hours</b><small>' . hc_e(s('working_hours')) . '</small></div></li>'; }
    $h .= '</ul></div></div>';
    return $h . '</div></section>';
}

function hc_block_spacer($d)
{
    $px = max(0, min(300, (int)hc_bd($d, 'height', 40)));
    return '<div class="spacer" style="height:' . $px . 'px">' . (on(hc_bd($d, 'line', false)) ? '<div class="wrap"><hr></div>' : '') . '</div>';
}
