<?php
/**
 * The template for displaying product content within loops
 *
 * @package ainsys
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>
<?php
$product_id         = $product->get_id();;
?>
<li class="product">
	<div class="product__img">
		<?php echo $product->get_image(); ?>
	</div>
	<div class="product__title">
		<h3><?php echo $product->get_title(); ?></h3>
	</div>

	<?php
	$quantity = 0;
	foreach ( WC()->cart->get_cart() as $cart_item ) {
		if ( $product_id === $cart_item['product_id'] ) {
			$quantity = intval( $cart_item['quantity'] );
		}
	}
	?>
	<div class="product__qnt-buttons">
		<button type="button" class="minus-btn btn-qnty">-</button>
		<input type="number" value="<?php echo $quantity; ?>" class="product__qnt" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>">   
		<button type="button" class="plus-btn btn-qnty">+</button>
	</div>
	<div class="product__description">
		<?php echo $product->get_description(); ?>
	</div>
</li>
