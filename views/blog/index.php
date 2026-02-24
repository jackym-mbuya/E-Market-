<?php
$page_title = 'Blog';
require_once '../layouts/header.php';
?>
<!-- Main Content -->
<main class="main-content">
    <div class="container">
        <nav aria-label="breadcrumb" class="breadcrumb-nav">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item active">Blog</li>
            </ol>
        </nav>
        
        <div class="section-header">
            <h2>Our Blog</h2>
            <p>Latest news and updates from eMarket</p>
        </div>
        
        <div class="blog-grid">
            <div class="blog-card">
                <div class="blog-image">
                    <img src="../../assets/images/blog-1.jpg" alt="Blog Post">
                </div>
                <div class="blog-content">
                    <span class="blog-date">February 23, 2026</span>
                    <h3 class="blog-title">Welcome to eMarket</h3>
                    <p class="blog-excerpt">Discover the best products at amazing prices. Shop with us for a seamless experience.</p>
                    <a href="#" class="read-more">Read More <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            
            <div class="blog-card">
                <div class="blog-image">
                    <img src="../../assets/images/blog-2.jpg" alt="Blog Post">
                </div>
                <div class="blog-content">
                    <span class="blog-date">February 20, 2026</span>
                    <h3 class="blog-title">New Arrivals</h3>
                    <p class="blog-excerpt">Check out our latest collection of products. New items added daily!</p>
                    <a href="#" class="read-more">Read More <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            
            <div class="blog-card">
                <div class="blog-image">
                    <img src="../../assets/images/blog-3.jpg" alt="Blog Post">
                </div>
                <div class="blog-content">
                    <span class="blog-date">February 15, 2026</span>
                    <h3 class="blog-title">Special Offers</h3>
                    <p class="blog-excerpt">Don't miss our special offers and discounts. Save big on your purchases!</p>
                    <a href="#" class="read-more">Read More <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../layouts/footer.php'; ?>
