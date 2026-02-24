<?php
require_once '../../config/functions.php';

$cart_items = getCartItems();
$cart_total = getCartTotal();
$shipping = calculateShipping($cart_total);
$tax = calculateTax($cart_total);
$grand_total = $cart_total + $shipping + $tax;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - <?php echo $settings['site_name'] ?? 'eMarket'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include '../../views/layouts/header.php'; ?>
    
    <div class="cart-section">
        <div class="container">
            <h2 class="mb-4">Shopping Cart</h2>
            
            <?php if(empty($cart_items)): ?>
                <div class="empty-state">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>Your cart is empty</h3>
                    <p>Looks like you haven't added anything to your cart yet.</p>
                    <a href="../../index.php" class="btn btn-primary">Continue Shopping</a>
                </div>
            <?php else: ?>
                <div class="row">
                    <div class="col-lg-8">
                        <div class="table-responsive">
                            <table class="cart-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($cart_items as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="product-info">
                                                    <img src="<?php echo $item['image'] ? '../../uploads/products/' . $item['image'] : '../../assets/images/no-image.png'; ?>" alt="<?php echo $item['name']; ?>">
                                                    <div>
                                                        <h4><?php echo $item['name']; ?></h4>
                                                        <small>SKU: <?php echo $item['sku'] ?? 'N/A'; ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo formatPrice($item['price']); ?></td>
                                            <td>
                                                <input type="number" value="<?php echo $item['quantity']; ?>" 
                                                       min="1" max="<?php echo $item['stock_quantity'] ?? 99; ?>"
                                                       class="quantity-input" 
                                                       data-cart-item-id="<?php echo $item['id']; ?>"
                                                       onchange="updateCartQuantity(<?php echo $item['id']; ?>, this.value)">
                                            </td>
                                            <td><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-danger" onclick="removeFromCart(<?php echo $item['id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="cart-summary">
                            <h3>Cart Summary</h3>
                            <div class="summary-row">
                                <span>Subtotal</span>
                                <span><?php echo formatPrice($cart_total); ?></span>
                            </div>
                            <div class="summary-row">
                                <span>Shipping</span>
                                <span><?php echo formatPrice($shipping); ?></span>
                            </div>
                            <div class="summary-row">
                                <span>Tax</span>
                                <span><?php echo formatPrice($tax); ?></span>
                            </div>
                            <div class="summary-row">
                                <span>Coupon Discount</span>
                                <span id="discountAmount">$0.00</span>
                            </div>
                            <div class="summary-row total">
                                <span>Total</span>
                                <span id="grandTotal"><?php echo formatPrice($grand_total); ?></span>
                            </div>
                            
                            <div class="mt-3">
                                <div class="input-group mb-3">
                                    <input type="text" id="couponCode" class="form-control" placeholder="Coupon Code">
                                    <button class="btn btn-outline-secondary" onclick="applyCoupon()">Apply</button>
                                </div>
                            </div>
                            
                            <?php if(isset($_SESSION['user_id'])): ?>
                                <a href="checkout.php" class="btn btn-primary w-100">Proceed to Checkout</a>
                            <?php else: ?>
                                <a href="../../views/auth/login.php?redirect=checkout" class="btn btn-primary w-100">Proceed to Checkout</a>
                        
                                
                            <?php endif; ?>
                            <a href="../../index.php" class="btn btn-outline w-100 mt-3">Continue Shopping</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include '../../views/layouts/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/script.js"></script>
</body>
</html>
