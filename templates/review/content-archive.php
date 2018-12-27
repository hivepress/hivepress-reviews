<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<div class="hp-review hp-review--archive">
	<div class="hp-review__sidebar"><?php echo hivepress()->template->render_area( 'archive_review__sidebar' ); ?></div>
	<div class="hp-review__content"><?php echo hivepress()->template->render_area( 'archive_review__content' ); ?></div>
</div>
