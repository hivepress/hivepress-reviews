<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( empty( $listing ) ) :
	?>
	<a href="<?php echo esc_url( hivepress()->router->get_url( 'listing_view_page', [ 'listing_id' => $review->get_listing__id() ] ) ); ?>" class="hp-review__listing hp-link">
		<i class="hp-icon fas fa-share"></i>
		<span><?php echo esc_html( $review->get_listing__title() ); ?></span>
	</a>
	<?php
endif;
