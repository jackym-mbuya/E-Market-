<?php
require_once '../../config/functions.php';

$product_id = $_GET['id'] ?? 0;
$product = getProduct($product_id);
$product_images = getProductImages($product_id);

if(!$product) {
    redirect('shop.php');
}

$related_products = getProducts(4, $product['category_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['name']; ?> - <?php echo $settings['site_name'] ?? 'eMarket'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include '../../views/layouts/header.php'; ?>
    
    <div class="container py-4">
        <nav class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../../index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="shop.php">Shop</a></li>
                <li class="breadcrumb-item active"><?php echo $product['name']; ?></li>
            </ol>
        </nav>
        
        <div class="row">
            <div class="col-lg-6">
                <div class="product-main-image mb-3">
                    <img src="<?php echo $product['image'] ? '../../uploads/products/' . $product['image'] : '../../assets/images/no-image.png'; ?>" alt="<?php echo $product['name']; ?>" class="img-fluid">
                </div>
            </div>
            <div class="col-lg-6">
                <h2><?php echo $product['name']; ?></h2>
                <p class="text-muted">Category: <?php echo $product['category_name']; ?></p>
                
                <div class="price mb-3">
                    <span class="fs-3 fw-bold text-primary"><?php echo formatPrice($product['price']); ?></span>
                    <?php if($product['old_price']): ?>
                        <span class="fs-5 text-muted text-decoration-line-through ms-2"><?php echo formatPrice($product['old_price']); ?></span>
                        <span class="badge bg-danger ms-2">-<?php echo getDiscountPercent($product['price'], $product['old_price']); ?>%</span>
                    <?php endif; ?>
                </div>
                
                <p class="mb-3"><?php echo $product['short_description']; ?></p>
                
                <div class="mb-3">
                    <span class="badge <?php echo $product['stock_quantity'] > 0 ? 'bg-success' : 'bg-danger'; ?>">
                        <?php echo $product['stock_quantity'] > 0 ? 'In Stock (' . $product['stock_quantity'] . ')' : 'Out of Stock'; ?>
                    </span>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Quantity:</label>
                    <input type="number" id="quantity" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>" class="form-control" style="width: 100px;">
                </div>
                
                <div class="d-flex gap-2 mb-4">
                    <button class="btn btn-primary" onclick="addToCart(<?php echo $product['id']; ?>, document.getElementById('quantity').value)">
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                    </button>
                    <button class="btn btn-outline" onclick="addToWishlist(<?php echo $product['id']; ?>)">
                        <i class="far fa-heart"></i> Add to Wishlist
                    </button>
                </div>
                
                <div class="mb-3">
                    <p class="mb-1"><strong>SKU:</strong> <?php echo $product['sku'] ?? 'N/A'; ?></p>
                    <p class="mb-1"><strong>Brand:</strong> <?php echo $product['brand'] ?? 'N/A'; ?></p>
                </div>
            </div>
        </div>
        
        <!-- Product Description -->
        <div class="row mt-5">
            <div class="col-12">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#description">Description</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#reviews">Reviews</button>
                    </li>
                </ul>
                <div class="tab-content p-3 border">
                    <div class="tab-pane fade show active" id="description">
                        <?php echo $product['description'] ?? 'No description available.'; ?>
                    </div>
                    <div class="tab-pane fade" id="reviews">
                        <p>No reviews yet. Be the first to review this product.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Related Products -->
        <?php if(!empty($related_products)): ?>
        <div class="mt-5">
            <h3 class="mb-4">Related Products</h3>
            <div class="row">
                <?php foreach($related_products as $rel_product): ?>
                    <?php if($rel_product['id'] != $product['id']): ?>
                    <div class="col-md-3 mb-4">
                        <div class="product-card">
                            <div class="product-image">
                                <img src="<?php echo $rel_product['image'] ? '../../uploads/products/' . $rel_product['image'] : '../../assets/images/no-image.png'; ?>" alt="<?php echo $rel_product['name']; ?>">
                                <div class="product-actions">
                                    <button onclick="addToCart(<?php echo $rel_product['id']; ?>)"><i class="fas fa-shopping-cart"></i></button>
                                    <button onclick="addToWishlist(<?php echo $rel_product['id']; ?>)"><i class="far fa-heart"></i></button>
                                </div>
                            </div>
                            <h3><a href="product-details.php?id=<?php echo $rel_product['id']; ?>"><?php echo $rel_product['name']; ?></a></h3>
                            <div class="price"><?php echo formatPrice($rel_product['price']); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <?php include '../../views/layouts/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/script.js"></script>
</body>
</html>
