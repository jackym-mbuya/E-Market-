<?php
require_once 'auth_check.php';

require_once '../../config/functions.php';

$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

$admin_id = $_SESSION['admin_id'];
global $db;
$admin = $db->selectOne("SELECT * FROM admins WHERE id = ?", [$admin_id]);

// Handle logout
if($action == 'logout') {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

// Handle product actions
if($page == 'product-delete' && $id) {
    $db->delete("DELETE FROM products WHERE id = ?", [$id]);
    redirect('index.php?page=products');
}

if($page == 'product-status' && $id) {
    $status = $_GET['status'] ?? 1;
    $db->update("products", ['status' => $status], "id = :id", ['id' => $id]);
    redirect('index.php?page=products');
}

// Handle category actions
if($page == 'category-delete' && $id) {
    $db->delete("DELETE FROM categories WHERE id = ?", [$id]);
    redirect('index.php?page=categories');
}

if($page == 'category-status' && $id) {
    $status = $_GET['status'] ?? 1;
    $db->update("categories", ['status' => $status], "id = :id", ['id' => $id]);
    redirect('index.php?page=categories');
}

// Handle category form submission
if($page == 'category-form' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'name' => $_POST['name'],
        'slug' => $_POST['slug'] ?: strtolower(str_replace(' ', '-', $_POST['name'])),
        'description' => $_POST['description'],
        'icon' => $_POST['icon'],
        'featured' => isset($_POST['featured']) ? 1 : 0,
        'status' => isset($_POST['status']) ? 1 : 0
    ];
    
    if($id) {
        $db->update("categories", $data, "id = :id", ['id' => $id]);
    } else {
        $db->insert("categories", $data);
    }
    redirect('index.php?page=categories');
}

// Handle banner actions
if($page == 'banner-delete' && $id) {
    $db->delete("DELETE FROM banners WHERE id = ?", [$id]);
    redirect('index.php?page=banners');
}

if($page == 'banner-status' && $id) {
    $status = $_GET['status'] ?? 1;
    $db->update("banners", ['status' => $status], "id = :id", ['id' => $id]);
    redirect('index.php?page=banners');
}

// Handle banner form submission
if($page == 'banner-form' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'title' => $_POST['title'],
        'subtitle' => $_POST['subtitle'],
        'description' => $_POST['description'],
        'link' => $_POST['link'],
        'link_text' => $_POST['link_text'],
        'position' => $_POST['position'],
        'sort_order' => $_POST['sort_order'] ?? 0,
        'status' => isset($_POST['status']) ? 1 : 0
    ];
    
    if(isset($_FILES['image']) && $_FILES['image']['name']) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = time() . '_' . rand(1000,9999) . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], '../../uploads/banners/' . $filename);
        $data['image'] = $filename;
    }
    
    if($id) {
        $db->update("banners", $data, "id = :id", ['id' => $id]);
    } else {
        $db->insert("banners", $data);
    }
    redirect('index.php?page=banners');
}

// Handle product form submission
if($page == 'product-form' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'category_id' => $_POST['category_id'],
        'subcategory_id' => $_POST['subcategory_id'] ?: null,
        'name' => $_POST['name'],
        'slug' => !empty($_POST['slug']) ? $_POST['slug'] : strtolower(str_replace(' ', '-', $_POST['name'])),
        'short_description' => $_POST['short_description'] ?? '',
        'description' => $_POST['description'] ?? '',
        'price' => $_POST['price'],
        'old_price' => $_POST['old_price'] ?: null,
        'stock_quantity' => $_POST['stock_quantity'] ?? 0,
        'sku' => $_POST['sku'] ?? '',
        'brand' => $_POST['brand'] ?? '',
        'featured' => isset($_POST['featured']) ? 1 : 0,
        'status' => isset($_POST['status']) ? 1 : 0
    ];
    
    if($id) {
        $db->update("products", $data, "id = :id", ['id' => $id]);
    } else {
        $db->insert("products", $data);
        $id = $db->conn->lastInsertId();
    }
    
    if(isset($_FILES['image']) && $_FILES['image']['name']) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = time() . '_' . rand(1000,9999) . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], '../../uploads/products/' . $filename);
        $db->delete("DELETE FROM product_images WHERE product_id = ?", [$id]);
        $db->insert("product_images", ['product_id' => $id, 'image' => $filename, 'is_primary' => 1]);
    }
    redirect('index.php?page=products');
}

// Handle coupon actions
if($page == 'coupon-delete' && $id) {
    $db->delete("DELETE FROM coupons WHERE id = ?", [$id]);
    redirect('index.php?page=coupons');
}

if($page == 'coupon-status' && $id) {
    $status = $_GET['status'] ?? 1;
    $db->update("coupons", ['status' => $status], "id = :id", ['id' => $id]);
    redirect('index.php?page=coupons');
}

// Handle user actions
if($page == 'user-delete' && $id) {
    $db->delete("DELETE FROM users WHERE id = ?", [$id]);
    redirect('index.php?page=users');
}

if($page == 'user-status' && $id) {
    $status = $_GET['status'] ?? 1;
    $db->update("users", ['status' => $status], "id = :id", ['id' => $id]);
    redirect('index.php?page=users');
}

// Handle review actions
if($page == 'review-delete' && $id) {
    $db->delete("DELETE FROM reviews WHERE id = ?", [$id]);
    redirect('index.php?page=reviews');
}

if($page == 'review-status' && $id) {
    $status = $_GET['status'] ?? 1;
    $db->update("reviews", ['status' => $status], "id = :id", ['id' => $id]);
    redirect('index.php?page=reviews');
}

// Handle order status update
if($page == 'order-update' && $id && isset($_GET['status'])) {
    $status = $_GET['status'];
    $db->update("orders", ['order_status' => $status], "id = :id", ['id' => $id]);
    redirect('index.php?page=orders');
}

