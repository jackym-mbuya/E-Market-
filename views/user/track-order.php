<?php
require_once '../../config/functions.php';

$order = null;
$order_items = [];

if(isset($_GET['order_number'])) {
    $order_number = sanitize($_GET['order_number']);
    global $db;
    
    if(isset($_SESSION['user_id'])) {
        $order = getOrder(null, $_SESSION['user_id']);
        if($order && $order['order_number'] != $order_number) {
            $order = null;
        }
    } else {
        $order = $db->selectOne("SELECT * FROM orders WHERE order_number = ?", [$order_number]);
    }
    
    if($order) {
        $order_items = getOrderItems($order['id']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Order - <?php echo $settings['site_name'] ?? 'eMarket'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include '../../views/layouts/header.php'; ?>
    
    <div class="container py-5">
        <h2 class="mb-4">Track Your Order</h2>
        
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-auto">
                        <input type="text" name="order_number" class="form-control" placeholder="Enter order number" value="<?php echo $_GET['order_number'] ?? ''; ?>" required>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Track Order</button>
                    </div>
                </form>
            </div>
        </div>
        
        <?php if($order): ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Order #<?php echo $order['order_number']; ?></h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Order Status:</strong></p>
                            <span class="badge bg-<?php echo $order['order_status'] == 'delivered' ? 'success' : ($order['order_status'] == 'cancelled' ? 'danger' : 'warning'); ?> fs-6">
                                <?php echo ucfirst($order['order_status']); ?>
                            </span>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Payment Status:</strong></p>
                            <span class="badge bg-<?php echo $order['payment_status'] == 'paid' ? 'success' : 'warning'; ?> fs-6">
                                <?php echo ucfirst($order['payment_status']); ?>
                            </span>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Total:</strong></p>
                            <p class="fs-5 fw-bold"><?php echo formatPrice($order['total']); ?></p>
                        </div>
                    </div>
                    
                    <?php if($order['tracking_number']): ?>
                        <div class="alert alert-info">
                            <strong>Tracking Number:</strong> <?php echo $order['tracking_number']; ?>
                        </div>
                    <?php endif; ?>
                    
                    <h6>Order Items:</h6>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($order_items as $item): ?>
                                <tr>
                                    <td><?php echo $item['product_name']; ?></td>
                                    <td><?php echo formatPrice($item['product_price']); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td><?php echo formatPrice($item['subtotal']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php elseif(isset($_GET['order_number'])): ?>
            <div class="alert alert-warning">Order not found. Please check the order number and try again.</div>
        <?php endif; ?>
    </div>
    
    <?php include '../../views/layouts/footer.php'; ?>
</body>
</html>
