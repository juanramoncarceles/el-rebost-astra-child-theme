# Astra child theme for Woocommerce

Theme main features:
- Products nutritional facts table.
- Sell by weight.
- Products only for local pickup and home delivery¹.

¹ *Home delivery refers to a free delivery only to specific locations when a minimum total price is reached. Those products can't be shipped.*

---

### Home delivery set up

This is only applicable to "free shipping" and "flat rate" shipping methods.

To indicate that a method is home delivery there is a checkbox in the shipping method settings.

To indicate that a product can only be sent with home delivery methods a shipping class should be applied.
Shipping classes are created in: *WooCommerce > Settings > Shipping > Shipping classes*

There are two variantions:

- **Regular**: Default behaviour as explained above. If those products are in the cart only local pickup or home delivery shipping methods will be allowed. If any other method is chosen a warning will appear and if the user continues to the checkout page those products will be removed from the cart.
  - Shipping class slug: **no-shipping**

- **Special**: Products that behave like the regular ones but that doesn't count to reach the minimum price for home delivery. They rely on regular home delivery products to reach the minimum.
  - Shipping class slug: **no-shipping-no-count**

---

All shipping methods have a field "Información extra para mostrar" to set a message that is displayed under the method.

The shipping method flat_rate also has the options "Coste mínimo" and "Precio antes del mínimo" to set a minimum price for the regular price to apply, and to show an alternative price if the cart total is below the minimum. In case the minimum cost is set but the price below minimum is not set the method will not be displayed if the cart total is below the minimum.

---

### Sell by weight

To sell by weight the weight unit is the one set in: *WooCommerce > Settings > Products > Measurements > Weight unit*
The available amounts are 20, 50 and 100, beacuse it was designed for "g".
The price set in the product page for products sold by weight is the price for 1Kg of the product.
