<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<time class="hp-review__date" datetime="<?php echo esc_attr( get_comment_date( 'Y-m-d', $review->comment_ID ) ); ?>"><?php echo esc_html( get_comment_date( '', $review->comment_ID ) ); ?></time>
