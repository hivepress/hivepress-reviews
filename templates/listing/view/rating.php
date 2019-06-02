<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( $listing->get_rating() ) :
	?>
	<div class="hp-listing__rating">
		<div data-component="rating" data-value="<?php echo esc_attr( $listing->get_rating() ); ?>"></div>
		<span><?php echo esc_html( $listing->get_rating_count() ); ?></span>
	</div>
	<?php
endif;
