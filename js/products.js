document.addEventListener('DOMContentLoaded', function () {
  const filterForm = document.querySelector('form');

  filterForm.addEventListener('submit', function (event) {
    event.preventDefault();
    const category = filterForm.querySelector('select[name="category"]').value;
    const minPrice = filterForm.querySelector('input[name="min_price"]').value;
    const maxPrice = filterForm.querySelector('input[name="max_price"]').value;

    const url = `products.php?category=${category}&min_price=${minPrice}&max_price=${maxPrice}`;
    window.location.href = url;
  });
});
