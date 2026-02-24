<?php
require_once '../../config/functions.php';

$order_number = $_GET['order'] ?? '';
$order = null;

if($order_number) {
    global $db;
    $order = $db->selectOne("SELECT * FROM orders WHERE order_number = ?", [$order_number]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success - <?php echo $settings['site_name'] ?? 'eMarket'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .success-page { padding: 60px 0; text-align: center; }
        .success-icon { font-size: 80px; color: #28a745; margin-bottom: 20px; }
    </style>
</head>
<body>
    <?php include '../../views/layouts/header.php'; ?>
    
    <div class="success-page">
        <div class="container">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2 class="mb-3">Thank You!</h2>
            <p class="lead">Your order has been placed successfully.</p>
            
            <?php if($order): ?>
                <div class="card mx-auto" style="max-width: 500px;">
                    <div class="card-body">
                        <p><strong>Order Number:</strong> <?php echo $order['order_number']; ?></p>
                        <p><strong>Total Amount:</strong> <?php echo formatPrice($order['total']); ?></p>
                        <p><strong>Payment Method:</strong> <?php echo ucfirst(str_replace('_', ' ', $order['payment_method'])); ?></p>
                        <p class="text-muted">A confirmation email has been sent to your email address.</p>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="mt-4">
                <a href="../../index.php" class="btn btn-primary">Continue Shopping</a>
                <a href="../user/track-order.php?order_number=<?php echo $order_number; ?>" class="btn btn-outline">Track Order</a>
            </div>
        </div>
    </div>
    
    <?php include '../../views/layouts/footer.php'; ?>
</body>
</html>
