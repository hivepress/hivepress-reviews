<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( $vendor->get_rating() ) :
	?>
	<div class="hp-vendor__rating hp-rating">
		<div class="hp-rating__stars hp-rating-stars" data-component="rating" data-value="<?php echo esc_attr( $vendor->get_rating() ); ?>"></div>
		<div class="hp-rating__details">
			<span class="hp-rating__value"><?php echo esc_html( $vendor->display_rating() ); ?></span>
			<span class="hp-rating__count">(<?php echo esc_html( $vendor->display_rating_count() ); ?>)</span>
		</div>
	</div>
	<?php
endif;
