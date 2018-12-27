<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( '' !== $review->comment_content ) :
	?>
	<div class="hp-review__text">
		<?php comment_text( $review->comment_ID ); ?>
	</div>
	<?php
endif;
