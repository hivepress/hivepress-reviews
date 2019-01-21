<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! empty( $reviews ) ) :
	?>
	<div class="hp-listing__reviews"><?php echo hivepress()->template->render_area( 'single_listing__reviews' ); ?></div>
	<?php
endif;
