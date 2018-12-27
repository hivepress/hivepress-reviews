<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<div class="hp-review__rating">
	<div class="hp-rating hp-js-rating" data-value="<?php echo esc_attr( get_comment_meta( $review->comment_ID, 'hp_rating', true ) ); ?>"></div>
</div>
