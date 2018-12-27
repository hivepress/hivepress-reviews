<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( get_post_meta( get_the_ID(), 'hp_rating', true ) ) :
	?>
	<div class="hp-listing__rating">
		<div class="hp-rating hp-js-rating" data-value="<?php echo esc_attr( get_post_meta( get_the_ID(), 'hp_rating', true ) ); ?>"></div>
		<span><?php echo esc_html( get_post_meta( get_the_ID(), 'hp_rating', true ) ); ?> (<?php echo esc_html( get_post_meta( get_the_ID(), 'hp_rating_count', true ) ); ?>)</span>
	</div>
	<?php
endif;
