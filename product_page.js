// Used in the product page.
// It adds the logic to display a realtime price / amount calculator.

(function() {
  console.log("Running realtime price so it should be a product page.");
  
  const qty_input = document.querySelector('form.cart div.quantity > input');
  const totalProductPrice = document.getElementById('total_product_price');
  const totalProductAmount = document.getElementById('total_product_amount');
  
  // If no form's <input> element has been found return since it means the product might be out of stock and nothing can be done.
  if (qty_input === null) return;

  const initial_qty = qty_input.value;

  function formatPrice(num) {
    return num.toFixed(2).replace('.', ',');
  }

  function updatePriceForVariation(arrayOfVariationObjs, variationId) {
    const variation = arrayOfVariationObjs.find(variation => variation.variation_id == variationId);
    const price_num = variation.display_price;
    totalProductPrice.textContent = formatPrice(price_num);
    totalProductPrice.dataset.initialPrice = price_num;
  }

  // Show the initial values.
  // Values can be different than the default ones for example when the form to add a product has been sent and the page refreshes.
  totalProductPrice.textContent = formatPrice(totalProductPrice.dataset.initialPrice * initial_qty);
  totalProductAmount.textContent = totalProductAmount.dataset.initialAmount * initial_qty;
  
  // Only in the case of a variable product a listener on the select elements should be added to update the initial price after each change.
  const variations_selects = document.querySelector('form.cart > table.variations');
  if (variations_selects != null) {
    // This is a variable product.
    console.log('This is a variable product.');
    // Get all the variations data that WooCommerce stores in a data attr in the <form> element.
    const form = document.querySelector('form.cart');
    const variations_arr = JSON.parse(form.dataset.product_variations);
    let variation_has_changed = false;
    // This <input> element will be observed to get its value attr which holds the current variation id.
    const variation_id_html = document.querySelector('input.variation_id');
    // Initially it is set to display nothing because the inital option is set in delay so it cannot be known.
    totalProductPrice.textContent = "-";
    // A few time is required just to let the WooCommerce's JavaScript to calculate the first variation so it can be read successfully.
    setTimeout(() => {
      console.log("Initial variation: ", variation_id_html.value);
      if (variation_id_html.value != "0") {
        updatePriceForVariation(variations_arr, variation_id_html.value);
      }
    }, 2000);
    // Create an observer instance for the <input> element.
    const observer = new MutationObserver((mutationsList, observer) => {
      const lastMutation = mutationsList[mutationsList.length - 1];
      if (variation_has_changed && lastMutation.type === 'attributes' && lastMutation.attributeName === 'value') {
        console.log("Value has changed to variation: ", variation_id_html.value);
        if (variation_id_html.value != "") {
          updatePriceForVariation(variations_arr, variation_id_html.value);
          totalProductAmount.textContent = totalProductAmount.dataset.initialAmount;
        } else {
          totalProductPrice.textContent = '-';
        }
        variation_has_changed = false;
      }
    });
    // Start observing the <input> element for mutations.
    observer.observe(variation_id_html, { attributes: true, childList: false, subtree: false });
    // This wouldn't be necessary but its an extra check, only if a <select> changes a variation can happen.
    variations_selects.addEventListener('input', e => {
      variation_has_changed = true;
    });
  }

  function updateDisplayedPriceAndAmount() {
    const real_time_qty = qty_input.value;
    totalProductPrice.textContent = formatPrice(totalProductPrice.dataset.initialPrice * real_time_qty);
    totalProductAmount.textContent = totalProductAmount.dataset.initialAmount * real_time_qty;
  }

  // Listens for the quantity input to change and updates de values for price and amount.
  qty_input.addEventListener('input', () => {
    updateDisplayedPriceAndAmount();
  });

  // Changing theme options the <input> can include two buttons to change the value, which are added by the theme in delay.
  setTimeout(() => {
    const plusBtn = document.querySelector('form.cart div.quantity > a.plus');
    const minusBtn = document.querySelector('form.cart div.quantity > a.minus');
    // In case the buttons to change the value exist add event listeners to them.
    if (plusBtn !== null && minusBtn !== null) {
      plusBtn.addEventListener('click', () => {
        updateDisplayedPriceAndAmount();
      });
      minusBtn.addEventListener('click', () => {
        updateDisplayedPriceAndAmount();
      });
    }
  }, 2000);
})()
