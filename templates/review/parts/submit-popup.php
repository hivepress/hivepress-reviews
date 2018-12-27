<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<div id="hp-review-submit" class="hp-popup">
	<h3 class="hp-popup__title"><?php esc_html_e( 'Write a Review', 'hivepress-reviews' ); ?></h3>
	<?php echo hivepress()->template->render_part( 'review/parts/submit-form' ); ?>
</div>
