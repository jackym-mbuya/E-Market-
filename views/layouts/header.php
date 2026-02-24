<?php
require_once '../../config/functions.php';

$categories = getAllCategories();
$featured_products = getProducts(8, null, true);
$daily_deals = getProducts(4);
$banners = getBanners('main');
$sidebar_banners = getBanners('sidebar');
$bottom_banners = getBanners('bottom');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo $settings['site_name'] ?? 'eMarket'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="icon" href="../../assets/images/favicon.ico">
    <style>
        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #fff;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 9999;
            transform: translateX(400px);
            transition: transform 0.3s ease;
            min-width: 300px;
            border-left: 4px solid #28a745;
        }
        .toast-notification.show { transform: translateX(0); }
        .toast-notification.toast-error { border-left-color: #dc3545; }
        .toast-icon { font-size: 20px; color: #28a745; }
        .toast-notification.toast-error .toast-icon { color: #dc3545; }
        .toast-message { flex: 1; color: #333; }
        .toast-close { background: none; border: none; font-size: 20px; cursor: pointer; color: #999; }
    </style>
</head>
<body>
    <!-- Top Header -->
    <div class="top-header">
        <div class="container">
            <div class="top-header-left">
                <span>Welcome to eMarket! Free shipping on orders over $100</span>
            </div>
            <div class="top-header-right">
                <div class="dropdown">
                    <span class="dropdown-toggle">English</span>
                </div>
                <span class="divider">|</span>
                <div class="dropdown">
                    <span class="dropdown-toggle">USD</span>
                </div>
                <span class="divider">|</span>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="<?php echo BASE_URL; ?>views/user/profile.php">My Account</a>
                    <span class="divider">|</span>
                    <a href="<?php echo BASE_URL; ?>controllers/auth-controller.php?action=logout">Logout</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>views/auth/login.php">Login</a>
                    <span class="divider">|</span>
                    <a href="<?php echo BASE_URL; ?>views/auth/register.php">Register</a>
                <?php endif; ?>
                <span class="divider">|</span>
                <a href="<?php echo BASE_URL; ?>views/user/track-order.php">Track Order</a>
                <span class="divider">|</span>
                <a href="tel:<?php echo $settings['site_phone'] ?? ''; ?>"><i class="fas fa-phone"></i> <?php echo $settings['site_phone'] ?? ''; ?></a>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="main-header">
        <div class="container">
            <a href="<?php echo BASE_URL; ?>" class="logo">eMarket</a>
            
            <form action="<?php echo BASE_URL; ?>views/product/search.php" method="GET" class="search-box">
                <select name="category">
                    <option value="">All Categories</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="q" placeholder="Search for products...">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
            
            <div class="header-actions">
                <div class="header-action">
                    <a href="<?php echo BASE_URL; ?>views/user/wishlist.php">
                        <i class="far fa-heart"></i>
                        <span class="count"><?php echo isset($_SESSION['user_id']) ? count(getWishlistItems()) : 0; ?></span>
                    </a>
                </div>
                <div class="header-action">
                    <a href="<?php echo BASE_URL; ?>views/cart/cart.php">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="count cart-count"><?php echo getCartCount(); ?></span>
                    </a>
                </div>
                <a href="<?php echo BASE_URL; ?>views/cart/cart.php" class="my-cart-btn">My Cart</a>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="main-nav">
        <div class="container">
            <div class="category-menu">
                <i class="fas fa-bars"></i>
                <span>All Departments</span>
            </div>
            <ul class="nav-menu">
                <li><a href="<?php echo BASE_URL; ?>">Home</a></li>
                <li><a href="<?php echo BASE_URL; ?>views/product/featured.php">Features</a></li>
                <li><a href="<?php echo BASE_URL; ?>views/product/best-selling.php">Best Selling</a></li>
                <li><a href="<?php echo BASE_URL; ?>views/product/deals.php">Deal</a></li>
                <li><a href="<?php echo BASE_URL; ?>views/product/shop.php">Shop</a></li>
                <li><a href="<?php echo BASE_URL; ?>views/blog/index.php">Blog</a></li>
                <li><a href="<?php echo BASE_URL; ?>views/pages/about.php">About</a></li>
                <li><a href="<?php echo BASE_URL; ?>views/pages/contact.php">Contact</a></li>
            </ul>
        </div>
    </nav>
