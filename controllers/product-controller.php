<?php
require_once '../config/functions.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch($action) {
    case 'quick-view':
        quickViewAjax();
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}

function quickViewAjax() {
    $product_id = $_GET['id'] ?? 0;
    
    if(!$product_id) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid product']);
        return;
    }
    
    $product = getProduct($product_id);
    $images = getProductImages($product_id);
    $inWishlist = isInWishlist($product_id);
    
    if(!$product) {
        echo json_encode(['status' => 'error', 'message' => 'Product not found']);
        return;
    }
    
    $html = '
    <div class="quick-view-content">
        <div class="product-images">
            <div class="main-image">
                <img src="' . ($product['image'] ? UPLOAD_URL . 'products/' . $product['image'] : 'assets/images/no-image.png') . '" alt="' . $product['name'] . '">
            </div>
        </div>
        <div class="product-details">
            <h2>' . $product['name'] . '</h2>
            <p class="category">' . $product['category_name'] . '</p>
            <div class="price">
                ' . formatPrice($product['price']) . '
                ' . ($product['old_price'] ? '<span class="old-price">' . formatPrice($product['old_price']) . '</span>' : '') . '
            </div>
            <p class="description">' . $product['short_description'] . '</p>
            <div class="stock">
                ' . ($product['stock_quantity'] > 0 ? '<span class="in-stock">In Stock (' . $product['stock_quantity'] . ')</span>' : '<span class="out-of-stock">Out of Stock</span>') . '
            </div>
            <div class="actions">
                <div class="quantity">
                    <input type="number" value="1" min="1" max="' . $product['stock_quantity'] . '" id="quickViewQuantity">
                </div>
                <button class="btn btn-primary" onclick="addToCart(' . $product['id'] . ', document.getElementById(\'quickViewQuantity\').value)">Add to Cart</button>
                <button class="btn btn-outline" onclick="addToWishlist(' . $product['id'] . ')">
                    <i class="' . ($inWishlist ? 'fas' : 'far') . ' fa-heart"></i>
                </button>
            </div>
        </div>
    </div>';
    
    echo json_encode(['status' => 'success', 'html' => $html]);
}
