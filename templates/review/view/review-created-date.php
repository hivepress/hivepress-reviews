<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<time class="hp-review__created-date hp-review__date hp-meta" datetime="<?php echo esc_attr( $review->get_created_date() ); ?>"><?php echo esc_html( $review->display_created_date() ); ?></time>
