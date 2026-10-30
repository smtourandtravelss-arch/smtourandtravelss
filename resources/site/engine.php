<?php
/* =====================================================================
   WEBSITE ENGINE — helper functions used by the page designs.
   Content comes from the database (Admin panel). The design (HTML/CSS)
   is the same as the original website.
   ===================================================================== */

use App\Models\Service;

/* ---------------------------------------------------------------------
   SETTINGS / CONTENT READERS
   --------------------------------------------------------------------- */
function s($key, $default = '')
{
    $set = $GLOBALS['HC']['settings'];
    if (strpos($key, '.') !== false) {
        list($a, $b) = explode('.', $key, 2);
        return (isset($set[$a]) && is_array($set[$a]) && array_key_exists($b, $set[$a]) && $set[$a][$b] !== null) ? $set[$a][$b] : $default;
    }
    return (array_key_exists($key, $set) && $set[$key] !== null) ? $set[$key] : $default;
}

function c($key, $default = '')
{
    $con = $GLOBALS['HC']['content'];
    return hc_fill((array_key_exists($key, $con) && $con[$key] !== null) ? $con[$key] : $default);
}

// {phone} {email} {address} {business} {city} {year} in any text = value from settings
function hc_fill($v)
{
    if (is_array($v)) { return array_map('hc_fill', $v); }
    if (!is_string($v) || strpos($v, '{') === false) { return $v; }
    return str_replace(
        array('{phone}', '{email}', '{address}', '{business}', '{city}', '{year}'),
        array((string)s('mobile'), (string)s('email'), (string)s('address'), (string)s('business_name'), (string)s('city'), date('Y')),
        $v
    );
}

function on($value)
{
    return $value === true || $value === 1 || $value === '1' || $value === 'true' || $value === 'yes' || $value === 'on';
}

function hc_e($text)
{
    return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
}

function hc_list($v)
{
    return is_array($v) ? array_values($v) : array();
}

/** Read a value from a list row that is either ['key' => …] or [0 => …]. */
function hc_i($row, $key, $index = null, $default = '')
{
    if (is_string($row) && ($index === 0 || $index === null)) { return $row; }
    if (!is_array($row)) { return $default; }
    if (array_key_exists($key, $row) && $row[$key] !== null) { return $row[$key]; }
    if ($index !== null && array_key_exists($index, $row) && $row[$index] !== null) { return $row[$index]; }
    return $default;
}

/** Simple list of strings from a repeater (['a','b'] or [['text'=>'a']]). */
function hc_strings($v, $key = 'text')
{
    $out = array();
    foreach (hc_list($v) as $row) {
        $t = is_array($row) ? hc_i($row, $key, 0, '') : $row;
        if (is_array($t)) { continue; }
        if (trim((string)$t) !== '') { $out[] = (string)$t; }
    }
    return $out;
}

/* ---------------------------------------------------------------------
   URLS
   --------------------------------------------------------------------- */
function hc_base()
{
    static $base = null;
    if ($base === null) {
        $base = rtrim((string)request()->getBaseUrl(), '/');
        if (substr($base, -10) === '/index.php') { $base = substr($base, 0, -10); }
    }
    return $base;
}

function hc_site_url()
{
    $u = trim((string)s('website_url', ''));
    if ($u !== '') { return rtrim($u, '/'); }
    return rtrim(request()->getSchemeAndHttpHost() . hc_base(), '/');
}

function hc_url($link = '')
{
    $link = trim((string)$link);
    if (preg_match('~^(https?:|mailto:|tel:|#|//)~i', $link)) { return $link; }
    $link = trim($link, '/');
    if ($link === 'home' || $link === 'index') { $link = ''; }
    return hc_base() . '/' . $link;
}

function hc_abs($link = '')
{
    $link = trim((string)$link, '/');
    if ($link === 'home') { $link = ''; }
    return hc_site_url() . '/' . $link;
}

function hc_img($path)
{
    $path = trim((string)$path);
    if ($path === '' || preg_match('~^(https?:)?//~i', $path)) { return $path; }
    $file = public_path(ltrim($path, '/'));
    $v = is_file($file) ? '?v=' . filemtime($file) : '';
    return hc_base() . '/' . ltrim($path, '/') . $v;
}

function hc_abs_img($path)
{
    $path = trim((string)$path);
    if ($path === '' || preg_match('~^https?://~i', $path)) { return $path; }
    return hc_site_url() . '/' . ltrim($path, '/');
}

