document.addEventListener('DOMContentLoaded', function () {
  const addToCartBtn = document.querySelector('.add-to-cart-btn');
  const backBtn = document.querySelector('.back-btn');

  addToCartBtn.addEventListener('click', function (event) {
    event.preventDefault();
    const quantity = document.getElementById('quantity').value;
    const productId = addToCartBtn.getAttribute('href').split('=')[1];
    window.location.href = `add_to_cart.php?id=${productId}&quantity=${quantity}`;
  });

  backBtn.addEventListener('click', function (event) {
    event.preventDefault();
    window.location.href = 'index.php';
  });
});
