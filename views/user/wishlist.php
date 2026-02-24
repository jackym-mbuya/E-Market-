<?php
require_once '../../config/functions.php';

if(!isset($_SESSION['user_id'])) {
    redirect('../auth/login.php?redirect=../user/wishlist.php');
}

$wishlist_items = getWishlistItems();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist - <?php echo $settings['site_name'] ?? 'eMarket'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include '../../views/layouts/header.php'; ?>
    
    <div class="container py-4">
        <h2 class="mb-4">My Wishlist</h2>
        
        <?php if(empty($wishlist_items)): ?>
            <div class="empty-state">
                <i class="fas fa-heart"></i>
                <h3>Your wishlist is empty</h3>
                <p>Save your favorite products for later</p>
                <a href="../../index.php" class="btn btn-primary">Continue Shopping</a>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach($wishlist_items as $item): ?>
                    <div class="col-md-3 mb-4">
                        <div class="product-card">
                            <div class="product-image">
                                <img src="<?php echo $item['image'] ? '../../uploads/products/' . $item['image'] : '../../assets/images/no-image.png'; ?>" alt="<?php echo $item['name']; ?>">
                                <div class="product-actions">
                                    <button onclick="addToCart(<?php echo $item['product_id']; ?>)"><i class="fas fa-shopping-cart"></i></button>
                                    <button onclick="removeFromWishlist(<?php echo $item['product_id']; ?>)"><i class="fas fa-heart"></i></button>
                                </div>
                            </div>
                            <h3><a href="../product/product-details.php?id=<?php echo $item['product_id']; ?>"><?php echo $item['name']; ?></a></h3>
                            <div class="price">
                                <?php echo formatPrice($item['price']); ?>
                                <?php if($item['old_price']): ?>
                                    <span class="old-price"><?php echo formatPrice($item['old_price']); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include '../../views/layouts/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/script.js"></script>
</body>
</html>
