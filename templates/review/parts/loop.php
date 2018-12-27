<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<div class="hp-reviews hp-content hp-block hp-block--reviews">
	<?php
	foreach ( $reviews as $review ) :
		echo hivepress()->template->render_template( 'archive_review', [ 'review' => $review ] );
	endforeach;
	?>
</div>
