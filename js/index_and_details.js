document.addEventListener('DOMContentLoaded', function () {
  const addToCartBtns = document.querySelectorAll('.add-to-cart-btn');

  addToCartBtns.forEach((btn) => {
    btn.addEventListener('click', function (event) {
      event.preventDefault();
      const productId = btn.getAttribute('data-product-id');
      const quantity = document.getElementById('quantity').value;
      window.location.href = `add_to_cart.php?id=${productId}&quantity=${quantity}`;
    });
  });
});
