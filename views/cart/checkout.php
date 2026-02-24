<?php
require_once '../../config/functions.php';

if(!isset($_SESSION['user_id'])) {
    redirect('../auth/login.php?redirect=../cart/checkout.php');
}

$cart_items = getCartItems();

if(empty($cart_items)) {
    redirect('cart.php');
}

$cart_total = getCartTotal();
$shipping = calculateShipping($cart_total);
$tax = calculateTax($cart_total);
$grand_total = $cart_total + $shipping + $tax;

$pending_order_number = 'ORD-' . time() . '-' . rand(1000, 9999);

$error = '';
$success = false;

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $shipping_name = sanitize($_POST['shipping_name']);
    $shipping_email = sanitize($_POST['shipping_email']);
    $shipping_phone = sanitize($_POST['shipping_phone']);
    $shipping_address = sanitize($_POST['shipping_address']);
    $shipping_city = sanitize($_POST['shipping_city']);
    $shipping_country = sanitize($_POST['shipping_country']);
    $shipping_zip = sanitize($_POST['shipping_zip']);
    $payment_method = sanitize($_POST['payment_method']);
    $notes = sanitize($_POST['notes'] ?? '');
    
    global $db;
    
    $order_number = $_POST['order_number'] ?? generateOrderNumber();
    
    $coupon_discount = 0;
    if(isset($_SESSION['coupon_code'])) {
        $coupon_result = validateCoupon($_SESSION['coupon_code'], $cart_total);
        if($coupon_result['valid']) {
            $coupon_discount = $coupon_result['discount'];
        }
    }
    
    $order_data = [
        'user_id' => $_SESSION['user_id'],
        'order_number' => $order_number,
        'subtotal' => $cart_total,
        'tax' => $tax,
        'shipping_cost' => $shipping,
        'discount' => $coupon_discount,
        'coupon_code' => $_SESSION['coupon_code'] ?? null,
        'total' => $cart_total + $shipping + $tax - $coupon_discount,
        'payment_method' => $payment_method,
        'payment_status' => 'pending',
        'order_status' => 'pending',
        'shipping_name' => $shipping_name,
        'shipping_email' => $shipping_email,
        'shipping_phone' => $shipping_phone,
        'shipping_address' => $shipping_address,
        'shipping_city' => $shipping_city,
        'shipping_country' => $shipping_country,
        'shipping_zip' => $shipping_zip,
        'notes' => $notes
    ];
    
    $order_id = $db->insert('orders', $order_data);
    
    if($order_id) {
        foreach($cart_items as $item) {
            $order_item = [
                'order_id' => $order_id,
                'product_id' => $item['product_id'],
                'product_name' => $item['name'],
                'product_price' => $item['price'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['price'] * $item['quantity']
            ];
            $db->insert('order_items', $order_item);
            
            $db->update('products', ['stock_quantity' => $item['stock_quantity'] - $item['quantity']], 'id = :id', ['id' => $item['product_id']]);
        }
        
        $db->delete("DELETE FROM cart_items WHERE cart_id IN (SELECT id FROM carts WHERE user_id = ?)", [$_SESSION['user_id']]);
        
        unset($_SESSION['coupon_code']);
        
        redirect('order-success.php?order=' . $order_number);
    } else {
        $error = 'Failed to place order. Please try again.';
    }
}

