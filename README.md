# Astra child theme for Woocommerce

Theme main features:
- Products nutritional facts table.
- Sell by weight.
- Products only for local pickup and home delivery¹.

¹ *Home delivery refers to a free delivery only to specific locations when a minimum total price is reached. Those products can't be shipped.*

---

### Home delivery set up

Those products are identified with shipping classes.

Shipping classes are created in: *WooCommerce > Settings > Shipping > Shipping classes*

There are two variantions:

- **Regular**: Default behaviour as explained above. If those products are in the cart only local pickup or home delivery shipping methods will be allowed. If any other method is chosen a warning will appear and if the user continues to the checkout page those products will be removed from the cart.
  - Shipping class slug: **no-shipping**

- **Special**: Products that behave like the regular ones but that doesn't count to reach the minimum price for home delivery. They rely on regular home delivery products to reach the minimum.
  - Shipping class slug: **no-shipping-no-count**
