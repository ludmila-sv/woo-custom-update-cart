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
 * Enqueue scripts and styles
 *
 * @return void
 */
function astrum_enqueue_scripts() {
	$asset_version = '1.0.0';

	//phpcs:ignore
	wp_enqueue_style( 'astrum-bs-style', get_template_directory_uri() . '/assets/style/bootstrap.min.css', null, $asset_version );
	wp_enqueue_style( 'astrum-style', get_template_directory_uri() . '/assets/css/main.css', array( 'astrum-bs-style' ), $asset_version );

	wp_enqueue_style( 'astrum-woocommerce', get_template_directory_uri() . '/assets/style/woocommerce.css', array( 'astrum-bs-style' ), $asset_version );

	wp_enqueue_script(
		'popper-js',
		'https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js',
		array(
			'jquery',
		),
		'2.9.2',
		true
	);

	wp_enqueue_script(
		'bootstrap-js',
		'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js',
		array(
			'jquery',
			'popper-js',
		),
		'5.0.2',
		true
	);

	wp_enqueue_script(
		'astrum-main-scripts',
		get_template_directory_uri() . '/assets/js/main/main.js',
		array( 'jquery' ),
		$asset_version,
		true
	);

	wp_enqueue_script(
		'astrum-woo-scripts',
		get_template_directory_uri() . '/assets/js/scripts/woocommerce.js',
		array( 'jquery' ),
		$asset_version,
		true
	);

	wp_localize_script( 'astrum-woo-scripts', 'cart_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

}

add_action( 'wp_enqueue_scripts', 'astrum_enqueue_scripts' );

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
function ainsys_update_cart() {
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

			$add_to_cart = WC()->cart->add_to_cart( $new_product_id, $quantity ); // returns true/false.

			if ( $passed_validation && $add_to_cart && 'publish' === $product_status ) {
				do_action( 'woocommerce_ajax_added_to_cart', $new_product_id );

				WC()->cart->calculate_totals();
				WC_AJAX::get_refreshed_fragments();
				WC()->cart->maybe_set_cart_cookies();
			} else {
				json_encode( wc_print_notices() );
			}
		}
		wp_die();
	}
}
add_action( 'wp_ajax_astrum_update_cart', 'astrum_update_cart' );
add_action( 'wp_ajax_nopriv_astrum_update_cart', 'astrum_update_cart' );
