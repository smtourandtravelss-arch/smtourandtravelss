<?php /* ALL SERVICES DESIGN — cards come from Admin → Services */ hc_layout_start(); ?>
<section class="sec" id="services">
  <div class="wrap">
    <?= hc_sec_head(1) ?>
    <?= hc_cards_grid(on(c('filter_show', true)), 0, c('category_filter', ''), c('category_filter', '') === '') ?>
  </div>
</section>
<?php hc_layout_end(on(c('cta_show', true))); ?>