global $db;
$user = $db->selectOne("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - <?php echo $settings['site_name'] ?? 'eMarket'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include '../../views/layouts/header.php'; ?>
    
    <div class="checkout-section">
        <div class="container">
            <h2 class="mb-4">Checkout</h2>
            
            <?php if($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" class="checkout-form">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Shipping Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Full Name *</label>
                                        <input type="text" name="shipping_name" class="form-control" value="<?php echo $user['name'] ?? ''; ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email *</label>
                                        <input type="email" name="shipping_email" class="form-control" value="<?php echo $user['email'] ?? ''; ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Phone *</label>
                                        <input type="tel" name="shipping_phone" class="form-control" value="<?php echo $user['phone'] ?? ''; ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Country *</label>
                                        <input type="text" name="shipping_country" class="form-control" required>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Address *</label>
                                        <input type="text" name="shipping_address" class="form-control" value="<?php echo $user['address'] ?? ''; ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">City *</label>
                                        <input type="text" name="shipping_city" class="form-control" value="<?php echo $user['city'] ?? ''; ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">ZIP Code *</label>
                                        <input type="text" name="shipping_zip" class="form-control" required>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Order Notes</label>
                                        <textarea name="notes" class="form-control" rows="3" placeholder="Special instructions for your order"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0" style="color: #28a745;">Payment Method</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check mb-3">
                                    <input class="form-check-input payment-option" type="radio" name="payment_method" value="cash_on_delivery" checked onchange="togglePaymentInfo()">
                                    <label class="form-check-label fw-bold" style="color: #28a745;">Cash on Delivery</label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input payment-option" type="radio" name="payment_method" value="mpesa" onchange="togglePaymentInfo()">
                                    <label class="form-check-label fw-bold" style="color: #28a745;"> <i class="fas fa-mobile-alt"></i> M-Pesa</label>
                                </div>
                                <div id="mpesaInfo" style="display: none;" class="alert alert-info mb-3">
                                    <p class="mb-2">You will receive an STK push on your phone. Enter your M-Pesa PIN to confirm payment.</p>
                                    <small>Make sure your phone has M-Pesa registered with the number you provided above.</small>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input payment-option" type="radio" name="payment_method" value="equity" onchange="togglePaymentInfo()">
                                    <label class="form-check-label fw-bold" style="color: #28a745;"> <i class="fas fa-university"></i> Equity Bank</label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input payment-option" type="radio" name="payment_method" value="kcb" onchange="togglePaymentInfo()">
                                    <label class="form-check-label fw-bold" style="color: #28a745;"> <i class="fas fa-university"></i> KCB Bank</label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input payment-option" type="radio" name="payment_method" value="bank_transfer" onchange="togglePaymentInfo()">
                                    <label class="form-check-label fw-bold" style="color: #28a745;"> <i class="fas fa-exchange-alt"></i> Other Bank Transfer</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Order Summary</h5>
                            </div>
                            <div class="card-body">
                                <?php foreach($cart_items as $item): ?>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span><?php echo $item['name']; ?> x <?php echo $item['quantity']; ?></span>
                                        <span><?php echo formatPrice($item['price'] * $item['quantity']); ?></span>
                                    </div>
                                <?php endforeach; ?>
                                <hr>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal</span>
                                    <span><?php echo formatPrice($cart_total); ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Shipping</span>
                                    <span><?php echo formatPrice($shipping); ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tax</span>
                                    <span><?php echo formatPrice($tax); ?></span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between fw-bold">
                                    <span>Total</span>
                                    <span><?php echo formatPrice($grand_total); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mt-3">Place Order</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <?php include '../../views/layouts/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const BASE_URL = '../../';
        const ORDER_NUMBER = '<?php echo $pending_order_number; ?>';
        
        function togglePaymentInfo() {
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            const mpesaInfo = document.getElementById('mpesaInfo');
            if (paymentMethod === 'mpesa') {
                mpesaInfo.style.display = 'block';
            } else {
                mpesaInfo.style.display = 'none';
            }
        }
        
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast-notification toast-${type}`;
            toast.innerHTML = `
                <div class="toast-icon">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                </div>
                <div class="toast-message">${message}</div>
                <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
            `;
            document.body.appendChild(toast);
            setTimeout(() => toast.classList.add('show'), 10);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
        
        document.querySelector('.checkout-form')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            const form = this;
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerText;
            
            if (paymentMethod === 'mpesa') {
                submitBtn.disabled = true;
                submitBtn.innerText = 'Processing...';
                
                const phone = document.querySelector('input[name="shipping_phone"]').value;
                const amount = <?php echo round($grand_total); ?>;
                
                if (!phone) {
                    showToast('Please enter your phone number', 'error');
                    submitBtn.disabled = false;
                    submitBtn.innerText = originalText;
                    return;
                }
                
                try {
                    const response = await fetch(BASE_URL + 'controllers/mpesa-controller.php?action=initiate', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `phone=${phone}&amount=${amount}&order_number=${ORDER_NUMBER}`
                    });
                    
                    const data = await response.json();
                    
                    if (data.status === 'success') {
                        showToast('Payment request sent! Check your phone and enter PIN.', 'success');
                        
                        let attempts = 0;
                        const maxAttempts = 20;
                        
                        const checkInterval = setInterval(async () => {
                            attempts++;
                            
                            const statusResponse = await fetch(BASE_URL + 'controllers/mpesa-controller.php?action=check', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: `checkout_id=${data.checkout_id}`
                            });
                            
                            const statusData = await statusResponse.json();
                            
                            if (statusData.status === 'success') {
                                clearInterval(checkInterval);
                                showToast('Payment successful! Placing order...', 'success');
                                
                                const orderInput = document.createElement('input');
                                orderInput.type = 'hidden';
                                orderInput.name = 'order_number';
                                orderInput.value = ORDER_NUMBER;
                                form.appendChild(orderInput);
                                
                                form.submit();
                            } else if (attempts >= maxAttempts) {
                                clearInterval(checkInterval);
                                showToast('Payment timeout. Please try again or use another payment method.', 'error');
                                submitBtn.disabled = false;
                                submitBtn.innerText = originalText;
                            }
                        }, 3000);
                    } else {
                        showToast(data.message || 'Failed to initiate M-Pesa payment', 'error');
                        submitBtn.disabled = false;
                        submitBtn.innerText = originalText;
                    }
                } catch (error) {
                    showToast('Error processing payment', 'error');
                    submitBtn.disabled = false;
                    submitBtn.innerText = originalText;
                }
            } else {
                const orderInput = document.createElement('input');
                orderInput.type = 'hidden';
                orderInput.name = 'order_number';
                orderInput.value = ORDER_NUMBER;
                form.appendChild(orderInput);
                
                form.submit();
            }
        });
    </script>
</body>
</html>
