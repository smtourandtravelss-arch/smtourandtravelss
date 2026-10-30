<?php /* BLOG LIST DESIGN — also used for /category/{slug} */ hc_layout_start();
$catId = isset($GLOBALS['HC']['blog_category_id']) ? $GLOBALS['HC']['blog_category_id'] : null;
$pg = max(1, (int)request()->query('page', 1));
$per = 9;
$total = hc_posts_count($catId);
$posts = hc_posts($per, $catId, ($pg - 1) * $per);
?>
<section class="sec"><div class="wrap">
  <?php if (c('category_text', '') !== ''): ?><p class="center muted" style="margin:-20px auto 36px;max-width:720px;font-size:17px"><?= hc_e(c('category_text')) ?></p><?php endif; ?>
  <?= hc_blog_filters($catId) ?>
  <?php if ($posts): ?>
    <?= hc_post_cards_html($posts) ?>
    <?php if ($total > $per): ?>
    <nav class="pager" aria-label="Pages">
      <?php if ($pg > 1): ?><a class="btn btn-outline" href="?page=<?= $pg - 1 ?>">← Newer</a><?php endif; ?>
      <?php if ($pg * $per < $total): ?><a class="btn btn-outline" href="?page=<?= $pg + 1 ?>">Older →</a><?php endif; ?>
    </nav>
    <?php endif; ?>
  <?php else: ?>
    <p class="center">No posts yet. Please check back soon.</p>
  <?php endif; ?>
</div></section>
<?php hc_layout_end(on(c('cta_show', true))); ?>
