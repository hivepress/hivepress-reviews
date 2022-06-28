<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$display = get_option( 'hp_user_enable_display' );

// Get author id.
$author_id = $review->get_anonymous() ? null : $review->get_author__id();
?>
<div class="hp-review__image">
	<?php if ( $display ) : ?>
		<a href="<?php echo esc_url( hivepress()->router->get_url( 'user_view_page', [ 'username' => $review->get_author__username() ] ) ); ?>">
		<?php
	endif;

	echo get_avatar( $author_id, 150, 'mystery' ); 

	if ( $display ) :
		?>
		</a>
	<?php endif; ?>
</div>
