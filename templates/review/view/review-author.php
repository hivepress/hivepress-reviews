<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
$display = get_option( 'hp_user_enable_display' ) && $review->get_author() && get_current_user_id() !== $review->get_author__id();
?> 
<div class="hp-review__author">
<?php if ( $display ) : ?>
<a class="hp-link" href="<?php echo esc_url( hivepress()->router->get_url( 'user_view_page', [ 'username' => $review->get_author__username() ] ) ); ?>">
<?php endif;
echo esc_html( $review->get_author__display_name() ); 
if ( $display ) : ?></a><?php endif; ?> 
</div>
