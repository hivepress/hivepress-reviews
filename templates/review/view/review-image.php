<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$display = get_option( 'hp_user_enable_display' );
?>
<div class="hp-review__image">
	<?php if ( $display ) : ?>
		<a href="<?php echo esc_url( hivepress()->router->get_url( 'user_view_page', [ 'username' => $review->get_author__username() ] ) ); ?>">
		<?php
	endif;

	echo get_avatar( $review->get_author__id(), 150 ); 

	if ( $display ) :
		?>
		</a>
	<?php endif; ?>
</div>
