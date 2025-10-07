<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$display = get_option( 'hp_user_enable_display' ) && ! $review->is_anonymous();

if ( $display ) :
	?>
	<a href="<?php echo esc_url( hivepress()->router->get_url( 'user_view_page', [ 'username' => $review->get_author__username() ] ) ); ?>">
	<?php
endif;

if ( ! $review->is_anonymous() ) :
	echo esc_html( $review->get_author__display_name() );
else :
	esc_html_e( 'Anonymous', 'hivepress-reviews' );
endif;

if ( $display ) :
	?>
	</a>
	<?php
endif;
