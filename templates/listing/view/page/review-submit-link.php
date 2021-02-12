<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<a href="#<?php if ( is_user_logged_in() ) : ?>review_submit<?php else : ?>user_login<?php endif; ?>_modal" class="hp-listing__action hp-listing__action--review hp-link">
	<i class="hp-icon fas fa-star"></i>
	<span><?php esc_html_e( 'Write a Review', 'hivepress-reviews' ); ?></span>
</a>
