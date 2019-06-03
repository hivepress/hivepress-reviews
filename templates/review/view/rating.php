<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<div class="hp-review__rating hp-rating-stars" data-component="rating" data-value="<?php echo esc_attr( $review->get_rating() ); ?>"></div>
