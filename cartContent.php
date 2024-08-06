<?php
$cart_items = $cart->getCartItems($user_id);
$total_price = 0;
?>

<?php if (count($cart_items) > 0): ?>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-light">
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                    <tr class="cart-item">
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td>
                            <div class="input-group quantity-control">
                                <div class="input-group-prepend">
                                    <a href="#" data-action="decrement"
                                        data-product-id="<?php echo htmlspecialchars($item['product_id']); ?>"
                                        class="btn btn-outline-secondary"><i class="fas fa-minus"></i></a>
                                </div>
                                <input type="text" class="form-control text-center"
                                    value="<?php echo htmlspecialchars($item['quantity']); ?>" readonly>
                                <div class="input-group-append">
                                    <a href="#" data-action="increment"
                                        data-product-id="<?php echo htmlspecialchars($item['product_id']); ?>"
                                        class="btn btn-outline-secondary"><i class="fas fa-plus"></i></a>
                                </div>
                            </div>
                        </td>
                        <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        <td>
                            <a href="#" data-action="remove"
                                data-product-id="<?php echo htmlspecialchars($item['product_id']); ?>"
                                class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php $total_price += $item['price'] * $item['quantity']; ?>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right"><strong>Total:</strong></td>
                    <td colspan="2"><strong>$<?php echo number_format($total_price, 2); ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="text-right mt-4">
        <a href="index.php" class="btn btn-secondary">Continue Shopping</a>
        <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
    </div>
<?php else: ?>
    <div class="alert alert-info text-center" role="alert">
        <i class="fas fa-shopping-cart fa-2x mb-3"></i>
        <p class="mb-0">Your cart is empty. Start shopping now!</p>
    </div>
    <div class="text-center mt-4">
        <a href="index.php" class="btn btn-primary">Explore Products</a>
    </div>
<?php endif; ?>