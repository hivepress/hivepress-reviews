<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( $review->get_attachment__id() ) : ?>
	<div><a href="<?php echo esc_url( $review->get_attachment__url() ); ?>" target="_blank" class="hp-review__attachment hp-link">
		<i class="hp-icon fas fa-file-download"></i>
		<span><?php echo esc_html( $review->get_attachment__name() ); ?></span>
	</a></div>
	<?php
endif;
