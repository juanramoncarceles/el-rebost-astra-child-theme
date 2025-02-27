<?php
/**
 * Shipping Methods Display
 *
 * In 2.1 we show methods per package. This allows for multiple methods per order if so desired.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-shipping.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

$formatted_destination    = isset( $formatted_destination ) ? $formatted_destination : WC()->countries->get_formatted_address( $package['destination'], ', ' );
$has_calculated_shipping  = ! empty( $has_calculated_shipping );
$show_shipping_calculator = ! empty( $show_shipping_calculator );
$calculator_text          = '';
?>
<tr class="woocommerce-shipping-totals shipping">
	<th><?php echo wp_kses_post( $package_name ); ?></th>
	<td data-title="<?php echo esc_attr( $package_name ); ?>">
		<?php if ( $available_methods ) : ?>

			<?php /* PHP block added by El Rebost */

			// If page is not cart and there is at least one product with no-shipping or no-shipping-no-count only create the
			// "local pickup" and "home delivery" options and show a message indicating that the reason is that there are some products
			// that are only available for this kind of shipping methods, to change the order click a link to go to the cart.

			$only_local_pickup_and_home_delivery_enabled = false;

			if ( !is_cart() ) {
				$cart_items = WC()->cart->get_cart();
				$product_no_shipping_exists = false;

				// Check if there is at least one that requires local pickup.
				foreach ($cart_items as $cart_item => $values) {
					$item_shipping_class = $values['data']->get_shipping_class();
					if ($item_shipping_class == 'no-shipping' || $item_shipping_class == 'no-shipping-no-count') {
						$product_no_shipping_exists = true;
						break;
					}
				}
		
				if ( $product_no_shipping_exists ) {
					// If the current selected is not a local pickup, set as selected the first local pickup found.
					if ( strpos( $chosen_method, 'local_pickup' ) === false ) {
						foreach ( $available_methods as $method) {
							if ( $method->method_id === 'local_pickup' ) {
								WC()->session->set( 'chosen_shipping_methods', array( $method->id ) );
								break;
							}
						}
					}
					// Print the HTML list but removing any method that is not local pickup or home delivery.
					echo "<ul id=\"shipping_method\" class=\"woocommerce-shipping-methods\">";
					// TODO If there is only one and it is local pickup means that the order is less than 25€
					// TODO If only one is being left it would be better to have the <input type="hidden">.
					foreach ( $available_methods as $method ) {
						if ( strpos($method->id, 'local_pickup') !== false || get_option('woocommerce_' . str_replace(":", "_", $method->id) . '_settings')['shipping_custom_field_is_home_delivery'] === "yes") {
							echo "<li>";
							printf( '<input type="radio" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d_%2$s" value="%3$s" class="shipping_method" %4$s />', $index, esc_attr( sanitize_title( $method->id ) ), esc_attr( $method->id ), checked( $method->id, $chosen_method, false ) ); // WPCS: XSS ok.
							printf( '<label for="shipping_method_%1$s_%2$s">%3$s</label>', $index, esc_attr( sanitize_title( $method->id ) ), wc_cart_totals_shipping_method_label( $method ) ); // WPCS: XSS ok.
							do_action( 'woocommerce_after_shipping_rate', $method, $index );
							echo "</li>";
						}
					}
					echo "</ul>";
					// Print the warning message.
					// TODO Make this message accessible from the WooCommerce settings. It would be difficult because it inlcudes a link to the cart.
					// TODO The home delivery option may not be visible because the order should be at least 25€, how to make this clear to the user?
					$slug_pag_modalitats_de_compra = get_permalink(5778); // The id of the page "modalitats de compra". (In my local WP is 4135)
					$text_to_modalitats_de_compra = $slug_pag_modalitats_de_compra ? "<br />Aquí pots trobar <a href=\"" . $slug_pag_modalitats_de_compra . "\" target=\"_blank\">informació sobre les modalitats de compra</a>." : "";
					echo "<div style=\"padding:10px;margin-top:12px;line-height:1.4rem;background-color:#efefef;\">Degut a certs productes que has afegit hi ha métodes d'enviament que no estan disponibles." . $text_to_modalitats_de_compra . "<br />Si vols modificar l'ordre per disposar de més opcions <a href=\"" . esc_url( wc_get_cart_url() ) . "\">fes clic aquí per tornar a la cistella</a>.</div>";
					$only_local_pickup_and_home_delivery_enabled = true;
				}
			}

			if (!$only_local_pickup_and_home_delivery_enabled) : ?>

			<ul id="shipping_method" class="woocommerce-shipping-methods">

				<?php foreach ( $available_methods as $method ) : ?>
					<li>
						<?php
						if ( 1 < count( $available_methods ) ) {
							printf( '<input type="radio" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d_%2$s" value="%3$s" class="shipping_method" %4$s />', $index, esc_attr( sanitize_title( $method->id ) ), esc_attr( $method->id ), checked( $method->id, $chosen_method, false ) ); // WPCS: XSS ok.
						} else {
							printf( '<input type="hidden" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d_%2$s" value="%3$s" class="shipping_method" />', $index, esc_attr( sanitize_title( $method->id ) ), esc_attr( $method->id ) ); // WPCS: XSS ok.
						}
						printf( '<label for="shipping_method_%1$s_%2$s">%3$s</label>', $index, esc_attr( sanitize_title( $method->id ) ), wc_cart_totals_shipping_method_label( $method ) ); // WPCS: XSS ok.
						do_action( 'woocommerce_after_shipping_rate', $method, $index );
						?>
					</li>
				<?php endforeach; ?>

			</ul>

			<?php endif; /* PHP block added by El Rebost */ ?>

			<?php if ( is_cart() ) : ?>
				<p class="woocommerce-shipping-destination">
					<?php
					if ( $formatted_destination ) {
						// Translators: $s shipping destination.
						printf( esc_html__( 'Shipping to %s.', 'woocommerce' ) . ' ', '<strong>' . esc_html( $formatted_destination ) . '</strong>' );
						$calculator_text = esc_html__( 'Change address', 'woocommerce' );
					} else {
						echo wp_kses_post( apply_filters( 'woocommerce_shipping_estimate_html', __( 'Shipping options will be updated during checkout.', 'woocommerce' ) ) );
					}
					?>
				</p>
			<?php endif; ?>
			<?php
		elseif ( ! $has_calculated_shipping || ! $formatted_destination ) :
			if ( is_cart() && 'no' === get_option( 'woocommerce_enable_shipping_calc' ) ) {
				echo wp_kses_post( apply_filters( 'woocommerce_shipping_not_enabled_on_cart_html', __( 'Shipping costs are calculated during checkout.', 'woocommerce' ) ) );
			} else {
				echo wp_kses_post( apply_filters( 'woocommerce_shipping_may_be_available_html', __( 'Enter your address to view shipping options.', 'woocommerce' ) ) );
			}
		elseif ( ! is_cart() ) :
			echo wp_kses_post( apply_filters( 'woocommerce_no_shipping_available_html', __( 'There are no shipping options available. Please ensure that your address has been entered correctly, or contact us if you need any help.', 'woocommerce' ) ) );
		else :
			// Translators: $s shipping destination.
			echo wp_kses_post( apply_filters( 'woocommerce_cart_no_shipping_available_html', sprintf( esc_html__( 'No shipping options were found for %s.', 'woocommerce' ) . ' ', '<strong>' . esc_html( $formatted_destination ) . '</strong>' ) ) );
			$calculator_text = esc_html__( 'Enter a different address', 'woocommerce' );
		endif;
		?>

		<?php if ( $show_package_details ) : ?>
			<?php echo '<p class="woocommerce-shipping-contents"><small>' . esc_html( $package_details ) . '</small></p>'; ?>
		<?php endif; ?>

		<?php if ( $show_shipping_calculator ) : ?>
			<?php woocommerce_shipping_calculator( $calculator_text ); ?>
		<?php endif; ?>
	</td>
</tr>
