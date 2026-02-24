<?php
$page_title = 'Best Selling';
require_once '../layouts/header.php';

$best_selling = $db->select("
    SELECT p.*, c.name as category_name, 
    COALESCE(SUM(oi.quantity), 0) as sold_count,
    (SELECT image FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN order_items oi ON p.id = oi.product_id
    LEFT JOIN orders o ON oi.order_id = o.id AND o.order_status != 'cancelled'
    WHERE p.status = 1
    GROUP BY p.id
    ORDER BY sold_count DESC
    LIMIT 20
");
?>
<!-- Main Content -->
<main class="main-content">
    <div class="container">
        <nav aria-label="breadcrumb" class="breadcrumb-nav mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item active">Best Selling</li>
            </ol>
        </nav>
        
        <div class="section-title mb-4">
            <h2>Best Selling Products</h2>
        </div>
        
        <?php if(empty($best_selling)): ?>
            <div class="alert alert-info">No products available.</div>
        <?php else: ?>
        <div class="row">
            <?php foreach($best_selling as $product): ?>
                <div class="col-md-3 mb-4">
                    <div class="product-card">
                        <?php if($product['old_price']): ?>
                            <span class="discount-badge">-<?php echo getDiscountPercent($product['price'], $product['old_price']); ?>%</span>
                        <?php endif; ?>
                        <div class="product-image">
                            <img src="<?php echo $product['image'] ? '../../uploads/products/' . $product['image'] : '../../assets/images/no-image.png'; ?>" alt="<?php echo $product['name']; ?>">
                            <?php if($product['sold_count'] > 0): ?>
                                <span class="discount-badge bg-success">Best Seller</span>
                            <?php endif; ?>
                            <div class="product-actions">
                                <button onclick="addToCart(<?php echo $product['id']; ?>)" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                                <button onclick="addToWishlist(<?php echo $product['id']; ?>)" title="Add to Wishlist"><i class="far fa-heart"></i></button>
                                <button onclick="quickView(<?php echo $product['id']; ?>)" title="Quick View"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                        <h3><a href="../../views/product/product-details.php?id=<?php echo $product['id']; ?>"><?php echo $product['name']; ?></a></h3>
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
        <?php endif; ?>
    </div>
</main>

<?php require_once '../layouts/footer.php'; ?>
