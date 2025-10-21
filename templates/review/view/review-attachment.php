<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( $review->get_attachment__id() && ! empty( $listing ) ) :
	$attachment = $review->get_attachment();

	if ( $attachment ) :
		?>
		<div class="hp-review__attachment">
			<div class="hp-row">
				<div class="hp-image hp-col-sm-2 hp-col-xs-4">
					<img src="<?php echo esc_url( $attachment->get_url( 'thumbnail' ) ); ?>" data-component="image" data-zoom="<?php echo esc_url( $attachment->get_url( 'large' ) ); ?>" alt="<?php echo esc_attr( $listing->get_title() ); ?>" loading="lazy">
				</div>
			</div>
		</div>
		<?php
	endif;
endif;
