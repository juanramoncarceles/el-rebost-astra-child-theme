<?php
/**
 * Proceed to checkout button
 *
 * Contains the markup for the proceed to checkout button on the cart.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/proceed-to-checkout-button.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<?php

$cart_items = WC()->cart->get_cart();

$no_shipping_products = [];

// Get all the names of the products that cannot be shipped.
foreach ($cart_items as $cart_item => $values) {
	if ($values['data']->get_shipping_class() == 'no-shipping') {
		array_push($no_shipping_products, $values['data']->get_title());
	}
}

$selected_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );

$are_all_selected_methods_localpickup = true;

// If all in the array are 'local_pickup' then nothing will happen.
array_walk($selected_shipping_methods, function($shipping_methods) use(&$are_all_selected_methods_localpickup) {
	if (strpos($shipping_methods, 'local_pickup') === false) {
		$are_all_selected_methods_localpickup = false;
	}
});

?>

<?php if (count($no_shipping_products) > 0 && !$are_all_selected_methods_localpickup) : /* Show the fake button that will trigger the message. */ ?>

	<div id="no_shipping_message_alert" style="display:none;background-color:rgb(225,225,225);padding:15px;margin-bottom:20px;font-weight:bold;">
		<?php
			echo "<p>INFORMACIÓ IMPORTANT!<br/>Els següents productes NO es poden enviar i has triat un tipus d'enviament a domicili, si continues s'eliminaran.</p><ul>";
			foreach ($no_shipping_products as $product) {
				echo '<li>' . $product . '</li>';
			}
			echo "</ul><p>Si vols procedir amb tots els productes tria l'opció \"Recollida local\". Sino fes clic abaix per continuar sense els productes que no es poden enviar.</p>";
		?>
	</div>

	<a id="fake_checkout_button" class="checkout-button button alt wc-forward" onclick="showAlertAndButtn()">
		<?php esc_html_e( 'Proceed to checkout', 'woocommerce' ); ?>
	</a>

	<a id="checkout_custom_btn_without_noshipping_products" style="display:none;" href="<?php echo esc_url( wc_get_checkout_url() ) . '?remove_noshipping_items=true'; ?>" class="checkout-button button alt wc-forward">
		Continuar sense els productes que no s'envien
	</a>

<?php else: /* Show the default WC button */ ?>

	<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="checkout-button button alt wc-forward">
		<?php esc_html_e( 'Proceed to checkout', 'woocommerce' ); ?>
	</a>

<?php endif; ?>
