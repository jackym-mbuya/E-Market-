<?php
require_once 'auth_check.php';

require_once '../../config/functions.php';

$id = $_GET['id'] ?? 0;
$order = $db->selectOne("SELECT o.*, u.name, u.email, u.phone, u.address, u.city, u.country FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = ?", [$id]);
$items = $db->select("SELECT oi.*, p.image as product_image FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?", [$id]);
$settings = getSettings();

if(!$order) {
    echo "Order not found";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - <?php echo $order['order_number']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; }
        .invoice-header { border-bottom: 2px solid #28a745; padding-bottom: 20px; margin-bottom: 20px; }
        .invoice-title { font-size: 24px; color: #28a745; }
        .table-items th { background: #f8f9fa; }
        .text-right { text-align: right; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
            .invoice-box { border: none; }
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="invoice-header d-flex justify-content-between">
            <div>
                <h1 class="invoice-title"><?php echo $settings['site_name'] ?? 'eMarket'; ?></h1>
                <p class="mb-0"><?php echo $settings['site_address'] ?? ''; ?></p>
                <p class="mb-0">Phone: <?php echo $settings['site_phone'] ?? ''; ?></p>
                <p>Email: <?php echo $settings['site_email'] ?? ''; ?></p>
            </div>
            <div class="text-right">
                <h2>INVOICE</h2>
                <p class="mb-0"><strong>Order #:</strong> <?php echo $order['order_number']; ?></p>
                <p class="mb-0"><strong>Date:</strong> <?php echo date('M d, Y', strtotime($order['created_at'])); ?></p>
                <p><strong>Status:</strong> <?php echo ucfirst($order['order_status']); ?></p>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <h5>Bill To:</h5>
                <p class="mb-0"><strong><?php echo $order['name'] ?? 'Guest'; ?></strong></p>
                <p class="mb-0"><?php echo $order['email'] ?? ''; ?></p>
                <p class="mb-0"><?php echo $order['phone'] ?? ''; ?></p>
                <p><?php echo $order['address'] ?? ''; ?>, <?php echo $order['city'] ?? ''; ?>, <?php echo $order['country'] ?? ''; ?></p>
            </div>
            <div class="col-md-6 text-right">
                <h5>Shipping Address:</h5>
                <p class="mb-0"><?php echo $order['shipping_name'] ?? $order['name']; ?></p>
                <p class="mb-0"><?php echo $order['shipping_address'] ?? ''; ?></p>
                <p class="mb-0"><?php echo $order['shipping_city'] ?? ''; ?>, <?php echo $order['shipping_zip'] ?? ''; ?></p>
                <p><?php echo $order['shipping_country'] ?? ''; ?></p>
            </div>
        </div>
        
        <table class="table table-bordered table-items">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $item): ?>
                <tr>
                    <td>
                        <strong><?php echo $item['product_name']; ?></strong>
                    </td>
                    <td class="text-right"><?php echo formatPrice($item['product_price']); ?></td>
                    <td class="text-right"><?php echo $item['quantity']; ?></td>
                    <td class="text-right"><?php echo formatPrice($item['subtotal']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
                    <td class="text-right"><?php echo formatPrice($order['subtotal']); ?></td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right">Tax:</td>
                    <td class="text-right"><?php echo formatPrice($order['tax']); ?></td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right">Shipping:</td>
                    <td class="text-right"><?php echo formatPrice($order['shipping_cost']); ?></td>
                </tr>
                <?php if($order['discount'] > 0): ?>
                <tr>
                    <td colspan="3" class="text-right">Discount:</td>
                    <td class="text-right">-<?php echo formatPrice($order['discount']); ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td colspan="3" class="text-right"><strong>Total:</strong></td>
                    <td class="text-right"><strong><?php echo formatPrice($order['total']); ?></strong></td>
                </tr>
            </tfoot>
        </table>
        
        <div class="mt-4 no-print">
            <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Print Invoice</button>
            <a href="index.php?page=orders" class="btn btn-secondary">Back to Orders</a>
        </div>
    </div>
</body>
</html>