function hc_asset($path)
{
    $file = public_path($path);
    $v = is_file($file) ? filemtime($file) : '1';
    return hc_base() . '/' . $path . '?v=' . $v;
}

/** CSS background-image style for a photo. */
function hc_bg($path)
{
    $path = trim((string)$path);
    if ($path === '') { return ''; }
    return ' style="background-image:url(\'' . hc_e(str_replace(array("'", '"', '(', ')'), '', hc_img($path))) . '\')"';
}

/* ---------------------------------------------------------------------
   PHONE / WHATSAPP
   --------------------------------------------------------------------- */
function hc_digits($v)
{
    return preg_replace('/\D+/', '', (string)$v);
}

function hc_tel()
{
    $d = hc_digits(s('mobile'));
    if (strlen($d) === 11 && $d[0] === '0') { $d = substr($d, 1); }
    if (strlen($d) === 10) { $d = '91' . $d; }
    return 'tel:+' . $d;
}

/** WhatsApp number without country code (shown as "WhatsApp: 98xxxxxxx"). */
function hc_wa_short()
{
    $num = hc_digits(s('whatsapp_number'));
    if (strlen($num) === 12 && strpos($num, '91') === 0) { $num = substr($num, 2); }
    return $num;
}

function hc_wa($item = '', $message = null)
{
    $num = hc_digits(s('whatsapp_number'));
    if (strlen($num) === 10) { $num = '91' . $num; }
    if ($message !== null && $message !== '') {
        $msg = str_replace('{item}', (string)$item, (string)$message);
    } else {
        $msg = ($item !== '' && $item !== null)
            ? str_replace('{item}', $item, (string)s('whatsapp_card_message', 'Hi, I need help with: {item}. Please call me back.'))
            : (string)s('whatsapp_message', 'Hi, I need more information.');
    }
    return 'https://wa.me/' . $num . '?text=' . rawurlencode(hc_fill($msg));
}

/**
 * WhatsApp link that carries ALL form details (Site Settings → Enquiry Form → After the form is sent).
 * $rows = [[label, value], …]   $vars = name, mobile, item, page, form
 */
function hc_wa_details_url($rows, $vars = array())
{
    $num = hc_digits(s('form_action.wa_number', '') !== '' ? s('form_action.wa_number') : s('whatsapp_number'));
    if (strlen($num) === 10) { $num = '91' . $num; }
    $lines = array();
    foreach ($rows as $r) {
        if (isset($r[1]) && trim((string)$r[1]) !== '') { $lines[] = '*' . $r[0] . ':* ' . $r[1]; }
    }
    $tpl = (string)s('form_action.wa_template', "Hi {business}, I am {name}.\nI want to enquire{for}.\n\n{details}");
    $item = isset($vars['item']) ? $vars['item'] : '';
    $msg = str_replace(
        array('{business}', '{name}', '{mobile}', '{item}', '{for}', '{page}', '{details}'),
        array(s('business_name', ''), isset($vars['name']) ? $vars['name'] : '', isset($vars['mobile']) ? $vars['mobile'] : '', $item,
            $item !== '' ? ' about ' . $item : '', isset($vars['page']) ? $vars['page'] : '', implode("\n", $lines)),
        $tpl
    );
    return 'https://wa.me/' . $num . '?text=' . rawurlencode(trim($msg));
}

/* ---------------------------------------------------------------------
   SERVICE CARDS (from Admin → Services)
   --------------------------------------------------------------------- */
function hc_cards($all = false)
{
    if (!isset($GLOBALS['HC']['cards'])) {
        $GLOBALS['HC']['cards'] = Service::query()->orderBy('sort_order')->orderBy('id')->get()
            ->map(fn ($s) => $s->toCard())->all();
    }
    if ($all) { return $GLOBALS['HC']['cards']; }
    return array_values(array_filter($GLOBALS['HC']['cards'], function ($c) {
        return on($c['show']);
    }));
}

/** Cards of one category (appliance). Empty category = all cards. */
function hc_cards_of($category = '')
{
    $category = trim((string)$category);
    $cards = hc_cards();
    if ($category === '') { return $cards; }
    return array_values(array_filter($cards, function ($cd) use ($category) {
        return isset($cd['category']) && mb_strtolower(trim((string)$cd['category']), 'UTF-8') === mb_strtolower($category, 'UTF-8');
    }));
}

