<?php
require_once '../../config/functions.php';

if(!isset($_SESSION['user_id'])) {
    redirect('../auth/login.php?redirect=../user/profile.php');
}

global $db;
$user = $db->selectOne("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $city = sanitize($_POST['city']);
    $country = sanitize($_POST['country']);
    
    $db->update('users', [
        'name' => $name,
        'phone' => $phone,
        'address' => $address,
        'city' => $city,
        'country' => $country
    ], 'id = :id', ['id' => $_SESSION['user_id']]);
    
    $success = 'Profile updated successfully';
    $user = $db->selectOne("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
}

$orders = getOrders($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - <?php echo $settings['site_name'] ?? 'eMarket'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include '../../views/layouts/header.php'; ?>
    
    <div class="container py-4">
        <h2 class="mb-4">My Account</h2>
        
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav flex-column">
                            <li class="nav-item"><a class="nav-link active" href="profile.php">My Profile</a></li>
                            <li class="nav-item"><a class="nav-link" href="orders.php">My Orders</a></li>
                            <li class="nav-item"><a class="nav-link" href="wishlist.php">My Wishlist</a></li>
                            <li class="nav-item"><a class="nav-link" href="track-order.php">Track Order</a></li>
                            <li class="nav-item"><a class="nav-link" href="../../controllers/auth-controller.php?action=logout">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-9">
                <?php if(isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Profile Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="name" class="form-control" value="<?php echo $user['name']; ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" value="<?php echo $user['email']; ?>" disabled>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="tel" name="phone" class="form-control" value="<?php echo $user['phone']; ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Country</label>
                                    <input type="text" name="country" class="form-control" value="<?php echo $user['country']; ?>">
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Address</label>
                                    <input type="text" name="address" class="form-control" value="<?php echo $user['address']; ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">City</label>
                                    <input type="text" name="city" class="form-control" value="<?php echo $user['city']; ?>">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </form>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">My Orders</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date #</th>
</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($orders as $order): ?>
                                    <tr>
                                        <td><?php echo $order['order_number']; ?></td>
                                        <td><?php echo formatPrice($order['total']); ?></td>
                                        <td><span class="badge bg-<?php echo $order['order_status'] == 'delivered' ? 'success' : ($order['order_status'] == 'cancelled' ? 'danger' : 'warning'); ?>"><?php echo ucfirst($order['order_status']); ?></span></td>
                                        <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                        <td><a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">View</a></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if(empty($orders)): ?>
                                    <tr><td colspan="5" class="text-center">No orders yet</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include '../../views/layouts/footer.php'; ?>
</body>
</html>
