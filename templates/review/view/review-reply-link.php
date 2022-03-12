<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( $listing->get_vendor()->get_user__id() === get_current_user_id() && ! $review->get_parent() ) {
	?>
<a href="#review_reply_modal_<?php echo esc_html( $review->get_id() ); ?>" class="hp-listing__action hp-listing__action--reply hp-link"><i class="hp-icon fas fa-share"></i><span><?php esc_html_e( 'Reply', 'hivepress-reviews' ); ?></span></a>
	<?php
}
?>