/** Value used in the "Select Your Service" list: "Category|Title". */
function hc_card_value($card)
{
    $cat = trim((string)(isset($card['category']) ? $card['category'] : ''));
    return $cat !== '' ? $cat . '|' . $card['title'] : $card['title'];
}

/** Readable name: "Title (Category)" — the category is left out when the title already has it. */
function hc_card_name($card)
{
    $cat = trim((string)(isset($card['category']) ? $card['category'] : ''));
    $title = (string)$card['title'];
    if ($cat === '' || mb_stripos($title, $cat, 0, 'UTF-8') !== false) { return $title; }
    return $title . ' (' . $cat . ')';
}

/** "Category|Title" → "Title (Category)" (anything else is returned as it is). */
function hc_service_label($value)
{
    $value = trim((string)$value);
    if (strpos($value, '|') === false) { return $value; }
    list($cat, $title) = explode('|', $value, 2);
    return hc_card_name(array('category' => $cat, 'title' => $title));
}

function hc_find_card($title)
{
    $title = trim(mb_strtolower((string)$title, 'UTF-8'));
    if ($title === '') { return null; }
    foreach (hc_cards() as $card) {
        foreach (array($card['title'], hc_card_name($card), hc_card_value($card)) as $name) {
            if (trim(mb_strtolower($name, 'UTF-8')) === $title) { return $card; }
        }
    }
    return null;
}

function hc_inr($n)
{
    $n = (string)round((float)$n);
    $last3 = substr($n, -3);
    $rest = substr($n, 0, -3);
    if ($rest !== '' && $rest !== false) {
        $rest = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest);
        return $rest . ',' . $last3;
    }
    return $last3;
}

function hc_price($p)
{
    $p = trim((string)$p);
    if ($p === '') { return ''; }
    $clean = str_replace(array(',', ' '), '', $p);
    return is_numeric($clean) ? '₹' . hc_inr($clean) : $p;
}

/** Short price text for the popup / email ("Starting from ₹499"). */
function hc_card_price_text($card)
{
    $price = isset($card['price']) ? hc_price($card['price']) : '';
    if ($price === '') { return ''; }
    $pre = !empty($card['price_prefix']) ? $card['price_prefix'] . ' ' : '';
    $note = !empty($card['price_note']) ? ' ' . $card['price_note'] : '';
    return trim($pre . $price . $note);
}

function hc_card_details($card)
{
    $parts = array();
    if (!empty($card['category'])) { $parts[] = $card['category']; }
    if (!empty($card['badge'])) { $parts[] = $card['badge']; }
    if (hc_card_price_text($card) !== '') { $parts[] = hc_card_price_text($card); }
    return implode(' · ', $parts);
}

/* ---------------------------------------------------------------------
   TEXT HELPERS
   --------------------------------------------------------------------- */
// Paragraph text: an empty line = new paragraph. Simple tags allowed.
function hc_paras($text, $class = '', $attr = '')
{
    $text = trim(str_replace("\r", '', (string)$text));
    if ($text === '') { return ''; }
    $out = '';
    foreach (preg_split('/\n\s*\n/', $text) as $p) {
        $p = strip_tags(trim($p), '<b><strong><i><em><a><br><span><u>');
        $out .= '<p' . ($class ? ' class="' . $class . '"' : '') . $attr . '>' . nl2br($p, false) . '</p>';
    }
    return $out;
}

// Heading with [highlighted words] in brand colour.
function hc_title($text)
{
    $t = hc_e($text);
    return preg_replace('/\[(.+?)\]/', '<em>$1</em>', $t);
}

// Big banner heading: [second line] shows on its own line in the orange gradient.
function hc_hero_title($text)
{
    $t = hc_e($text);
    return preg_replace('/\s*\[(.+?)\]/', ' <span>$1</span>', $t);
}

function hc_plain_title($text)
{
    return str_replace(array('[', ']'), '', (string)$text);
}

