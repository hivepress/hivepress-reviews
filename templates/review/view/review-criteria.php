<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( $review->has_criteria() ) :
	?>
	<div class="hp-review__criteria">
		<?php foreach ( $review->get_criteria() as $criterion ) : ?>
			<div class="hp-review__criterion">
				<strong><?php echo esc_html( $criterion['name'] ); ?>:</strong>
				<span><?php echo esc_html( $criterion['rating'] ); ?></span>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
endif;
