<?php
/**
 * Woocommerce customization.
 *
 * @package astrum
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Custom function that display minicart span with count.
 *
 * @package astrum
 */
function astrum_minicartcount() {
	$html_output = '<i class="fa fa-shopping-cart" aria-hidden="true"></i><span class="count">' . WC()->cart->get_cart_contents_count() . '</span>';
	//return '<a class="wcminicart" href="' . wc_get_cart_url() . '">' . $html_output . '</a>';
	return '<a class="wcminicart dropdown-toggle" id="cart-dropdown" data-bs-toggle="dropdown" aria-expanded="false" href="' . wc_get_cart_url() . '" style="color: #fff;">' . $html_output . '</a>';
}
/**
 * Custom mini cart shortcode.
 *
 * @package astrum
 */
function astrum_mini_cart() {
	?>
	<div class="dropdown widget_shopping_cart">
		<?php echo astrum_minicartcount(); //phpcs:ignore ?>
		<div class="dropdown-menu" aria-labelledby="cart-dropdown">
			<div class="widget_shopping_cart_content">
				<?php woocommerce_mini_cart(); ?>
			</div>
		</div>
	</div>
	<?php
}
add_shortcode( 'customminicart', 'astrum_mini_cart' );

/**
 * Refresh minicart data on Ajax cart events.
 *
 * @param array $fragments - cart fragments.
 *
 * @package astrum
 */
function astrum_ajax_refreshed_minicart_data( $fragments ) {
	$fragments['a.wcminicart'] = astrum_minicartcount();
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'astrum_ajax_refreshed_minicart_data' );

/**
 * Ajax update cart.
 *
 * @package astrum
 */
function astrum_update_cart() {
	$product_id = isset( $_POST['cart_item_key'] ) ? sanitize_text_field( wp_unslash( $_POST['cart_item_key'] ) ) : null;
	$quantity   = isset( $_POST['qty'] ) ? wp_unslash( $_POST['qty'] ) : null;

	if ( $product_id && isset( $quantity ) ) {
		$cart = WC()->cart;
		$cart_id = $cart->generate_cart_id( $product_id );
		$cart_item_id = $cart->find_product_in_cart( $cart_id );

		if ( ! empty( $cart_item_id ) ) {
			$cart->set_quantity( $cart_item_id, $quantity, true ); // true = refresh totals.
		} else {
			$new_product_id    = apply_filters( 'woocommerce_add_to_cart_product_id', $product_id );
			$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $new_product_id, $quantity );
			$product_status    = get_post_status( $new_product_id );

			if ( $passed_validation && WC()->cart->add_to_cart( $new_product_id, $quantity ) && 'publish' === $product_status ) {
				do_action( 'woocommerce_ajax_added_to_cart', $new_product_id );
				WC()->cart->calculate_totals();
			}
		}
		WC_AJAX::get_refreshed_fragments();
		WC()->cart->maybe_set_cart_cookies();
		wp_die();
	}
}
add_action( 'wp_ajax_astrum_update_cart', 'astrum_update_cart' );
add_action( 'wp_ajax_nopriv_astrum_update_cart', 'astrum_update_cart' );
