<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! $review->is_approved() ) :
	$status = 'pending';
	$label  = _x( 'Pending', 'review', 'hivepress-reviews' );
elseif ( isset( $listing ) && $listing->get_user__id() === $review->get_author__id() ) :
	$status = 'vendor';
	$label  = hivepress()->translator->get_string( 'vendor' );
endif;

if ( isset( $status ) ) :
	?>
	<i class="hp-review__status-badge hp-review__status-badge--<?php echo esc_attr( $status ); ?> hp-icon fas" title="<?php echo esc_attr( $label ); ?>"></i>
	<?php
endif;
