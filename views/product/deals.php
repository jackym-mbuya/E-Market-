<?php
$page_title = 'Daily Deals';
require_once '../layouts/header.php';

$deals = $db->select("
    SELECT p.*, c.name as category_name,
    (SELECT image FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.status = 1 AND p.old_price IS NOT NULL AND p.old_price > p.price
    ORDER BY ((p.old_price - p.price) / p.old_price) DESC
    LIMIT 20
");
?>
<!-- Main Content -->
<main class="main-content">
    <div class="container">
        <nav aria-label="breadcrumb" class="breadcrumb-nav mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item active">Daily Deals</li>
            </ol>
        </nav>
        
        <div class="section-title mb-4">
            <h2>Daily Deals</h2>
        </div>
        
        <?php if(empty($deals)): ?>
            <div class="alert alert-info">No deals available at the moment.</div>
        <?php else: ?>
        <div class="row">
            <?php foreach($deals as $product): 
                $discount = round((($product['old_price'] - $product['price']) / $product['old_price']) * 100);
            ?>
                <div class="col-md-3 mb-4">
                    <div class="product-card">
                        <span class="discount-badge">-<?php echo $discount; ?>%</span>
                        <div class="product-image">
                            <img src="<?php echo $product['image'] ? '../../uploads/products/' . $product['image'] : '../../assets/images/no-image.png'; ?>" alt="<?php echo $product['name']; ?>">
                            <div class="product-actions">
                                <button onclick="addToCart(<?php echo $product['id']; ?>)" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                                <button onclick="addToWishlist(<?php echo $product['id']; ?>)" title="Add to Wishlist"><i class="far fa-heart"></i></button>
                                <button onclick="quickView(<?php echo $product['id']; ?>)" title="Quick View"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                        <h3><a href="../../views/product/product-details.php?id=<?php echo $product['id']; ?>"><?php echo $product['name']; ?></a></h3>
                        <div class="price">
                            <?php echo formatPrice($product['price']); ?>
                            <span class="old-price"><?php echo formatPrice($product['old_price']); ?></span>
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
