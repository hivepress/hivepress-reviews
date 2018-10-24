<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( is_user_logged_in() && comments_open() && ! isset( $review ) ) :
	?>
	<div id="hp-review-submit-<?php echo esc_attr( get_the_ID() ); ?>" class="hp-popup">
		<h3 class="hp-popup__title"><?php esc_html_e( 'Submit Review', 'hivepress-reviews' ); ?></h3>
		<?php echo hivepress()->template->render_part( 'review/parts/submit-form' ); ?>
	</div>
	<?php
endif;
