<?php /* BLOG POST DESIGN — banner + meta line + sections */ hc_layout_start(); ?>
<?php if (c('post_date', '') !== '' || c('post_category', '') !== '' || c('post_image', '') !== ''): ?>
<section class="sec sec-tight"><div class="wrap narrow">
  <p class="post-meta">
    <?php if (c('post_date', '') !== ''): ?><time><?= hc_e(c('post_date')) ?></time><?php endif; ?>
    <?php if (c('post_category', '') !== ''): ?><a class="post-cat" href="<?= hc_e(c('post_category_url')) ?>"><?= hc_e(c('post_category')) ?></a><?php endif; ?>
  </p>
  <?php if (c('post_image', '') !== ''): ?><img class="post-feature" src="<?= hc_e(hc_img(c('post_image'))) ?>" alt="<?= hc_e(hc_plain_title(c('page_name', ''))) ?>" width="1200" height="630"><?php endif; ?>
</div></section>
<?php endif; ?>
<?= hc_blocks(c('blocks', array())) ?>
<?php $rel = hc_posts(3, c('post_category_id', null), 0, c('post_id', null)); if ($rel): ?>
<section class="sec sec-soft"><div class="wrap">
  <?= hc_head_html('Keep reading', 'More [Articles]', '', true) ?>
  <?= hc_post_cards_html($rel) ?>
</div></section>
<?php endif; ?>
<?php hc_layout_end(on(c('cta_show', true))); ?>
