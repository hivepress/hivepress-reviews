<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! $review->get_parent__id() && isset( $listing ) && $listing->get_user__id() === get_current_user_id() ) :
	?>
	<a href="#review_reply_modal_<?php echo esc_attr( $review->get_id() ); ?>" class="hp-review__action hp-review__action--reply hp-link">
		<i class="hp-icon fas fa-share"></i>
		<span><?php esc_html_e( 'Reply', 'hivepress-reviews' ); ?></span>
	</a>
	<?php
endif;
