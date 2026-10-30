<?php /* LONG TEXT PAGE DESIGN (Privacy / Terms) */ hc_layout_start(); ?>
<section class="sec">
  <div class="wrap">
    <div class="prose"><?= hc_rich(c('body', '')) ?></div>
  </div>
</section>
<?php hc_layout_end(on(c('cta_show', false))); ?>
