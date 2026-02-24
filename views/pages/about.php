<?php
$page_title = 'About Us';
require_once '../layouts/header.php';
?>
<!-- Main Content -->
<main class="main-content">
    <div class="container">
        <nav aria-label="breadcrumb" class="breadcrumb-nav">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item active">About Us</li>
            </ol>
        </nav>
        
        <div class="about-section">
            <div class="row">
                <div class="col-md-6">
                    <h2>About eMarket</h2>
                    <p>Welcome to eMarket, your one-stop online shopping destination. We offer a wide range of products across various categories including electronics, fashion, home & garden, and more.</p>
                    <p>Our mission is to provide quality products at affordable prices with excellent customer service.</p>
                    <ul class="about-features">
                        <li><i class="fas fa-check-circle"></i> Quality Products</li>
                        <li><i class="fas fa-check-circle"></i> Fast Shipping</li>
                        <li><i class="fas fa-check-circle"></i> Secure Payments</li>
                        <li><i class="fas fa-check-circle"></i> 24/7 Customer Support</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <img src="../../assets/images/about.jpg" alt="About Us" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../layouts/footer.php'; ?>
