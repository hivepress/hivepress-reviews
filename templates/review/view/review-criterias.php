<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

foreach ( (array) $review->get_criterias() as $criteria ) :
	?>
    <p class="hp-review__rating hp-review__rating-name"><?php echo esc_html( hivepress()->helper->get_array_value( $criteria, 'name' ) ); ?></p>
	<div class="hp-review__rating hp-rating-stars" data-component="rating" data-value="<?php echo esc_attr( hivepress()->helper->get_array_value( $criteria, 'rating', 0 ) ); ?>"></div>
	<?php
endforeach;
