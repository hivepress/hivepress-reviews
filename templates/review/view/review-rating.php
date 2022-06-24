<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( $review->has_rating() ) :
	?>
	<div class="hp-review__rating hp-rating-stars" data-component="rating" data-value="<?php echo esc_attr( $review->get_rating() ); ?>"></div>
	<?php
endif;
