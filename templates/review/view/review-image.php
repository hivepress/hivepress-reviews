<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Get author id.
$author_id = $review->get_anonymous() ? null : $review->get_author__id();
?>
<div class="hp-review__image">
	<?php echo get_avatar( $author_id, 150, 'mystery' ); ?>
</div>
