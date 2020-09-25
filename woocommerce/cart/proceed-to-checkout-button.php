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

$selected_shipping_methods_ids = WC()->session->get( 'chosen_shipping_methods' );

$are_all_selected_methods_localpickup_or_home_delivery = true;

// Only run if there is at least one product with no-shipping, otherwise there is no need to check the shipping methods.
if (count($no_shipping_products) > 0) {
	// If all in the array are not 'local_pickup' neither 'home delivery' then nothing will happen.
	array_walk($selected_shipping_methods_ids, function($method_id) use(&$are_all_selected_methods_localpickup_or_home_delivery) {
		if (strpos($method_id, 'local_pickup') === false && get_option('woocommerce_' . str_replace(":", "_", $method_id) . '_settings')['shipping_custom_field_is_home_delivery'] !== "yes") {
			$are_all_selected_methods_localpickup_or_home_delivery = false;
		}
	});
}

?>

<?php if (count($no_shipping_products) > 0 && !$are_all_selected_methods_localpickup_or_home_delivery) : /* Show the fake button that will trigger the message. */ ?>

	<div id="no_shipping_message_alert" style="display:none;background-color:#efefef;padding:15px;margin-bottom:20px;font-weight:bold;">
		<?php
			echo "<p>INFORMACIÓ IMPORTANT!<br/>Els següents productes només estan disponibles per recollida a la botiga o per entrega a domicili als següents municipis: St. Feliu de Llobregat, Molins de Rei, St. Joan Despí i St. Just Desvern. Si continues s'eliminaran de la cistella.</p><ul>";
			foreach ($no_shipping_products as $product) {
				echo '<li>' . $product . '</li>';
			}
			echo "</ul><p>Si vols procedir amb tots els productes tria l'opció \"Recollida botiga\" o \"Entrega a domicili\". Sino fes clic abaix per continuar sense aquests productes.</p>";
		?>
	</div>

	<a id="fake_checkout_button" class="checkout-button button alt wc-forward" onclick="showAlertAndButtn()">
		<?php esc_html_e( 'Proceed to checkout', 'woocommerce' ); ?>
	</a>

	<a id="checkout_custom_btn_without_noshipping_products" style="display:none;" href="<?php echo esc_url( wc_get_checkout_url() ) . '?remove_noshipping_items=true'; ?>" class="checkout-button button alt wc-forward">
		Continuar sense els productes esmentats
	</a>

<?php else: /* Show the default WC button */ ?>

	<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="checkout-button button alt wc-forward">
		<?php esc_html_e( 'Proceed to checkout', 'woocommerce' ); ?>
	</a>

<?php endif; ?>
