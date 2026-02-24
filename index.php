<?php
$page_title = 'Home';
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'config/functions.php';

$categories = getAllCategories();
$featured_products = getProducts(8, null, true);
$daily_deals = getProducts(4);
$all_products = getProducts(12);
$main_banners = getBanners('main');
$sidebar_banners = getBanners('sidebar');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $settings['site_name'] ?? 'eMarket'; ?> - Your Online Shopping Destination</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
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
        
        /* Departments Dropdown */
        .departments-wrapper { position: relative; }
        .departments-menu {
            position: absolute;
            top: 100%;
            left: 0;
            width: 280px;
            background: #fff;
            display: none;
            z-index: 9999;
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }
        .departments-menu.active { display: block; }
        .departments-menu ul { margin: 0; padding: 0; }
        .departments-menu li { border-bottom: 1px solid #eee; }
        .departments-menu li:last-child { border-bottom: none; }
        .departments-menu a {
            display: block;
            padding: 12px 15px;
            color: #333;
            text-decoration: none;
            transition: 0.3s;
        }
        .departments-menu a:hover { background: #28a745; color: #fff; }
        .departments-menu a i { width: 25px; }
        
        /* Left Banner */
        .left-banner { position: relative; z-index: 1; }
        .left-banner img { border-radius: 8px; width: 100%; height: auto; }
    </style>
</head>
<body>
    <!-- Top Header -->
    <div class="top-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <span>Welcome to eMarket! Free shipping on orders over 1 Million</span>
                <div class="d-flex align-items-center gap-3">
                    <div class="dropdown d-inline-block">
                        <span class="dropdown-toggle" style="cursor:pointer">English</span>
                    </div>
                    <span style="color:#555">|</span>
                    <div class="dropdown d-inline-block">
                        <span class="dropdown-toggle" style="cursor:pointer">USD</span>
                    </div>
                    <span style="color:#555">|</span>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="views/user/profile.php">My Account</a>
                        <span style="color:#555">|</span>
                        <a href="controllers/auth-controller.php?action=logout">Logout</a>
                    <?php else: ?>
                        <a href="views/auth/login.php">Login</a>
                        <span style="color:#555">|</span>
                        <a href="views/auth/register.php">Register</a>
                    <?php endif; ?>
                    <span style="color:#555">|</span>
                    <a href="views/user/track-order.php">Track Order</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="main-header">
        <div class="container">
            <a href="index.php" class="logo">eMarket</a>
            
            <form action="views/product/search.php" method="GET" class="search-box">
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="q" class="form-control" placeholder="Search for products...">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
            </form>
            
            <div class="header-actions">
                <div class="header-action">
                    <a href="views/user/wishlist.php">
                        <i class="far fa-heart"></i>
                        <span class="count"><?php echo isset($_SESSION['user_id']) ? count(getWishlistItems()) : 0; ?></span>
                    </a>
                </div>
                <div class="header-action">
                    <a href="views/cart/cart.php">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="count cart-count"><?php echo getCartCount(); ?></span>
                    </a>
                </div>
                <a href="views/cart/cart.php" class="my-cart-btn">My Cart</a>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="main-nav">
        <div class="container d-flex align-items-center">
            <div class="departments-wrapper position-relative">
                <button id="deptToggle" class="category-menu">
                    <i class="fas fa-bars"></i>
                    <span>All Departments</span>
                </button>
                <div class="departments-menu" id="categoryDropdown">
                    <ul class="list-unstyled">
                        <li><a href="#"><i class="fas fa-tools"></i> Industrial Parts & Tools</a></li>
                        <li><a href="#"><i class="fas fa-heartbeat"></i> Health & Beauty</a></li>
                        <li><a href="#"><i class="fas fa-football-ball"></i> Gifts, Sports & Toys</a></li>
                        <li><a href="#"><i class="fas fa-tshirt"></i> Textiles & Accessories</a></li>
                        <li><a href="#"><i class="fas fa-box"></i> Packaging & Office</a></li>
                        <li><a href="#"><i class="fas fa-laptop"></i> Electronics</a></li>
                        <li><a href="#"><i class="fas fa-home"></i> Home, Lights & Construction</a></li>
                        <li><a href="#"><i class="fas fa-gem"></i> Jewellery & Shoes</a></li>
                        <li><a href="#"><i class="fas fa-bars"></i> More Categories</a></li>
                    </ul>
                </div>
            </div>
            <ul class="nav-menu mb-0">
                <li><a href="index.php" class="active">Home</a></li>
                <li><a href="views/product/featured.php">Features</a></li>
                <li><a href="views/product/best-selling.php">Best Selling</a></li>
                <li><a href="views/product/deals.php">Deal</a></li>
                <li><a href="views/product/shop.php">Shop</a></li>
                <li><a href="views/blog/index.php">Blog</a></li>
                <li><a href="views/pages/about.php">Pages</a></li>
            </ul>
        </div>
    </nav>

    <!-- Left Banner & Hero Slider -->
    <div class="container mt-3">
        <div class="row">
            <div class="col-lg-3">
                <div class="left-banner">
                    <img src="assets/images/Best Stylish Running Shoes for Men _ Lightweight & Comfortable Sneakers _.jpg" class="img-fluid" alt="Banner">
                </div>
            </div>
            <div class="col-lg-9">
                <!-- Hero Slider -->
                <?php $main_banners = getBanners('main'); ?>
                <?php if(!empty($main_banners)): ?>
                <div id="heroSlider" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
                    <?php if(count($main_banners) > 1): ?>
                    <div class="carousel-indicators">
                        <?php foreach($main_banners as $index => $banner): ?>
                        <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="<?php echo $index; ?>" class="<?php echo $index == 0 ? 'active' : ''; ?>"></button>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <div class="carousel-inner rounded">
                        <?php foreach($main_banners as $index => $banner): ?>
                        <div class="carousel-item <?php echo $index == 0 ? 'active' : ''; ?>" style="background: linear-gradient(135deg, rgba(40, 167, 70, 0.1) 0%, rgba(25,135,84,0.4) 100%), url('uploads/banners/<?php echo $banner['image']; ?>') center/cover no-repeat; min-height:500px;">
                            <div class="container h-100">
                                <div class="row h-100 align-items-center justify-content-center">
                                    <div class="col-md-10 text-center" style="padding-top: 60px;">
                                        <h2 class="display-3 fw-bold text-uppercase d-inline-block px-4 py-2" style="background: #fff; color: #c79b32;"><?php echo $banner['subtitle'] ?? ''; ?></h2>
                                        <h1 class="display-4 fw-bold mb-4 px-4 py-2" style="background: #fff; color: #c79b32; display: inline-block;"><?php echo $banner['title']; ?></h1>
                                        <p class="lead mb-5 fs-4" style="color: #fff;"><?php echo $banner['description'] ?? ''; ?></p>
                                        <?php if($banner['link']): ?>
                                        <a href="<?php echo $banner['link']; ?>" class="btn btn-lg px-5 py-3" style="background: #c79b32; color: #fff;"><?php echo $banner['link_text'] ?? 'Shop Now'; ?></a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if(count($main_banners) > 1): ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroSlider" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon bg-dark rounded-circle p-3"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroSlider" data-bs-slide="next">
                        <span class="carousel-control-next-icon bg-dark rounded-circle p-3"></span>
                    </button>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <div class="hero-banner" style="background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%); min-height:500px;">
                    <div class="container h-100">
                        <div class="row h-100 align-items-center justify-content-center">
                            <div class="col-md-10 text-center" style="padding-top: 60px;">
                                <h2 class="display-3 fw-bold text-uppercase d-inline-block px-4 py-2" style="background: #fff; color: #c79b32;">Welcome</h2>
                                <h1 class="display-4 fw-bold mb-4 px-4 py-2" style="background: #fff; color: #c79b32; display: inline-block;">eMarket</h1>
                                <p class="lead mb-5 fs-4" style="color: #fff;">Your One-Stop Shop for Quality Products</p>
                                <a href="views/product/shop.php" class="btn btn-lg px-5 py-3" style="background: #c79b32; color: #fff;">Shop Now</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Gift Banner -->
    <?php $bottom_banners = getBanners('bottom'); ?>
    <?php if(!empty($bottom_banners)): ?>
        <?php foreach($bottom_banners as $banner): ?>
        <section class="gift-banner mt-4" style="background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%), url('uploads/banners/<?php echo $banner['image']; ?>') center/cover;">
            <div class="container d-flex justify-content-between align-items-center">
                <div class="text-white">
                    <h2><?php echo $banner['title']; ?></h2>
                    <p><?php echo $banner['subtitle']; ?></p>
                </div>
                <?php if($banner['link']): ?>
                <a href="<?php echo $banner['link']; ?>" class="btn btn-light"><?php echo $banner['link_text'] ?? 'Shop Now'; ?></a>
                <?php endif; ?>
            </div>
        </section>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Daily Deals -->
    <section class="daily-deals">
        <div class="container">
            <div class="section-title">
                <h2>Daily Deals</h2>
            </div>
            <div class="row">
                <?php foreach($daily_deals as $product): ?>
                    <div class="col-md-3">
                        <div class="product-card">
                            <?php if($product['old_price']): ?>
                                <span class="discount-badge">-<?php echo getDiscountPercent($product['price'], $product['old_price']); ?>%</span>
                            <?php endif; ?>
                            <div class="product-image">
                                <img src="<?php echo $product['image'] ? 'uploads/products/' . $product['image'] : 'assets/images/no-image.png'; ?>" alt="<?php echo $product['name']; ?>">
                                <div class="product-actions">
                                    <button onclick="addToCart(<?php echo $product['id']; ?>)" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                                    <button onclick="addToWishlist(<?php echo $product['id']; ?>)" title="Add to Wishlist"><i class="far fa-heart"></i></button>
                                    <button onclick="quickView(<?php echo $product['id']; ?>)" title="Quick View"><i class="fas fa-eye"></i></button>
                                </div>
                            </div>
                            <h3><a href="views/product/product-details.php?id=<?php echo $product['id']; ?>"><?php echo $product['name']; ?></a></h3>
                            <div class="price">
                                <?php echo formatPrice($product['price']); ?>
                                <?php if($product['old_price']): ?>
                                    <span class="old-price"><?php echo formatPrice($product['old_price']); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="stock">Stock: <?php echo $product['stock_quantity']; ?></div>
                            <div class="countdown" data-countdown="2026-03-01">
                                <span>05d</span>
                                <span>12h</span>
                                <span>30m</span>
                                <span>45s</span>
                            </div>
                            <div class="hurry-up">Hurry Up!</div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Trending Products -->
    <section class="trending-products">
        <div class="container">
            <div class="section-title">
                <h2>Trending Products</h2>
            </div>
            <div class="product-tabs">
                <button class="active" onclick="filterProducts('all')">All</button>
                <button onclick="filterProducts('furniture')">Furniture</button>
                <button onclick="filterProducts('electronics')">Electronics</button>
                <button onclick="filterProducts('fashion')">Fashion</button>
                <button onclick="filterProducts('beauty')">Beauty</button>
            </div>
            <div class="row">
                <?php foreach($all_products as $product): ?>
                    <div class="col-md-3 mb-4" data-category="<?php echo strtolower($product['category_name'] ?? 'all'); ?>">
                        <div class="product-card">
                            <?php if($product['old_price']): ?>
                                <span class="discount-badge">-<?php echo getDiscountPercent($product['price'], $product['old_price']); ?>%</span>
                            <?php endif; ?>
                            <div class="product-image">
                                <img src="<?php echo $product['image'] ? 'uploads/products/' . $product['image'] : 'assets/images/no-image.png'; ?>" alt="<?php echo $product['name']; ?>">
                                <div class="product-actions">
                                    <button onclick="addToCart(<?php echo $product['id']; ?>)" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                                    <button onclick="addToWishlist(<?php echo $product['id']; ?>)" title="Add to Wishlist"><i class="far fa-heart"></i></button>
                                    <button onclick="quickView(<?php echo $product['id']; ?>)" title="Quick View"><i class="fas fa-eye"></i></button>
                                </div>
                            </div>
                            <h3><a href="views/product/product-details.php?id=<?php echo $product['id']; ?>"><?php echo $product['name']; ?></a></h3>
                            <div class="price">
                                <?php echo formatPrice($product['price']); ?>
                                <?php if($product['old_price']): ?>
                                    <span class="old-price"><?php echo formatPrice($product['old_price']); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Full Banner -->
    <section class="full-banner" style="background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);">
        <div class="container">
            <h2>Summer Sale</h2>
            <p>Get up to 40% off on selected items</p>
            <a href="views/product/shop.php" class="btn btn-light">Shop Now</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <h3>About eMarket</h3>
                    <p style="color: #fff;">We are a leading e-commerce platform offering quality products at competitive prices.</p>
                </div>
                <div class="col-md-3">
                    <h3>Customer Service</h3>
                    <ul class="footer-links">
                        <li><a href="#">Shipping & Returns</a></li>
                        <li><a href="#">Order Tracking</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h3>Categories</h3>
                    <ul class="footer-links">
                        <li><a href="#">Electronics</a></li>
                        <li><a href="#">Fashion</a></li>
                        <li><a href="#">Home & Garden</a></li>
                        <li><a href="#">Health & Beauty</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h3>Newsletter</h3>
                    <p style="color: #fff;">Subscribe to get special offers</p>
                    <form class="d-flex gap-2">
                        <input type="email" class="form-control" placeholder="Your email">
                        <button type="submit" class="btn btn-primary">Go</button>
                    </form>
                </div>
            </div>
            <div class="footer-bottom mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                <p style="color: #fff;" class="mb-0">&copy; 2026 eMarket. All rights reserved.</p>
                <div class="payment-methods d-flex align-items-center gap-3">
                    <span style="color: #fff;" class="me-2">We Accept:</span>
                    <i class="fab fa-cc-visa fa-2x" style="color: #1A1F71;" title="Visa"></i>
                    <i class="fab fa-cc-mastercard fa-2x" style="color: #EB001B;" title="Mastercard"></i>
                    <i class="fab fa-cc-paypal fa-2x" style="color: #003087;" title="PayPal"></i>
                    <i class="fab fa-cc-amex fa-2x" style="color: #006FCF;" title="American Express"></i>
                    <i class="fab fa-cc-discover fa-2x" style="color: #FF6000;" title="Discover"></i>
                </div>
            </div>
        </div>
    </footer>

    <!-- Quick View Modal -->
    <div id="quickViewModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal('quickViewModal')">&times;</span>
            <div id="quickViewContent"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script>
        // Departments dropdown toggle
        document.getElementById("deptToggle").addEventListener("click", function(e) {
            e.stopPropagation();
            document.querySelector(".departments-menu").classList.toggle("active");
        });
        
        // Close dropdown when clicking outside
        document.addEventListener("click", function(e) {
            if (!e.target.closest(".departments-wrapper")) {
                document.querySelector(".departments-menu").classList.remove("active");
            }
        });
    </script>
</body>
</html>
