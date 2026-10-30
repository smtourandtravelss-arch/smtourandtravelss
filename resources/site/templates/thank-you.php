<?php /* THANK YOU PAGE DESIGN — text comes from Admin → Pages → Thank You */
$clean = function ($v) { return trim(mb_substr(strip_tags((string)$v), 0, 80, 'UTF-8')); };
$name = $clean(request()->query('name', ''));
$item = $clean(request()->query('item', ''));
$parts = preg_split('/\s+/', $name);
$first = $name !== '' ? $parts[0] : '';
// "Share details on WhatsApp" — only for the visitor who just sent the form (signed link)
$shareUrl = '';
if (on(s('form_action.thankyou_wa_share', false))) {
    $tok = explode('.', (string)request()->query('l', ''));
    if (count($tok) === 2 && ctype_digit($tok[0]) && hash_equals(substr(hash_hmac('sha256', $tok[0], hc_secret()), 0, 16), $tok[1])) {
        $lead = \App\Models\Lead::query()->find((int)$tok[0]);
        if ($lead && $lead->created_at && $lead->created_at->gt(now()->subDay())) {
            $rows = array();
            foreach ((array)$lead->data as $f) { $rows[] = array(isset($f['label']) ? $f['label'] : '', isset($f['value']) ? $f['value'] : ''); }
            if ($lead->item) { $rows[] = array('Enquiry For', $lead->item); }
            $shareUrl = hc_wa_details_url($rows, array('name' => $lead->name, 'mobile' => $lead->mobile, 'item' => (string)$lead->item, 'page' => (string)$lead->page_url, 'form' => (string)$lead->form_name));
        }
    }
}
hc_layout_start();
hc_inner_hero(false, ' style="padding-bottom:140px"');
?>
<section class="sec" style="padding-top:0">
  <div class="wrap">
    <div class="ty">
      <div class="tick"><?= hc_icon('check') ?></div>
      <h1><?= hc_e($first !== '' ? str_replace('{name}', $first, c('heading_name', 'Thank You, {name}!')) : c('heading_general', 'Thank You!')) ?></h1>
      <p><?= hc_e($item !== '' && c('message_item', '') !== '' ? str_replace('{item}', $item, c('message_item')) : c('message_general', 'We received your request. Our team will call you soon.')) ?></p>
      <?php if (c('small_line', '') !== ''): ?><p class="urgent"><?= hc_e(c('small_line')) ?></p><?php endif; ?>
      <div class="btns">
        <?= hc_call_btn('btn btn-tycall btn-lg', s('thankyou.call_text', 'Call Now')) ?>
        <?php if ($shareUrl !== ''): ?>
        <a href="<?= hc_e($shareUrl) ?>" target="_blank" rel="noopener" class="btn btn-wa btn-lg" data-track="whatsapp_share"><?= hc_icon('wa') ?><?= hc_e(s('form_action.share_text', 'Share details on WhatsApp')) ?></a>
        <?php else: ?>
        <?= hc_wa_btn('btn btn-wa btn-lg', '', s('buttons.whatsapp_text', 'WhatsApp'), s('thankyou.wa_message', 'Hi, I just sent a request.')) ?>
        <?php endif; ?>
        <a href="<?= hc_e(hc_url('')) ?>" class="btn btn-ghost btn-lg"><?= hc_icon('home') ?><?= hc_e(s('thankyou.home_text', 'Back to Home')) ?></a>
      </div>
    </div>
  </div>
</section>
<?php hc_layout_end(false); ?>