// Handle profile update (name/password)
if($page == 'profile' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['update_name'])) {
        $new_name = $_POST['name'];
        $db->update("admins", ['name' => $new_name], "id = :id", ['id' => $admin_id]);
        $_SESSION['admin_name'] = $new_name;
        $admin = $db->selectOne("SELECT * FROM admins WHERE id = ?", [$admin_id]);
        echo '<div class="alert alert-success">Name updated successfully!</div>';
    }
    if(isset($_POST['update_password'])) {
        $current_pass = $_POST['current_password'];
        $new_pass = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];
        
        $admin = $db->selectOne("SELECT * FROM admins WHERE id = ?", [$admin_id]);
        if(!password_verify($current_pass, $admin['password'])) {
            echo '<div class="alert alert-danger">Current password is incorrect!</div>';
        } elseif($new_pass != $confirm_pass) {
            echo '<div class="alert alert-danger">New passwords do not match!</div>';
        } else {
            $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
            $db->update("admins", ['password' => $hashed], "id = :id", ['id' => $admin_id]);
            echo '<div class="alert alert-success">Password updated successfully!</div>';
        }
    }
}

// Calculate stats
$stats = [
    'products' => $db->count("SELECT COUNT(*) FROM products"),
    'orders' => $db->count("SELECT COUNT(*) FROM orders"),
    'users' => $db->count("SELECT COUNT(*) FROM users"),
    'pending_orders' => $db->count("SELECT COUNT(*) FROM orders WHERE order_status = 'pending'"),
    'total_revenue' => $db->selectOne("SELECT COALESCE(SUM(total), 0) as revenue FROM orders WHERE payment_status = 'paid'")['revenue'],
    'low_stock' => $db->count("SELECT COUNT(*) FROM products WHERE stock_quantity < 10 AND stock_quantity > 0"),
    'out_of_stock' => $db->count("SELECT COUNT(*) FROM products WHERE stock_quantity = 0")
];

$recent_orders = $db->select("SELECT o.*, u.name as customer_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 10");

