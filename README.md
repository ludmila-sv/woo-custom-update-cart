# woo-custom-update-cart
Woocommerce. Custom minicart and +/- buttons on product card that can add/remove products from cart.

content-product.php - product card template for woocommerce. The product card includes +/- buttons. 
Clicking on + adds the product to cart and, if it is already in the cart, increases its quantity.
Clicking on - decreases the product quantity in the cart / delets the product from cart.

woocommerce-customizations.php:
- enqueues styles & scripts, wp_localize_script for ajax in particular,
- adds shortcode for minicart
- contains ajax handler for minicart, that updates the cart

woocommerce.js
- changes the number input field on click on +/- button
- sends input value and product id to cart via ajax

header.php - contains the shortcode that renderes the minicart.
