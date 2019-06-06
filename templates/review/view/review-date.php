<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<time class="hp-review__date" datetime="<?php echo esc_attr( $review->get_date( 'Y-m-d' ) ); ?>"><?php echo esc_html( $review->get_date() ); ?></time>
