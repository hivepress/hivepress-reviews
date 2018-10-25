<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! empty( $reviews ) ) :
	?>
	<div class="hp-listing__reviews">
		<h2><?php esc_html_e( 'Reviews', 'hivepress-reviews' ); ?></h2>
		<div class="hp-reviews">
			<?php foreach ( $reviews as $review ) : ?>
			<div class="hp-review">
				<div class="hp-review__sidebar">
					<div class="hp-review__image">
						<?php echo get_avatar( $review ); ?>
					</div>
				</div>
				<div class="hp-review__content">
					<div class="hp-review__header">
						<strong class="hp-review__author"><?php echo esc_html( $review->comment_author ); ?></strong>
						<time class="hp-review__date" datetime="<?php echo esc_attr( get_comment_date( 'Y-m-d', $review->comment_ID ) ); ?>"><?php echo esc_html( get_comment_date( '', $review->comment_ID ) ); ?></time>
					</div>
					<div class="hp-review__rating">
						<div class="hp-rating hp-js-rating" data-value="<?php echo esc_attr( get_comment_meta( $review->comment_ID, 'hp_rating', true ) ); ?>"></div>
					</div>
					<?php if ( '' !== $review->comment_content ) : ?>
					<div class="hp-review__text">
						<?php comment_text( $review->comment_ID ); ?>
					</div>
					<?php endif; ?>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
endif;
