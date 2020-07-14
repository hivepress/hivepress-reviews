<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( $listing->get_rating() ) :
	?>
	<div class="hp-listing__rating hp-rating">
		<div class="hp-rating__stars hp-rating-stars" data-component="rating" data-value="<?php echo esc_attr( $listing->get_rating() ); ?>"></div>
		<a href="<?php echo esc_url( hivepress()->router->get_url( 'listing_view_page', [ 'listing_id' => $listing->get_id() ] ) ); ?>#reviews" class="hp-rating__details">
			<span class="hp-rating__value"><?php echo esc_html( $listing->display_rating() ); ?></span>
			<span class="hp-rating__count">(<?php echo esc_html( $listing->display_rating_count() ); ?>)</span>
		</a>
	</div>
	<?php
endif;
