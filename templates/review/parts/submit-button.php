<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( comments_open() && $review_allowed ) :
	?>
	<button type="button" class="hp-listing__action hp-js-link" data-url="#hp-<?php if ( is_user_logged_in() ) : ?>review-submit<?php else : ?>user-login<?php endif; ?>" data-type="popup"><?php esc_html_e( 'Write a Review', 'hivepress-reviews' ); ?></button>
	<?php
	echo hivepress()->template->render_part( 'review/parts/submit-popup' );
endif;
