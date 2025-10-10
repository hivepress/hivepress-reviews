<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( $review->get_images__id() && ! empty( $listing ) ) :
	$images = is_array( $review->get_images__id() ) ? $review->get_images() : [ $review->get_images() ];
	?>
	<div class="hp-review__images">
		<div class="hp-row">
			<?php foreach ( $images as $image ) : ?>
				<div class="hp-image hp-col-sm-2 hp-col-xs-4">
					<img src="<?php echo esc_url( $image->get_url( 'thumbnail' ) ); ?>" data-component="image" data-zoom="<?php echo esc_url( $image->get_url( 'large' ) ); ?>" alt="<?php echo esc_attr( $listing->get_title() ); ?>" loading="lazy">
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
endif;
