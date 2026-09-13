// ===== Add to Cart Button (Client-side only, no real backend) =====
document.addEventListener('DOMContentLoaded', function () {

  const addToCartBtn = document.getElementById('addToCartBtn');
  const cartMessage = document.getElementById('cartMessage');
  const quantityInput = document.getElementById('quantity');

  if (addToCartBtn) {
    addToCartBtn.addEventListener('click', function () {

      const quantity = quantityInput ? quantityInput.value : 1;

      if (quantity < 1) {
        cartMessage.style.color = '#d32f2f';
        cartMessage.textContent = 'Please select a valid quantity.';
        return;
      }

      cartMessage.style.color = '#2e7d32';
      cartMessage.textContent = `${quantity} item(s) added to cart successfully!`;

      // Reset message after a few seconds
      setTimeout(function () {
        cartMessage.textContent = '';
      }, 3000);
    });
  }

});