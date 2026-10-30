<?php /* 404 PAGE DESIGN */ hc_layout_start(); ?>
<section class="sec">
  <div class="wrap">
    <div class="prose"><p style="text-align:center"><?= hc_e(c('body_text', 'Please use the menu above, or go back to the home page.')) ?></p></div>
    <div style="text-align:center"><a class="btn btn-book btn-lg" href="<?= hc_e(hc_url('')) ?>"><?= hc_e(c('button_text', 'Go to Home Page')) ?></a></div>
  </div>
</section>
<?php hc_layout_end(false); ?>
