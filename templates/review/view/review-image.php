<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$display = get_option( 'hp_user_enable_display' ) && ! $review->is_anonymous();
?>
<div class="hp-review__image">
	<?php if ( $display ) : ?>
		<a href="<?php echo esc_url( hivepress()->router->get_url( 'user_view_page', [ 'username' => $review->get_author__username() ] ) ); ?>">
		<?php
	endif;

	if ( ! $review->is_anonymous() ) :
		echo get_avatar( $review->get_author__id(), 150 );
	else :
		?>
		<img src="<?php echo esc_url( hivepress()->get_url() . '/assets/images/placeholders/user-square.svg' ); ?>" alt="<?php esc_attr_e( 'Anonymous', 'hivepress-reviews' ); ?>" loading="lazy">
		<?php
	endif;

	if ( $display ) :
		?>
		</a>
	<?php endif; ?>
</div>
