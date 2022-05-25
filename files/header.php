<?php
/**
 * Header template.
 *
 * @package astrum
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<?php wp_head(); ?>
	</head>

	<body <?php body_class(); ?>>

		<?php do_action( 'body_top' ); ?>


		<div id="page" class="site">
			<div class="site-content-contain">
				<div id="content" class="site-content site-content-header">

					<header class="header">
						<div class="container">
							<a href="/" class="header__logo"></a>

							<?php if ( function_exists( 'woocommerce_mini_cart' ) ) : ?>
								<div class="header__cart">
									<?php echo do_shortcode('[customminicart]'); ?>
								</div>
							<?php endif; ?>
						</div>
					</header>
