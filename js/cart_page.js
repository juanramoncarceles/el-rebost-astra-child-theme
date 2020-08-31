console.log("Running script for fake \"Go to checkout button\"");

// Toggles the visibility of some objects.
// This function should be added to the window global object to be called from an inline event listener.
function showAlertAndButtn() {
  document.getElementById('fake_checkout_button').style.display = 'none';
  document.getElementById('no_shipping_message_alert').style.display = 'block';
  document.getElementById('checkout_custom_btn_without_noshipping_products').style.display = 'block';
}