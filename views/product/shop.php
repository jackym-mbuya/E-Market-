<?php
require_once '../../config/functions.php';

$category_id = $_GET['category'] ?? null;
$search = $_GET['q'] ?? '';
$sort = $_GET['sort'] ?? 'latest';

$query = "SELECT p.*, c.name as category_name, 
          (SELECT image FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE p.status = 1";

$params = [];

if($category_id) {
    $query .= " AND p.category_id = ?";
    $params[] = $category_id;
}

if($search) {
    $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

switch($sort) {
    case 'price_low':
        $query .= " ORDER BY p.price ASC";
        break;
    case 'price_high':
        $query .= " ORDER BY p.price DESC";
        break;
    case 'name':
        $query .= " ORDER BY p.name ASC";
        break;
    default:
        $query .= " ORDER BY p.created_at DESC";
}

global $db;
$products = $db->select($query, $params);
$categories = getAllCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - <?php echo $settings['site_name'] ?? 'eMarket'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include '../../views/layouts/header.php'; ?>
    
    <div class="container py-4">
        <div class="row">
            <div class="col-lg-3">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Categories</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2"><a href="shop.php" class="<?php echo !$category_id ? 'text-primary' : ''; ?>">All Products</a></li>
                            <?php foreach($categories as $cat): ?>
                                <li class="mb-2"><a href="shop.php?category=<?php echo $cat['id']; ?>" class="<?php echo $category_id == $cat['id'] ? 'text-primary' : ''; ?>"><?php echo $cat['name']; ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <p class="mb-0">Showing <?php echo count($products); ?> products</p>
                    <select class="form-select w-auto" onchange="window.location.href='?sort='+this.value+'<?php echo $category_id ? '&category='.$category_id : ''; ?><?php echo $search ? '&q='.$search : ''; ?>'">
                        <option value="latest" <?php echo $sort == 'latest' ? 'selected' : ''; ?>>Sort by: Latest</option>
                        <option value="price_low" <?php echo $sort == 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price_high" <?php echo $sort == 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                        <option value="name" <?php echo $sort == 'name' ? 'selected' : ''; ?>>Name: A-Z</option>
                    </select>
                </div>
                
                <?php if($search): ?>
                    <p class="mb-3">Search results for: <strong><?php echo htmlspecialchars($search); ?></strong></p>
                <?php endif; ?>
                
                <div class="row">
                    <?php foreach($products as $product): ?>
                        <div class="col-md-4 mb-4">
                            <div class="product-card h-100">
                                <?php if($product['old_price']): ?>
                                    <span class="discount-badge">-<?php echo getDiscountPercent($product['price'], $product['old_price']); ?>%</span>
                                <?php endif; ?>
                                <div class="product-image">
                                    <img src="<?php echo $product['image'] ? '../../uploads/products/' . $product['image'] : '../../assets/images/no-image.png'; ?>" alt="<?php echo $product['name']; ?>">
                                    <div class="product-actions">
                                        <button onclick="addToCart(<?php echo $product['id']; ?>)" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                                        <button onclick="addToWishlist(<?php echo $product['id']; ?>)" title="Add to Wishlist"><i class="far fa-heart"></i></button>
                                        <button onclick="quickView(<?php echo $product['id']; ?>)" title="Quick View"><i class="fas fa-eye"></i></button>
                                    </div>
                                </div>
                                <h3><a href="product-details.php?id=<?php echo $product['id']; ?>"><?php echo $product['name']; ?></a></h3>
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
                    
                    <?php if(empty($products)): ?>
                        <div class="col-12">
                            <div class="empty-state">
                                <i class="fas fa-search"></i>
                                <h3>No products found</h3>
                                <p>Try different keywords or browse categories</p>
                                <a href="shop.php" class="btn btn-primary">View All Products</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick View Modal -->
    <div id="quickViewModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal('quickViewModal')">&times;</span>
            <div id="quickViewContent"></div>
        </div>
    </div>
    
    <?php include '../../views/layouts/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/script.js"></script>
</body>
</html>