// Monthly revenue for chart
$monthly_revenue = $db->select("
    SELECT DATE(created_at) as date, SUM(total) as revenue 
    FROM orders 
    WHERE payment_status = 'paid' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY DATE(created_at)
    ORDER BY date
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - eMarket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #28a745; --dark: #1a1a1a; }
        body { background: #f5f5f5; }
        .admin-wrapper { display: flex; min-height: 100vh; }
        .admin-sidebar { width: 260px; background: var(--dark); color: #fff; position: fixed; height: 100vh; overflow-y: auto; }
        .sidebar-logo { padding: 20px; font-size: 22px; font-weight: bold; color: var(--primary); border-bottom: 1px solid #333; }
        .sidebar-menu { padding: 15px 0; }
        .sidebar-menu a { display: flex; align-items: center; padding: 12px 20px; color: #aaa; text-decoration: none; transition: 0.3s; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: var(--primary); color: #fff; }
        .sidebar-menu a i { width: 25px; }
        .admin-main { margin-left: 260px; flex: 1; padding: 25px; }
        .stat-card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.08); }
        .stat-card h3 { margin: 0; font-size: 28px; color: var(--dark); }
        .stat-card p { margin: 5px 0 0; color: #888; font-size: 14px; }
        .stat-card .icon { font-size: 30px; color: var(--primary); opacity: 0.5; }
        .badge-pending { background: #ffc107; color: #000; }
        .badge-processing { background: #0dcaf0; color: #fff; }
        .badge-shipped { background: #6610f2; color: #fff; }
        .badge-delivered { background: #198754; color: #fff; }
        .badge-cancelled { background: #dc3545; color: #fff; }
        .table-action-btn { padding: 4px 8px; font-size: 12px; margin-right: 3px; }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <div class="sidebar-logo"><i class="fas fa-shopping-bag"></i> eMarket</div>
            <div class="sidebar-menu">
                <a href="index.php?page=dashboard" class="<?php echo $page == 'dashboard' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="index.php?page=products" class="<?php echo in_array($page, ['products', 'product-form']) ? 'active' : ''; ?>"><i class="fas fa-box"></i> Products</a>
                <a href="index.php?page=categories" class="<?php echo in_array($page, ['categories', 'category-form', 'subcategories']) ? 'active' : ''; ?>"><i class="fas fa-list"></i> Categories</a>
                <a href="index.php?page=orders" class="<?php echo in_array($page, ['orders', 'order-view']) ? 'active' : ''; ?>"><i class="fas fa-shopping-cart"></i> Orders <span class="badge bg-warning float-end"><?php echo $stats['pending_orders']; ?></span></a>
                <a href="index.php?page=users" class="<?php echo $page == 'users' ? 'active' : ''; ?>"><i class="fas fa-users"></i> Customers</a>
                <a href="index.php?page=reviews" class="<?php echo $page == 'reviews' ? 'active' : ''; ?>"><i class="fas fa-star"></i> Reviews</a>
                <a href="index.php?page=banners" class="<?php echo in_array($page, ['banners', 'banner-form']) ? 'active' : ''; ?>"><i class="fas fa-image"></i> Banners</a>
                <a href="index.php?page=coupons" class="<?php echo in_array($page, ['coupons', 'coupon-form']) ? 'active' : ''; ?>"><i class="fas fa-tag"></i> Coupons</a>
                <a href="index.php?page=payments" class="<?php echo $page == 'payments' ? 'active' : ''; ?>"><i class="fas fa-credit-card"></i> Payments</a>
                <a href="index.php?page=reports" class="<?php echo $page == 'reports' ? 'active' : ''; ?>"><i class="fas fa-chart-bar"></i> Reports</a>
                <a href="index.php?page=profile" class="<?php echo $page == 'profile' ? 'active' : ''; ?>"><i class="fas fa-user-circle"></i> Profile</a>
                <a href="index.php?page=settings" class="<?php echo $page == 'settings' ? 'active' : ''; ?>"><i class="fas fa-cog"></i> Settings</a>
                <a href="index.php?action=logout" class="text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </aside>
        
        <main class="admin-main">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0"><?php echo ucfirst(str_replace('-', ' ', $page)); ?></h4>
                <div class="dropdown">
                    <a class="dropdown-toggle text-decoration-none text-dark" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i> <?php echo $admin['name']; ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><span class="dropdown-item-text"><small class="text-muted"><?php echo ucfirst($admin['role']); ?></small></span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="index.php?page=settings"><i class="fas fa-cog"></i> Settings</a></li>
                        <li><a class="dropdown-item text-danger" href="index.php?action=logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </div>
            </div>

            <?php 
            // ============ DASHBOARD ============
            if($page == 'dashboard'): 
            ?>
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between">
                                <div><h3><?php echo formatPrice($stats['total_revenue']); ?></h3><p>Total Revenue</p></div>
                                <div class="icon"><i class="fas fa-dollar-sign"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between">
                                <div><h3><?php echo $stats['orders']; ?></h3><p>Total Orders</p></div>
                                <div class="icon"><i class="fas fa-shopping-cart"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between">
                                <div><h3><?php echo $stats['products']; ?></h3><p>Total Products</p></div>
                                <div class="icon"><i class="fas fa-box"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between">
                                <div><h3><?php echo $stats['users']; ?></h3><p>Total Customers</p></div>
                                <div class="icon"><i class="fas fa-users"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <h6 class="text-muted mb-3">Stock Alerts</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Low Stock</span><span class="badge bg-warning"><?php echo $stats['low_stock']; ?></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Out of Stock</span><span class="badge bg-danger"><?php echo $stats['out_of_stock']; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="stat-card">
                            <h6 class="text-muted mb-3">Recent Orders</h6>
                            <table class="table table-sm">
                                <thead><tr><th>Order #</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
                                <tbody>
                                    <?php foreach($recent_orders as $order): ?>
                                    <tr>
                                        <td><?php echo $order['order_number']; ?></td>
                                        <td><?php echo $order['customer_name'] ?? 'Guest'; ?></td>
                                        <td><?php echo formatPrice($order['total']); ?></td>
                                        <td><span class="badge badge-<?php echo $order['order_status']; ?>"><?php echo ucfirst($order['order_status']); ?></span></td>
                                        <td><?php echo date('M d', strtotime($order['created_at'])); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            
            <?php 
            // ============ PRODUCTS ============
            elseif($page == 'products'): 
                $products = $db->select("SELECT p.*, c.name as category_name, (SELECT image FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC");
            ?>
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Products</h5>
                        <a href="?page=product-form" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Product</a>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover" id="dataTable">
                            <thead>
                                <tr><th>ID</th><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Featured</th><th>Status</th><th>Actions</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach($products as $p): ?>
                                <tr>
                                    <td><?php echo $p['id']; ?></td>
                                    <td><img src="<?php echo !empty($p['image']) ? BASE_URL . 'uploads/products/' . $p['image'] : BASE_URL . 'assets/images/no-image.png'; ?>" width="50" height="50" style="object-fit:cover"></td>
                                    <td><?php echo $p['name']; ?><br><small class="text-muted">SKU: <?php echo $p['sku'] ?? 'N/A'; ?></small></td>
                                    <td><?php echo $p['category_name']; ?></td>
                                    <td><?php echo formatPrice($p['price']); ?><?php if($p['old_price']): ?><br><small class="text-decoration-line-through text-muted"><?php echo formatPrice($p['old_price']); ?></small><?php endif; ?></td>
                                    <td><?php echo $p['stock_quantity']; ?></td>
                                    <td><?php echo $p['featured'] ? '<i class="fas fa-star text-warning"></i>' : '-'; ?></td>
                                    <td><span class="badge bg-<?php echo $p['status'] ? 'success' : 'danger'; ?>"><?php echo $p['status'] ? 'Active' : 'Inactive'; ?></span></td>
                                    <td>
                                        <a href="?page=product-form&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-primary table-action-btn"><i class="fas fa-edit"></i></a>
                                        <a href="?page=product-status&id=<?php echo $p['id']; ?>&status=<?php echo $p['status'] ? 0 : 1; ?>" class="btn btn-sm btn-<?php echo $p['status'] ? 'warning' : 'success'; ?>" title="<?php echo $p['status'] ? 'Deactivate' : 'Activate'; ?>"><i class="fas fa-<?php echo $p['status'] ? 'eye-slash' : 'eye'; ?>"></i></a>
                                        <a href="?page=product-delete&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?')"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            
            <?php 
            // ============ PRODUCT FORM ============
            elseif($page == 'product-form'): 
                $product = $id ? $db->selectOne("SELECT p.*, (SELECT image FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image FROM products p WHERE p.id = ?", [$id]) : null;
                $categories = $db->select("SELECT * FROM categories WHERE status = 1 ORDER BY name");
                $subcategories = $product ? $db->select("SELECT * FROM subcategories WHERE category_id = ? ORDER BY name", [$product['category_id']]) : [];
            ?>
                <div class="card">
                    <div class="card-header"><h5 class="mb-0"><?php echo $id ? 'Edit' : 'Add'; ?> Product</h5></div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label">Product Name</label>
                                        <input type="text" name="name" class="form-control" value="<?php echo $product['name'] ?? ''; ?>" required>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Category</label>
                                            <select name="category_id" class="form-select" required>
                                                <option value="">Select Category</option>
                                                <?php foreach($categories as $cat): ?>
                                                <option value="<?php echo $cat['id']; ?>" <?php echo $product['category_id'] == $cat['id'] ? 'selected' : ''; ?>><?php echo $cat['name']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Subcategory</label>
                                            <select name="subcategory_id" class="form-select">
                                                <option value="">Select Subcategory</option>
                                                <?php foreach($subcategories as $sub): ?>
                                                <option value="<?php echo $sub['id']; ?>" <?php echo $product['subcategory_id'] == $sub['id'] ? 'selected' : ''; ?>><?php echo $sub['name']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Price</label>
                                            <input type="number" step="0.01" name="price" class="form-control" value="<?php echo $product['price'] ?? ''; ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Old Price</label>
                                            <input type="number" step="0.01" name="old_price" class="form-control" value="<?php echo $product['old_price'] ?? ''; ?>">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">SKU</label>
                                            <input type="text" name="sku" class="form-control" value="<?php echo $product['sku'] ?? ''; ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Stock Quantity</label>
                                            <input type="number" name="stock_quantity" class="form-control" value="<?php echo $product['stock_quantity'] ?? 0; ?>">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Brand</label>
                                        <input type="text" name="brand" class="form-control" value="<?php echo $product['brand'] ?? ''; ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Short Description</label>
                                        <textarea name="short_description" class="form-control" rows="2"><?php echo $product['short_description'] ?? ''; ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Full Description</label>
                                        <textarea name="description" class="form-control" rows="5"><?php echo $product['description'] ?? ''; ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Product Image</label>
                                        <input type="file" name="image" class="form-control" accept="image/*">
                                        <?php if($product && isset($product['image']) && $product['image']): ?>
                                        <img src="<?php echo BASE_URL . 'uploads/products/' . $product['image']; ?>" class="mt-2 img-fluid">
                                        <?php endif; ?>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" name="featured" class="form-check-input" id="featured" <?php echo $product['featured'] ?? 0 ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="featured">Featured Product</label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" name="status" class="form-check-input" id="status" <?php echo isset($product['status']) ? ($product['status'] ? 'checked' : '') : 'checked'; ?>>
                                            <label class="form-check-label" for="status">Active</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Save Product</button>
                            <a href="?page=products" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            
            <?php 
            // ============ CATEGORIES ============
            elseif($page == 'categories'): 
                $categories = $db->select("SELECT c.*, (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count FROM categories c ORDER BY c.name");
            ?>
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Categories</h5>
                        <a href="?page=category-form" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Category</a>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead><tr><th>ID</th><th>Name</th><th>Slug</th><th>Products</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody>
                                <?php foreach($categories as $cat): ?>
                                <tr>
                                    <td><?php echo $cat['id']; ?></td>
                                    <td><?php echo $cat['name']; ?></td>
                                    <td><?php echo $cat['slug']; ?></td>
                                    <td><span class="badge bg-info"><?php echo $cat['product_count']; ?></span></td>
                                    <td><span class="badge bg-<?php echo $cat['status'] ? 'success' : 'danger'; ?>"><?php echo $cat['status'] ? 'Active' : 'Inactive'; ?></span></td>
                                    <td>
                                        <a href="?page=category-form&id=<?php echo $cat['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                        <a href="?page=subcategories&category_id=<?php echo $cat['id']; ?>" class="btn btn-sm btn-info"><i class="fas fa-list"></i></a>
                                        <a href="?page=category-status&id=<?php echo $cat['id']; ?>&status=<?php echo $cat['status'] ? 0 : 1; ?>" class="btn btn-sm btn-<?php echo $cat['status'] ? 'warning' : 'success'; ?>"><i class="fas fa-<?php echo $cat['status'] ? 'eye-slash' : 'eye'; ?>"></i></a>
                                        <a href="?page=category-delete&id=<?php echo $cat['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this category?')"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            
            <?php 
            // ============ CATEGORY FORM ============
            elseif($page == 'category-form'): 
                $category = $id ? $db->selectOne("SELECT * FROM categories WHERE id = ?", [$id]) : null;
            ?>
                <div class="card">
                    <div class="card-header"><h5 class="mb-0"><?php echo $id ? 'Edit' : 'Add'; ?> Category</h5></div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Category Name</label>
                                    <input type="text" name="name" class="form-control" value="<?php echo $category['name'] ?? ''; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Slug</label>
                                    <input type="text" name="slug" class="form-control" value="<?php echo $category['slug'] ?? ''; ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Icon (FontAwesome class)</label>
                                    <input type="text" name="icon" class="form-control" value="<?php echo $category['icon'] ?? ''; ?>" placeholder="fa fa-box">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="form-check mt-2">
                                        <input type="checkbox" name="featured" class="form-check-input" id="featured" <?php echo $category['featured'] ?? 0 ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="featured">Featured Category</label>
                                    </div>
                                    <div class="form-check mt-2">
                                        <input type="checkbox" name="status" class="form-check-input" id="status" <?php echo isset($category['status']) ? ($category['status'] ? 'checked' : '') : 'checked'; ?>>
                                        <label class="form-check-label" for="status">Active</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3"><?php echo $category['description'] ?? ''; ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Save Category</button>
                            <a href="?page=categories" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            
            <?php 
            // ============ SUBCATEGORIES ============
            elseif($page == 'subcategories'): 
                $category_id = $_GET['category_id'] ?? 0;
                $category = $db->selectOne("SELECT * FROM categories WHERE id = ?", [$category_id]);
                $subcategories = $db->select("SELECT s.*, (SELECT COUNT(*) FROM products WHERE subcategory_id = s.id) as product_count FROM subcategories s WHERE s.category_id = ? ORDER BY s.name", [$category_id]);
                
                if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
                    $data = [
                        'category_id' => $category_id,
                        'name' => $_POST['name'],
                        'slug' => strtolower(str_replace(' ', '-', $_POST['name'])),
                        'status' => isset($_POST['status']) ? 1 : 0
                    ];
                    $db->insert("subcategories", $data);
                    redirect('?page=subcategories&category_id=' . $category_id);
                }
                
                if(isset($_GET['delete_sub'])) {
                    $db->delete("DELETE FROM subcategories WHERE id = ?", [$_GET['delete_sub']]);
                    redirect('?page=subcategories&category_id=' . $category_id);
                }
            ?>
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between">
                        <h5 class="mb-0">Subcategories: <?php echo $category['name']; ?></h5>
                        <a href="?page=categories" class="btn btn-secondary btn-sm">Back</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="row g-3 mb-4">
                            <div class="col-md-4">
                                <input type="text" name="name" class="form-control" placeholder="Subcategory Name" required>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check mt-2">
                                    <input type="checkbox" name="status" class="form-check-input" id="substatus" checked>
                                    <label class="form-check-label" for="substatus">Active</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">Add Subcategory</button>
                            </div>
                        </form>
                        <table class="table table-hover">
                            <thead><tr><th>ID</th><th>Name</th><th>Slug</th><th>Products</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody>
                                <?php foreach($subcategories as $sub): ?>
                                <tr>
                                    <td><?php echo $sub['id']; ?></td>
                                    <td><?php echo $sub['name']; ?></td>
                                    <td><?php echo $sub['slug']; ?></td>
                                    <td><span class="badge bg-info"><?php echo $sub['product_count']; ?></span></td>
                                    <td><span class="badge bg-<?php echo $sub['status'] ? 'success' : 'danger'; ?>"><?php echo $sub['status'] ? 'Active' : 'Inactive'; ?></span></td>
                                    <td>
                                        <a href="?page=subcategories&category_id=<?php echo $category_id; ?>&delete_sub=<?php echo $sub['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            
            <?php 
            // ============ ORDERS ============
            elseif($page == 'orders'): 
                $orders = $db->select("SELECT o.*, u.name as customer_name, u.email as customer_email FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC");
            ?>
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h5 class="mb-0">Orders</h5>
                        <a href="?page=orders&export=csv" class="btn btn-success btn-sm"><i class="fas fa-download"></i> Export CSV</a>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead><tr><th>Order #</th><th>Customer</th><th>Items</th><th>Total</th><th>Payment</th><th>Order Status</th><th>Date</th><th>Actions</th></tr></thead>
                            <tbody>
                                <?php foreach($orders as $order): 
                                    $item_count = $db->count("SELECT SUM(quantity) FROM order_items WHERE order_id = ?", [$order['id']]);
                                ?>
                                <tr>
                                    <td><?php echo $order['order_number']; ?></td>
                                    <td><?php echo $order['customer_name'] ?? 'Guest'; ?><br><small class="text-muted"><?php echo $order['customer_email'] ?? ''; ?></small></td>
                                    <td><?php echo $item_count; ?></td>
                                    <td><?php echo formatPrice($order['total']); ?></td>
                                    <td><span class="badge bg-<?php echo $order['payment_status'] == 'paid' ? 'success' : 'warning'; ?>"><?php echo ucfirst($order['payment_status']); ?></span></td>
                                    <td><span class="badge badge-<?php echo $order['order_status']; ?>"><?php echo ucfirst($order['order_status']); ?></span></td>
                                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <a href="?page=order-view&id=<?php echo $order['id']; ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                        <a href="order-print.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-secondary" target="_blank"><i class="fas fa-print"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            
            <?php 
            // ============ ORDER VIEW ============
            elseif($page == 'order-view'): 
                $order = $db->selectOne("SELECT o.*, u.name, u.email, u.phone, u.address, u.city, u.country FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = ?", [$id]);
                $items = $db->select("SELECT oi.*, p.image as product_image FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?", [$id]);
                
                if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_status'])) {
                    $db->update("orders", ['order_status' => $_POST['order_status']], "id = :id", ['id' => $id]);
                    $order = $db->selectOne("SELECT o.*, u.name, u.email, u.phone, u.address, u.city, u.country FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = ?", [$id]);
                }
            ?>
                <div class="row">
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between">
                                <h5 class="mb-0">Order #<?php echo $order['order_number']; ?></h5>
                                <a href="?page=order-print&id=<?php echo $id; ?>" class="btn btn-sm btn-secondary" target="_blank"><i class="fas fa-print"></i> Print Invoice</a>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Total</th></tr></thead>
                                    <tbody>
                                        <?php foreach($items as $item): ?>
                                        <tr>
                                            <td><?php echo $item['product_name']; ?></td>
                                            <td><?php echo formatPrice($item['product_price']); ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td><?php echo formatPrice($item['subtotal']); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr><td colspan="3" class="text-end">Subtotal:</td><td><?php echo formatPrice($order['subtotal']); ?></td></tr>
                                        <tr><td colspan="3" class="text-end">Tax:</td><td><?php echo formatPrice($order['tax']); ?></td></tr>
                                        <tr><td colspan="3" class="text-end">Shipping:</td><td><?php echo formatPrice($order['shipping_cost']); ?></td></tr>
                                        <?php if($order['discount'] > 0): ?>
                                        <tr><td colspan="3" class="text-end">Discount:</td><td>-<?php echo formatPrice($order['discount']); ?></td></tr>
                                        <?php endif; ?>
                                        <tr><td colspan="3" class="text-end"><strong>Total:</strong></td><td><strong><?php echo formatPrice($order['total']); ?></strong></td></tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-header"><h6 class="mb-0">Customer Info</h6></div>
                            <div class="card-body">
                                <p class="mb-1"><strong>Name:</strong> <?php echo $order['name'] ?? 'Guest'; ?></p>
                                <p class="mb-1"><strong>Email:</strong> <?php echo $order['email'] ?? 'N/A'; ?></p>
                                <p class="mb-1"><strong>Phone:</strong> <?php echo $order['phone'] ?? 'N/A'; ?></p>
                                <p class="mb-0"><strong>Address:</strong> <?php echo $order['shipping_address'] ?? 'N/A'; ?></p>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header"><h6 class="mb-0">Update Status</h6></div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <select name="order_status" class="form-select">
                                            <option value="pending" <?php echo $order['order_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="processing" <?php echo $order['order_status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                            <option value="shipped" <?php echo $order['order_status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                            <option value="delivered" <?php echo $order['order_status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                            <option value="cancelled" <?php echo $order['order_status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">Update Status</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            
            <?php 
            // ============ USERS ============
            elseif($page == 'users'): 
                $users = $db->select("SELECT u.*, (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as order_count FROM users u ORDER BY u.created_at DESC");
            ?>
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Customers</h5></div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Orders</th><th>Status</th><th>Joined</th><th>Actions</th></tr></thead>
                            <tbody>
                                <?php foreach($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo $user['name']; ?></td>
                                    <td><?php echo $user['email']; ?></td>
                                    <td><?php echo $user['phone'] ?? '-'; ?></td>
                                    <td><span class="badge bg-info"><?php echo $user['order_count']; ?></span></td>
                                    <td><span class="badge bg-<?php echo $user['status'] ? 'success' : 'danger'; ?>"><?php echo $user['status'] ? 'Active' : 'Blocked'; ?></span></td>
                                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <a href="?page=user-view&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                        <a href="?page=user-status&id=<?php echo $user['id']; ?>&status=<?php echo $user['status'] ? 0 : 1; ?>" class="btn btn-sm btn-<?php echo $user['status'] ? 'warning' : 'success'; ?>"><i class="fas fa-<?php echo $user['status'] ? 'ban' : 'check'; ?>"></i></a>
                                        <a href="?page=user-delete&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            
            <?php 
            // ============ BANNERS ============
            elseif($page == 'banners'): 
                $banners = $db->select("SELECT * FROM banners ORDER BY sort_order, id DESC");
            ?>
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h5 class="mb-0">Banners</h5>
                        <a href="?page=banner-form" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Banner</a>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead><tr><th>ID</th><th>Image</th><th>Title</th><th>Position</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody>
                                <?php foreach($banners as $banner): ?>
                                <tr>
                                    <td><?php echo $banner['id']; ?></td>
                                    <td><img src="<?php echo BASE_URL . 'uploads/banners/' . $banner['image']; ?>" width="100" style="object-fit:cover"></td>
                                    <td><?php echo $banner['title']; ?><br><small><?php echo $banner['subtitle']; ?></small></td>
                                    <td><span class="badge bg-secondary"><?php echo ucfirst($banner['position']); ?></span></td>
                                    <td><span class="badge bg-<?php echo $banner['status'] ? 'success' : 'danger'; ?>"><?php echo $banner['status'] ? 'Active' : 'Inactive'; ?></span></td>
                                    <td>
                                        <a href="?page=banner-form&id=<?php echo $banner['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                        <a href="?page=banner-status&id=<?php echo $banner['id']; ?>&status=<?php echo $banner['status'] ? 0 : 1; ?>" class="btn btn-sm btn-<?php echo $banner['status'] ? 'warning' : 'success'; ?>"><i class="fas fa-<?php echo $banner['status'] ? 'eye-slash' : 'eye'; ?>"></i></a>
                                        <a href="?page=banner-delete&id=<?php echo $banner['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            
            <?php 
            // ============ BANNER FORM ============
            elseif($page == 'banner-form'): 
                $banner = $id ? $db->selectOne("SELECT * FROM banners WHERE id = ?", [$id]) : null;
            ?>
                <div class="card">
                    <div class="card-header"><h5 class="mb-0"><?php echo $id ? 'Edit' : 'Add'; ?> Banner</h5></div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label">Title</label>
                                        <input type="text" name="title" class="form-control" value="<?php echo $banner['title'] ?? ''; ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Subtitle</label>
                                        <input type="text" name="subtitle" class="form-control" value="<?php echo $banner['subtitle'] ?? ''; ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea name="description" class="form-control" rows="3"><?php echo $banner['description'] ?? ''; ?></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Link URL</label>
                                            <input type="text" name="link" class="form-control" value="<?php echo $banner['link'] ?? ''; ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Button Text</label>
                                            <input type="text" name="link_text" class="form-control" value="<?php echo $banner['link_text'] ?? ''; ?>">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Position</label>
                                            <select name="position" class="form-select">
                                                <option value="main" <?php echo ($banner['position'] ?? '') == 'main' ? 'selected' : ''; ?>>Main Banner</option>
                                                <option value="sidebar" <?php echo ($banner['position'] ?? '') == 'sidebar' ? 'selected' : ''; ?>>Sidebar</option>
                                                <option value="bottom" <?php echo ($banner['position'] ?? '') == 'bottom' ? 'selected' : ''; ?>>Bottom</option>
                                                <option value="popup" <?php echo ($banner['position'] ?? '') == 'popup' ? 'selected' : ''; ?>>Popup</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Sort Order</label>
                                            <input type="number" name="sort_order" class="form-control" value="<?php echo $banner['sort_order'] ?? 0; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Banner Image</label>
                                        <input type="file" name="image" class="form-control" accept="image/*" <?php echo $id ? '' : 'required'; ?>>
                                        <?php if($banner && $banner['image']): ?>
                                        <img src="<?php echo BASE_URL . 'uploads/banners/' . $banner['image']; ?>" class="mt-2 img-fluid">
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="status" class="form-check-input" id="bstatus" <?php echo isset($banner['status']) ? ($banner['status'] ? 'checked' : '') : 'checked'; ?>>
                                        <label class="form-check-label" for="bstatus">Active</label>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Save Banner</button>
                            <a href="?page=banners" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            
            <?php 
            // ============ COUPONS ============
            elseif($page == 'coupons'): 
                $coupons = $db->select("SELECT * FROM coupons ORDER BY id DESC");
            ?>
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h5 class="mb-0">Coupons</h5>
                        <a href="?page=coupon-form" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Coupon</a>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead><tr><th>Code</th><th>Type</th><th>Value</th><th>Min Order</th><th>Uses</th><th>Valid Until</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody>
                                <?php foreach($coupons as $coupon): ?>
                                <tr>
                                    <td><code><?php echo $coupon['code']; ?></code></td>
                                    <td><span class="badge bg-info"><?php echo ucfirst($coupon['discount_type']); ?></span></td>
                                    <td><?php echo $coupon['discount_type'] == 'percentage' ? $coupon['discount_value'] . '%' : formatPrice($coupon['discount_value']); ?></td>
                                    <td><?php echo formatPrice($coupon['min_order_amount']); ?></td>
                                    <td><?php echo $coupon['used_count']; ?><?php echo $coupon['max_uses'] ? '/' . $coupon['max_uses'] : ''; ?></td>
                                    <td><?php echo $coupon['valid_until'] ? date('M d, Y', strtotime($coupon['valid_until'])) : 'No expiry'; ?></td>
                                    <td><span class="badge bg-<?php echo $coupon['status'] ? 'success' : 'danger'; ?>"><?php echo $coupon['status'] ? 'Active' : 'Inactive'; ?></span></td>
                                    <td>
                                        <a href="?page=coupon-form&id=<?php echo $coupon['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                        <a href="?page=coupon-status&id=<?php echo $coupon['id']; ?>&status=<?php echo $coupon['status'] ? 0 : 1; ?>" class="btn btn-sm btn-<?php echo $coupon['status'] ? 'warning' : 'success'; ?>"><i class="fas fa-<?php echo $coupon['status'] ? 'eye-slash' : 'eye'; ?>"></i></a>
                                        <a href="?page=coupon-delete&id=<?php echo $coupon['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            
            <?php 
            // ============ COUPON FORM ============
            elseif($page == 'coupon-form'): 
                $coupon = $id ? $db->selectOne("SELECT * FROM coupons WHERE id = ?", [$id]) : null;
                
                if($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $data = [
                        'code' => strtoupper($_POST['code']),
                        'discount_type' => $_POST['discount_type'],
                        'discount_value' => $_POST['discount_value'],
                        'min_order_amount' => $_POST['min_order_amount'] ?? 0,
                        'max_uses' => $_POST['max_uses'] ?: null,
                        'valid_from' => $_POST['valid_from'] ?: null,
                        'valid_until' => $_POST['valid_until'] ?: null,
                        'status' => isset($_POST['status']) ? 1 : 0
                    ];
                    
                    if($id) {
                        $db->update("coupons", $data, "id = :id", ['id' => $id]);
                    } else {
                        $db->insert("coupons", $data);
                    }
                    redirect('index.php?page=coupons');
                }
            ?>
                <div class="card">
                    <div class="card-header"><h5 class="mb-0"><?php echo $id ? 'Edit' : 'Add'; ?> Coupon</h5></div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Coupon Code</label>
                                    <input type="text" name="code" class="form-control" value="<?php echo $coupon['code'] ?? ''; ?>" required style="text-transform: uppercase;">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Discount Type</label>
                                    <select name="discount_type" class="form-select">
                                        <option value="percentage" <?php echo ($coupon['discount_type'] ?? '') == 'percentage' ? 'selected' : ''; ?>>Percentage (%)</option>
                                        <option value="fixed" <?php echo ($coupon['discount_type'] ?? '') == 'fixed' ? 'selected' : ''; ?>>Fixed Amount</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Discount Value</label>
                                    <input type="number" step="0.01" name="discount_value" class="form-control" value="<?php echo $coupon['discount_value'] ?? ''; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Minimum Order Amount</label>
                                    <input type="number" step="0.01" name="min_order_amount" class="form-control" value="<?php echo $coupon['min_order_amount'] ?? 0; ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Max Uses</label>
                                    <input type="number" name="max_uses" class="form-control" value="<?php echo $coupon['max_uses'] ?? ''; ?>" placeholder="Leave empty for unlimited">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="form-check mt-2">
                                        <input type="checkbox" name="status" class="form-check-input" id="cstatus" <?php echo isset($coupon['status']) ? ($coupon['status'] ? 'checked' : '') : 'checked'; ?>>
                                        <label class="form-check-label" for="cstatus">Active</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Valid From</label>
                                    <input type="date" name="valid_from" class="form-control" value="<?php echo $coupon['valid_from'] ?? ''; ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Valid Until</label>
                                    <input type="date" name="valid_until" class="form-control" value="<?php echo $coupon['valid_until'] ?? ''; ?>">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Save Coupon</button>
                            <a href="?page=coupons" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            
            <?php 
            // ============ REVIEWS ============
            elseif($page == 'reviews'): 
                $reviews = $db->select("SELECT r.*, p.name as product_name, u.name as user_name FROM reviews r LEFT JOIN products p ON r.product_id = p.id LEFT JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC");
            ?>
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Product Reviews</h5></div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead><tr><th>ID</th><th>Product</th><th>User</th><th>Rating</th><th>Review</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
                            <tbody>
                                <?php foreach($reviews as $review): ?>
                                <tr>
                                    <td><?php echo $review['id']; ?></td>
                                    <td><?php echo $review['product_name']; ?></td>
                                    <td><?php echo $review['user_name'] ?? 'Unknown'; ?></td>
                                    <td><?php echo str_repeat('★', $review['rating']); ?></td>
                                    <td><?php echo substr($review['review_text'], 0, 50); ?>...</td>
                                    <td><span class="badge bg-<?php echo $review['status'] ? 'success' : 'danger'; ?>"><?php echo $review['status'] ? 'Approved' : 'Pending'; ?></span></td>
                                    <td><?php echo date('M d, Y', strtotime($review['created_at'])); ?></td>
                                    <td>
                                        <a href="?page=review-status&id=<?php echo $review['id']; ?>&status=<?php echo $review['status'] ? 0 : 1; ?>" class="btn btn-sm btn-<?php echo $review['status'] ? 'warning' : 'success'; ?>"><i class="fas fa-<?php echo $review['status'] ? 'eye-slash' : 'eye'; ?>"></i></a>
                                        <a href="?page=review-delete&id=<?php echo $review['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            
            <?php 
            // ============ PAYMENTS ============
            elseif($page == 'payments'): 
                $payments = $db->select("SELECT p.*, o.order_number FROM payments p LEFT JOIN orders o ON p.order_id = o.id ORDER BY p.created_at DESC");
            ?>
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Payment Records</h5></div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead><tr><th>ID</th><th>Order #</th><th>Method</th><th>Transaction ID</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
                            <tbody>
                                <?php foreach($payments as $payment): ?>
                                <tr>
                                    <td><?php echo $payment['id']; ?></td>
                                    <td><?php echo $payment['order_number']; ?></td>
                                    <td><?php echo ucfirst($payment['payment_method']); ?></td>
                                    <td><code><?php echo $payment['transaction_id'] ?? 'N/A'; ?></code></td>
                                    <td><?php echo formatPrice($payment['amount']); ?></td>
                                    <td><span class="badge bg-<?php echo $payment['status'] == 'completed' ? 'success' : ($payment['status'] == 'failed' ? 'danger' : 'warning'); ?>"><?php echo ucfirst($payment['status']); ?></span></td>
                                    <td><?php echo date('M d, Y H:i', strtotime($payment['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            
            <?php 
            // ============ REPORTS ============
            elseif($page == 'reports'): 
                $date_from = $_GET['date_from'] ?? date('Y-m-01');
                $date_to = $_GET['date_to'] ?? date('Y-m-d');
                
                $sales_by_date = $db->select("
                    SELECT DATE(created_at) as date, COUNT(*) as orders, SUM(total) as revenue 
                    FROM orders 
                    WHERE payment_status = 'paid' AND DATE(created_at) BETWEEN ? AND ?
                    GROUP BY DATE(created_at)
                    ORDER BY date
                ", [$date_from, $date_to]);
                
                $sales_by_product = $db->select("
                    SELECT p.name, SUM(oi.quantity) as sold, SUM(oi.subtotal) as revenue
                    FROM order_items oi
                    JOIN products p ON oi.product_id = p.id
                    JOIN orders o ON oi.order_id = o.id
                    WHERE o.payment_status = 'paid' AND DATE(o.created_at) BETWEEN ? AND ?
                    GROUP BY p.id
                    ORDER BY revenue DESC
                    LIMIT 10
                ", [$date_from, $date_to]);
                
                $total_revenue = array_sum(array_column($sales_by_date, 'revenue'));
                $total_orders = array_sum(array_column($sales_by_date, 'orders'));
            ?>
                <div class="card mb-4">
                    <div class="card-header"><h5 class="mb-0">Filter</h5></div>
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <input type="hidden" name="page" value="reports">
                            <div class="col-md-3">
                                <label class="form-label">From Date</label>
                                <input type="date" name="date_from" class="form-control" value="<?php echo $date_from; ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">To Date</label>
                                <input type="date" name="date_to" class="form-control" value="<?php echo $date_to; ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="stat-card">
                            <h3><?php echo formatPrice($total_revenue); ?></h3><p>Total Revenue</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="stat-card">
                            <h3><?php echo $total_orders; ?></h3><p>Total Orders</p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header"><h6 class="mb-0">Sales by Product</h6></div>
                            <div class="card-body">
                                <table class="table table-sm">
                                    <thead><tr><th>Product</th><th>Sold</th><th>Revenue</th></tr></thead>
                                    <tbody>
                                        <?php foreach($sales_by_product as $sp): ?>
                                        <tr><td><?php echo $sp['name']; ?></td><td><?php echo $sp['sold']; ?></td><td><?php echo formatPrice($sp['revenue']); ?></td></tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            
            <?php 
            // ============ PROFILE ============
            elseif($page == 'profile'): 
            ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header"><h5 class="mb-0"><i class="fas fa-user"></i> Change Username</h5></div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label class="form-label">Current Name</label>
                                        <input type="text" class="form-control" value="<?php echo $admin['name']; ?>" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">New Name</label>
                                        <input type="text" name="name" class="form-control" required>
                                    </div>
                                    <button type="submit" name="update_name" class="btn btn-primary">Update Name</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header"><h5 class="mb-0"><i class="fas fa-lock"></i> Change Password</h5></div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label class="form-label">Current Password</label>
                                        <input type="password" name="current_password" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">New Password</label>
                                        <input type="password" name="new_password" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Confirm New Password</label>
                                        <input type="password" name="confirm_password" class="form-control" required>
                                    </div>
                                    <button type="submit" name="update_password" class="btn btn-primary">Update Password</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Profile Information</h5></div>
                    <div class="card-body">
                        <table class="table">
                            <tr><th>Name:</th><td><?php echo $admin['name']; ?></td></tr>
                            <tr><th>Email:</th><td><?php echo $admin['email']; ?></td></tr>
                            <tr><th>Role:</th><td><span class="badge bg-info"><?php echo ucfirst($admin['role']); ?></span></td></tr>
                            <tr><th>Status:</th><td><span class="badge bg-success"><?php echo $admin['status'] ? 'Active' : 'Inactive'; ?></span></td></tr>
                            <tr><th>Joined:</th><td><?php echo date('M d, Y', strtotime($admin['created_at'])); ?></td></tr>
                        </table>
                    </div>
                </div>
            
            <?php 
            // ============ SETTINGS ============
            elseif($page == 'settings'): 
                if($_SERVER['REQUEST_METHOD'] == 'POST') {
                    foreach($_POST as $key => $value) {
                        $existing = $db->selectOne("SELECT id FROM settings WHERE setting_key = ?", [$key]);
                        if($existing) {
                            $db->update("settings", ['setting_value' => $value], "setting_key = :key", ['key' => $key]);
                        } else {
                            $db->insert("settings", ['setting_key' => $key, 'setting_value' => $value]);
                        }
                    }
                    $settings = getSettings();
                    echo '<div class="alert alert-success">Settings saved successfully!</div>';
                }
                $settings = getSettings();
            ?>
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Site Settings</h5></div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Site Name</label>
                                    <input type="text" name="site_name" class="form-control" value="<?php echo $settings['site_name'] ?? 'eMarket'; ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Site Email</label>
                                    <input type="email" name="site_email" class="form-control" value="<?php echo $settings['site_email'] ?? ''; ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Site Phone</label>
                                    <input type="text" name="site_phone" class="form-control" value="<?php echo $settings['site_phone'] ?? ''; ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Currency Symbol</label>
                                    <input type="text" name="currency_symbol" class="form-control" value="<?php echo $settings['currency_symbol'] ?? '$'; ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Site Address</label>
                                <textarea name="site_address" class="form-control" rows="2"><?php echo $settings['site_address'] ?? ''; ?></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Tax Rate (%)</label>
                                    <input type="number" step="0.01" name="tax_rate" class="form-control" value="<?php echo $settings['tax_rate'] ?? 0; ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Shipping Cost</label>
                                    <input type="number" step="0.01" name="shipping_cost" class="form-control" value="<?php echo $settings['shipping_cost'] ?? 0; ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Free Shipping Amount</label>
                                    <input type="number" step="0.01" name="free_shipping_amount" class="form-control" value="<?php echo $settings['free_shipping_amount'] ?? 100; ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Facebook</label>
                                    <input type="text" name="facebook" class="form-control" value="<?php echo $settings['facebook'] ?? ''; ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Twitter</label>
                                    <input type="text" name="twitter" class="form-control" value="<?php echo $settings['twitter'] ?? ''; ?>">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Save Settings</button>
                        </form>
                    </div>
                </div>
            
            <?php else: ?>
                <div class="alert alert-info">Select a menu item from the sidebar.</div>
            <?php endif; ?>
            
            </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