/** Rich HTML from the editor — keeps safe tags only. */
function hc_rich($html)
{
    $html = hc_fill((string)$html);
    $html = strip_tags($html, '<h2><h3><h4><p><ul><ol><li><b><strong><i><em><a><br><u><s><blockquote><img><figure><figcaption><table><thead><tbody><tr><th><td><hr><span><div>');
    // remove inline event handlers and javascript: links
    $html = preg_replace('/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
    $html = preg_replace('/(href|src)\s*=\s*(["\'])\s*javascript:[^"\']*\2/i', '$1="#"', $html);
    if (hc_landing()) {
        // Landing pages never send the visitor away: only #anchors and tel: links stay
        $html = preg_replace_callback('~<a\b([^>]*)>(.*?)</a>~is', function ($m) {
            return preg_match('~href\s*=\s*(["\'])\s*(#|tel:)~i', $m[1]) ? $m[0] : $m[2];
        }, $html);
    }
    return $html;
}

/** True while a Landing Page is being rendered (no menu, no diverting links). */
function hc_landing()
{
    return !empty($GLOBALS['HC']['landing']);
}

/** On landing pages every link except #anchor / tel: / WhatsApp becomes "open the booking popup". */
function hc_landing_link($link)
{
    $link = trim((string)$link);
    if ($link === '' || $link === 'popup' || !hc_landing()) { return $link; }
    return preg_match('~^(#|tel:|https?://(wa\.me|api\.whatsapp\.com)/)~i', $link) ? $link : 'popup';
}

/* ---------------------------------------------------------------------
   ICONS (SVG sprite — resources/site/icons.php)
   --------------------------------------------------------------------- */
function hc_icon($name)
{
    $name = trim((string)$name);
    $alias = array('whatsapp' => 'wa', 'calendar' => 'cal', 'email' => 'mail');
    if (isset($alias[$name])) { $name = $alias[$name]; }
    if ($name === '') { return ''; }
    if (\App\Support\Icons::exists($name)) {
        return '<svg aria-hidden="true"><use href="#i-' . $name . '"/></svg>';
    }
    // anything else (e.g. an emoji typed by the admin) is shown as text
    return '<span class="emo" aria-hidden="true">' . hc_e($name) . '</span>';
}

/* ---------------------------------------------------------------------
   LINKS & BUTTONS
   'popup' = opens booking popup, '#section' = scroll, 'page-name' = page, '' = no link
   --------------------------------------------------------------------- */
function hc_popup_attrs($item = '', $appliance = '', $extra = array())
{
    $h = ' data-item="' . hc_e($item) . '" data-appliance="' . hc_e($appliance) . '"';
    foreach ($extra as $k => $v) {
        if ($v !== '' && $v !== null) { $h .= ' data-' . $k . '="' . hc_e($v) . '"'; }
    }
    return $h;
}

function hc_button($text, $link, $class = 'btn btn-book', $item = '', $appliance = '', $icon = '')
{
    $link = hc_landing_link($link);
    if ($text === '' || $text === null) { return ''; }
    $ic = $icon !== '' ? hc_icon($icon) : '';
    if ($link === 'popup') {
        return '<button type="button" class="' . $class . ' js-popup"' . hc_popup_attrs($item, $appliance) . '>' . $ic . hc_e($text) . '</button>';
    }
    if ($link === '') { return ''; }
    $ext = preg_match('~^https?://~i', $link) && strpos($link, hc_site_url()) !== 0;
    return '<a class="' . $class . '" href="' . hc_e(hc_url($link)) . '"' . ($ext ? ' target="_blank" rel="noopener"' : '') . '>' . $ic . hc_e($text) . '</a>';
}

function hc_box_link($link, $item = '', $class = '')
{
    $link = hc_landing_link($link);
    if ($link === 'popup') {
        return array('<button type="button" class="' . $class . ' is-link js-popup"' . hc_popup_attrs($item) . '>', '</button>');
    }
    if ($link !== '') {
        return array('<a class="' . $class . ' is-link" href="' . hc_e(hc_url($link)) . '">', '</a>');
    }
    return array('<div class="' . $class . '">', '</div>');
}

function hc_call_btn($class = 'btn btn-call', $text = null)
{
    $text = $text === null ? s('buttons.call_text', 'Call Now') : hc_fill($text);
    return '<a href="' . hc_e(hc_tel()) . '" class="' . $class . '" data-track="call">' . hc_icon('phone') . hc_e($text) . '</a>';
}

function hc_wa_btn($class = 'btn btn-wa', $item = '', $text = null, $message = null)
{
    $text = $text === null ? s('buttons.whatsapp_text', 'WhatsApp') : $text;
    return '<a href="' . hc_e(hc_wa($item, $message)) . '" target="_blank" rel="noopener" class="' . $class . '" data-track="whatsapp">' . hc_icon('wa') . hc_e($text) . '</a>';
}

/* ---------------------------------------------------------------------
   FORMS (one field list for every form — Admin → Site Settings → Form)
   --------------------------------------------------------------------- */
function hc_secret()
{
    return hash('sha256', (string)config('app.key') . '|hc-form-key');
}

function hc_token()
{
    $t = time();
    return $t . '.' . substr(hash_hmac('sha256', (string)$t, hc_secret()), 0, 24);
}

function hc_form_fields()
{
    $fields = array();
    foreach (hc_list(s('form_fields', array())) as $f) {
        if (!is_array($f) || empty($f['name'])) { continue; }
        $f['name'] = preg_replace('/[^a-z0-9_]/i', '', $f['name']);
        if ($f['name'] === '' || in_array($f['name'], array('form_name', 'enquiry_item', 'page_url', 'hc_ts', 'website', 'ajax'), true)) { continue; }
        $f['label'] = isset($f['label']) && $f['label'] !== '' ? $f['label'] : ucfirst($f['name']);
        $f['type'] = isset($f['type']) && $f['type'] ? strtolower($f['type']) : 'text';
        $f['required'] = isset($f['required']) && on($f['required']);
        $fields[] = $f;
    }
    if (!$fields) {
        $fields = array(
            array('name' => 'name', 'label' => 'Your Name', 'type' => 'text', 'required' => true),
            array('name' => 'mobile', 'label' => 'Mobile Number', 'type' => 'tel', 'required' => true),
        );
    }
    return $fields;
}

/** Default error text of a field (shown under the box). */
function hc_field_error($f)
{
    if (isset($f['error']) && trim((string)$f['error']) !== '') { return (string)$f['error']; }
    switch ($f['type']) {
        case 'tel': return 'Please enter a valid 10-digit mobile number';
        case 'email': return 'Please enter a valid email address';
        case 'date': return 'Please choose today or a future date';
        case 'time': return 'Please choose a time';
        case 'select': return 'Please select an option';
        case 'trip': return 'Please choose a trip type';
        case 'number': return 'Please enter a valid number';
    }
    return $f['name'] === 'name' ? 'Please enter your full name' : 'Please fill this field';
}

/** The "trip type" tabs field (One Way / Round Trip / …), or null when the form has none. */
function hc_trip_field()
{
    foreach (hc_form_fields() as $f) {
        if ($f['type'] === 'trip') { return $f; }
    }
    return null;
}

/** Trip types this field is shown for (empty = always shown). */
function hc_field_show_for($f)
{
    $v = isset($f['show_for']) ? $f['show_for'] : '';
    $list = is_array($v) ? $v : preg_split('/[\n,|]+/', (string)$v);
    return array_values(array_filter(array_map('trim', $list), 'strlen'));
}

/** True when the field belongs to the chosen trip type. */
function hc_field_applies($f, $trip)
{
    $for = hc_field_show_for($f);
    if (!$for || $trip === '') { return true; }
    foreach ($for as $t) {
        if (strcasecmp($t, $trip) === 0) { return true; }
    }
    return false;
}

/** Field "length" limits for text boxes. */
function hc_field_limits($f)
{
    $min = isset($f['min']) && is_numeric($f['min']) ? max(0, (int)$f['min']) : ($f['required'] ? 1 : 0);
    $max = isset($f['max']) && is_numeric($f['max']) && (int)$f['max'] > 0 ? (int)$f['max'] : ($f['type'] === 'textarea' ? 2000 : 150);
    return array($min, $max);
}

/** True when this field is the "Select your service" list built from Admin → Services. */
function hc_is_service_field($f)
{
    if ($f['type'] !== 'select') { return false; }
    $o = isset($f['options']) && is_string($f['options']) ? strtolower(trim($f['options'])) : '';
    return in_array($o, array('services', 'cards', 'packages'), true);
}

function hc_select_options($f)
{
    $opts = isset($f['options']) ? $f['options'] : array();
    if (is_string($opts)) {
        $opts = array_values(array_filter(array_map('trim', preg_split('/[\n,]+/', trim($opts))), 'strlen'));
    }
    return hc_list($opts);
}

/** <option> list for the service field: grouped by category, or one category only. */
function hc_service_options($category = '', $selected = '')
{
    $cards = hc_cards_of($category);
    $h = '';
    $opt = function ($card) use ($selected) {
        $v = hc_card_value($card);
        return '<option value="' . hc_e($v) . '"' . ($selected !== '' && ($selected === $v || $selected === $card['title']) ? ' selected' : '') . '>' . hc_e($card['title']) . '</option>';
    };
    if (trim((string)$category) !== '') {
        foreach ($cards as $card) { $h .= $opt($card); }
        return $h;
    }
    $groups = array();
    foreach ($cards as $card) {
        $cat = trim((string)(isset($card['category']) ? $card['category'] : ''));
        $groups[$cat][] = $card;
    }
    if (isset($groups[''])) {
        foreach ($groups[''] as $card) { $h .= $opt($card); }
        unset($groups['']);
    }
    foreach ($groups as $cat => $list) {
        $h .= '<optgroup label="' . hc_e($cat) . '">';
        foreach ($list as $card) { $h .= $opt($card); }
        $h .= '</optgroup>';
    }
    return $h;
}

/**
 * One enquiry form.
 * $popup = true for the booking popup (other button colour/text).
 * $category = show only this appliance in the service list (null = page setting).
 */
function hc_form($id, $name, $popup = false, $item = '', $submitText = null, $category = null, $startTrip = '')
{
    static $n = 0;
    $n++;
    if ($category === null) { $category = trim((string)c('appliance', '')); }
    if ($submitText === null || $submitText === '') {
        $submitText = $popup ? s('buttons.popup_text', 'Confirm Booking') : s('buttons.submit_text', 'Book Repair Now');
    }
    $h = '<form class="lead-form" action="' . hc_e(hc_url('send')) . '" method="post" novalidate' . ($id ? ' id="' . hc_e($id) . '"' : '') . '>';
    $h .= '<div class="hp" aria-hidden="true"><label>Leave this empty<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>';
    $h .= '<input type="hidden" name="hc_ts" value="' . hc_e(hc_token()) . '">';
    $h .= '<input type="hidden" name="form_name" value="' . hc_e($name) . '">';
    $h .= '<input type="hidden" name="enquiry_item" value="' . hc_e($item) . '">';
    $h .= '<input type="hidden" name="page_url" value="">';
    if ($popup) { $h .= '<input type="hidden" name="hc_short" value="1">'; }

    // Trip type tabs (One Way / Round Trip / Local / Airport) — always at the top of the form
    $tripField = hc_trip_field();
    $trips = $tripField ? array_map('strval', hc_select_options($tripField)) : array();
    // the form opens on $startTrip when it is one of the tabs, else on the first tab
    $trip = $trips ? (in_array((string)$startTrip, $trips, true) ? (string)$startTrip : $trips[0]) : '';
    if ($trips) {
        $h .= '<div class="trip-tabs" role="tablist" aria-label="' . hc_e($tripField['label']) . '">';
        foreach ($trips as $k => $t) {
            $h .= '<button type="button" class="tab' . ($t === $trip ? ' active' : '') . '" role="tab" aria-selected="' . ($t === $trip ? 'true' : 'false') . '" data-trip="' . hc_e($t) . '">' . hc_e($t) . '</button>';
        }
        $h .= '</div><input type="hidden" name="' . hc_e($tripField['name']) . '" value="' . hc_e($trip) . '" data-trip-input>';
    }

    $h .= '<div class="fields">';
    foreach (hc_form_fields() as $f) {
        if ($f['type'] === 'trip') { continue; }
        if ($popup && isset($f['short_form']) && !on($f['short_form'])) { continue; }
        $fid = 'f' . $n . '-' . $f['name'];
        $err = hc_field_error($f);
        $req = $f['required'] ? ' data-req="1" required' : '';
        $ph = isset($f['placeholder']) && $f['placeholder'] !== '' ? ' placeholder="' . hc_e($f['placeholder']) . '"' : '';
        $def = isset($f['default']) ? trim((string)$f['default']) : '';
        $common = ' id="' . $fid . '" name="' . $f['name'] . '"' . $req . ' data-err="' . hc_e($err) . '"';
        list($min, $max) = hc_field_limits($f);
        $showFor = hc_field_show_for($f);
        $cls = 'field' . (on(isset($f['half']) ? $f['half'] : false) ? ' half' : '') . (hc_field_applies($f, $trip) ? '' : ' is-off');
        $h .= '<div class="' . $cls . '"' . ($showFor ? ' data-show-for="' . hc_e(implode('|', $showFor)) . '"' : '') . '><label for="' . $fid . '">' . hc_e($f['label']) . ($f['required'] ? ' <i>*</i>' : '') . '</label>';
        $icon = isset($f['icon']) ? trim((string)$f['icon']) : '';
        if ($icon !== '' && in_array($f['type'], array('text', 'select'), true)) { $h .= '<div class="input-ic">' . hc_icon($icon); }
        switch ($f['type']) {
            case 'time':
                $h .= '<input type="time"' . $common . ' data-v="time"' . (preg_match('/^\d{2}:\d{2}$/', $def) ? ' value="' . hc_e($def) . '"' : '') . '>';
                break;
            case 'textarea':
                $h .= '<textarea' . $common . ' rows="3" data-v="text" data-min="' . $min . '" data-max="' . $max . '" maxlength="' . $max . '"' . $ph . '></textarea>';
                break;
            case 'select':
                $isSvc = hc_is_service_field($f);
                $h .= '<select' . $common . ' data-v="' . ($isSvc ? 'service' : 'select') . '">';
                $h .= '<option value="">' . hc_e(isset($f['placeholder']) && $f['placeholder'] !== '' ? $f['placeholder'] : 'Select') . '</option>';
                if ($isSvc) {
                    $h .= hc_service_options($category, $item);
                } else {
                    foreach (hc_select_options($f) as $o) {
                        $h .= '<option' . ($item !== '' && $o === $item ? ' selected' : '') . '>' . hc_e($o) . '</option>';
                    }
                }
                $h .= '</select>';
                break;
            case 'tel':
                $h .= '<div class="mob-wrap"><span>+91</span><input type="tel" inputmode="numeric" maxlength="10" autocomplete="tel-national"' . $common . ' data-v="mobile"' . $ph . '></div>';
                break;
            case 'email':
                $h .= '<input type="email" maxlength="100" autocomplete="email"' . $common . ' data-v="email"' . $ph . '>';
                break;
            case 'date':
                $h .= '<input type="date" min="' . date('Y-m-d') . '"' . $common . ' data-v="date"' . (strtolower($def) === 'today' ? ' data-today="1"' : '') . '>';
                break;
            case 'number':
                $h .= '<input type="text" inputmode="numeric" maxlength="6"' . $common . ' data-v="number"' . (isset($f['min']) && is_numeric($f['min']) ? ' data-min="' . (int)$f['min'] . '"' : '') . (isset($f['max']) && is_numeric($f['max']) ? ' data-max="' . (int)$f['max'] . '"' : '') . $ph . '>';
                break;
            default:
                if ($f['name'] === 'name') {
                    $h .= '<input type="text" maxlength="50" autocomplete="name"' . $common . ' data-v="name"' . $ph . '>';
                } else {
                    $h .= '<input type="text" maxlength="' . $max . '" autocomplete="off"' . $common . ' data-v="text" data-min="' . $min . '" data-max="' . $max . '"' . $ph . ($def !== '' ? ' value="' . hc_e($def) . '"' : '') . '>';
                }
        }
        if ($icon !== '' && in_array($f['type'], array('text', 'select'), true)) { $h .= '</div>'; }
        $h .= '<div class="err">' . hc_e($err) . '</div></div>';
    }
    $h .= '</div>';
    $h .= '<button type="submit" class="btn btn-lg ' . ($popup ? 'btn-popup' : 'btn-submit') . '" data-sending="' . hc_e(s('buttons.sending_text', 'Sending…')) . '">' . hc_e($submitText) . hc_icon('arrow') . '</button>';
    $h .= '<div class="form-msg" role="alert"></div>';
    $note = s('privacy_note', '');
    if ($note !== '') { $h .= '<p class="form-note">' . hc_icon('shield') . hc_e($note) . '</p>'; }
    $h .= '</form>';
    return $h;
}

/* ---------------------------------------------------------------------
   TRACKING CODES (Admin → Site Settings → Tracking)
   --------------------------------------------------------------------- */
function hc_tracking($box)
{
    if (!empty($GLOBALS['HC']['preview'])) { return ''; }
    $on = on(s('tracking.' . $box . '_code_on', false));
    $code = (string)s('tracking.' . $box . '_code', '');
    if (!$on || trim($code) === '') { return ''; }
    return "\n" . $code . "\n";
}
