<?php /* HOME PAGE DESIGN — sections come from Admin → Pages → Home (no inner banner) */ hc_layout_start(); ?>
<?= hc_blocks(c('blocks', array())) ?>
<?php hc_layout_end(on(c('cta_show', false))); ?>
