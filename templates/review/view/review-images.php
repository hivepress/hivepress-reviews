<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( $review->get_images__id() ) :
	?>
	<a href="<?php echo esc_url( $review->get_images__url() ); ?>" target="_blank" class="hp-review__images hp-link">
		<i class="hp-icon fas fa-file-download"></i>
		<span><?php echo esc_html( $review->get_images__name() ); ?></span>
	</a>
	<?php
endif;
