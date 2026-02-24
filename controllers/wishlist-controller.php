<?php
require_once '../config/functions.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch($action) {
    case 'add':
        addToWishlistAjax();
        break;
    case 'remove':
        removeFromWishlistAjax();
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}

function addToWishlistAjax() {
    if(!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Please login first']);
        return;
    }
    
    $product_id = $_POST['product_id'] ?? 0;
    
    if(!$product_id) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid product']);
        return;
    }
    
    $result = addToWishlist($product_id);
    echo json_encode($result);
}

function removeFromWishlistAjax() {
    if(!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Please login first']);
        return;
    }
    
    $product_id = $_POST['product_id'] ?? 0;
    
    if(!$product_id) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid product']);
        return;
    }
    
    $result = removeFromWishlist($product_id);
    
    if($result) {
        echo json_encode(['status' => 'success', 'message' => 'Removed from wishlist']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to remove']);
    }
}
