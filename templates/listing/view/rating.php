<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( $listing->get_rating() ) :
	?>
	<div class="hp-listing__rating hp-rating">
		<div class="hp-rating__stars hp-rating-stars" data-component="rating" data-value="<?php echo esc_attr( $listing->get_rating() ); ?>"></div>
		<div class="hp-rating__details">
			<span class="hp-rating__value"><?php echo esc_html( number_format_i18n( $listing->get_rating(), 1 ) ); ?></span>
			<span class="hp-rating__count">(<?php echo esc_html( number_format_i18n( $listing->get_rating_count() ) ); ?>)</span>
		</div>
	</div>
	<?php
endif;
