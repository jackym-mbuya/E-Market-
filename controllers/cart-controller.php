<?php
require_once '../config/functions.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch($action) {
    case 'add':
        addToCartAjax();
        break;
    case 'remove':
        removeFromCartAjax();
        break;
    case 'update':
        updateCartAjax();
        break;
    case 'count':
        getCartCountAjax();
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}

function addToCartAjax() {
    $product_id = $_POST['product_id'] ?? 0;
    $quantity = $_POST['quantity'] ?? 1;
    
    if(!$product_id) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid product']);
        return;
    }
    
    $result = addToCart($product_id, $quantity);
    $cart_count = getCartCount();
    
    if($result) {
        echo json_encode(['status' => 'success', 'message' => 'Product added to cart', 'cart_count' => $cart_count]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add product. Product may not exist.']);
    }
}

function removeFromCartAjax() {
    $cart_item_id = $_POST['cart_item_id'] ?? 0;
    
    if(!$cart_item_id) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid item']);
        return;
    }
    
    $result = removeFromCart($cart_item_id);
    
    if($result) {
        echo json_encode(['status' => 'success', 'message' => 'Item removed from cart']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to remove item']);
    }
}

function updateCartAjax() {
    $cart_item_id = $_POST['cart_item_id'] ?? 0;
    $quantity = $_POST['quantity'] ?? 1;
    
    if(!$cart_item_id) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid item']);
        return;
    }
    
    $result = updateCartQuantity($cart_item_id, $quantity);
    
    if($result) {
        echo json_encode(['status' => 'success', 'message' => 'Cart updated']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update cart']);
    }
}

function getCartCountAjax() {
    $count = getCartCount();
    echo json_encode(['count' => $count]);
}
