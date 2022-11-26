<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( $review->get_listing__vendor()->get_user__id() === $review->get_author__id() ) { ?>
	<div class="hp-review__author hp-status"><?php echo esc_html( $review->get_author__display_name() ); ?><span><?php echo esc_html( hivepress()->translator->get_string( 'vendor' ) ); ?></span></div>
	<?php
} else {
	?>
<div class="hp-review__author"><?php echo esc_html( $review->get_author__display_name() ); ?></div>
	<?php
}
