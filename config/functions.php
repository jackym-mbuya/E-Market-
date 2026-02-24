<?php
require_once 'config.php';
require_once 'database.php';

function getSettings() {
    global $db;
    $settings = $db->select("SELECT setting_key, setting_value FROM settings");
    $result = [];
    foreach($settings as $s) {
        $result[$s['setting_key']] = $s['setting_value'];
    }
    return $result;
}

$settings = getSettings();

function getCategories($parent_id = 0) {
    global $db;
    return $db->select("SELECT * FROM categories WHERE parent_id = ? AND status = 1 ORDER BY name", [$parent_id]);
}

function getAllCategories() {
    global $db;
    return $db->select("SELECT * FROM categories WHERE status = 1 ORDER BY name");
}

function getSubcategories($category_id) {
    global $db;
    return $db->select("SELECT * FROM subcategories WHERE category_id = ? AND status = 1 ORDER BY name", [$category_id]);
}

function getProducts($limit = null, $category_id = null, $featured = false) {
    global $db;
    
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
    
    if($featured) {
        $query .= " AND p.featured = 1";
    }
    
    $query .= " ORDER BY p.created_at DESC";
    
    if($limit) {
        $query .= " LIMIT " . intval($limit);
    }
    
    return $db->select($query, $params);
}

function getProduct($id) {
    global $db;
    return $db->selectOne("SELECT p.*, c.name as category_name,
        (SELECT image FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.id = ?", [$id]);
}

function getProductImages($product_id) {
    global $db;
    return $db->select("SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order", [$product_id]);
}

function getBanners($position = null) {
    global $db;
    
    $query = "SELECT * FROM banners WHERE status = 1";
    $params = [];
    
    if($position) {
        $query .= " AND position = ?";
        $params[] = $position;
    }
    
    $query .= " ORDER BY sort_order";
    
    return $db->select($query, $params);
}

function getCartCount() {
    global $db;
    $session_id = session_id();
    
    if(isset($_SESSION['user_id'])) {
        $count = $db->selectOne("SELECT SUM(ci.quantity) as total FROM cart_items ci 
            JOIN carts c ON ci.cart_id = c.id 
            WHERE c.user_id = ?", [$_SESSION['user_id']]);
    } else {
        $count = $db->selectOne("SELECT SUM(ci.quantity) as total FROM cart_items ci 
            JOIN carts c ON ci.cart_id = c.id 
            WHERE c.session_id = ?", [$session_id]);
    }
    
    return $count['total'] ?? 0;
}

function getCartItems() {
    global $db;
    $session_id = session_id();
    
    if(isset($_SESSION['user_id'])) {
        $items = $db->select("SELECT ci.*, p.name, p.slug,
            (SELECT image FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image
            FROM cart_items ci
            JOIN carts c ON ci.cart_id = c.id
            JOIN products p ON ci.product_id = p.id
            WHERE c.user_id = ?
            ORDER BY ci.created_at DESC", [$_SESSION['user_id']]);
    } else {
        $items = $db->select("SELECT ci.*, p.name, p.slug,
            (SELECT image FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image
            FROM cart_items ci
            JOIN carts c ON ci.cart_id = c.id
            JOIN products p ON ci.product_id = p.id
            WHERE c.session_id = ?
            ORDER BY ci.created_at DESC", [$session_id]);
    }
    
    return $items;
}

function getCartTotal() {
    global $db;
    $session_id = session_id();
    
    if(isset($_SESSION['user_id'])) {
        $total = $db->selectOne("SELECT SUM(ci.quantity * ci.price) as total 
            FROM cart_items ci
            JOIN carts c ON ci.cart_id = c.id
            WHERE c.user_id = ?", [$_SESSION['user_id']]);
    } else {
        $total = $db->selectOne("SELECT SUM(ci.quantity * ci.price) as total 
            FROM cart_items ci
            JOIN carts c ON ci.cart_id = c.id
            WHERE c.session_id = ?", [$session_id]);
    }
    
    return $total['total'] ?? 0;
}

function addToCart($product_id, $quantity = 1) {
    global $db;
    $session_id = session_id();
    $user_id = $_SESSION['user_id'] ?? null;
    
    $product = getProduct($product_id);
    if(!$product) return false;
    
    if($user_id) {
        $cart = $db->selectOne("SELECT id FROM carts WHERE user_id = ?", [$user_id]);
        if(!$cart) {
            $db->insert("carts", ['user_id' => $user_id, 'session_id' => $session_id]);
            $cart = $db->selectOne("SELECT id FROM carts WHERE user_id = ?", [$user_id]);
        }
    } else {
        $cart = $db->selectOne("SELECT id FROM carts WHERE session_id = ?", [$session_id]);
        if(!$cart) {
            $db->insert("carts", ['session_id' => $session_id]);
            $cart = $db->selectOne("SELECT id FROM carts WHERE session_id = ?", [$session_id]);
        }
    }
    
    $cart_id = $cart['id'];
    
    $existing = $db->selectOne("SELECT id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ?", 
        [$cart_id, $product_id]);
    
    if($existing) {
        $db->update("cart_items", 
            ['quantity' => $existing['quantity'] + $quantity], 
            "id = :id", 
            ['id' => $existing['id']]);
    } else {
        $db->insert("cart_items", [
            'cart_id' => $cart_id,
            'product_id' => $product_id,
            'quantity' => $quantity,
            'price' => $product['price']
        ]);
    }
    
    return true;
}

function removeFromCart($cart_item_id) {
    global $db;
    return $db->delete("DELETE FROM cart_items WHERE id = ?", [$cart_item_id]);
}

function updateCartQuantity($cart_item_id, $quantity) {
    global $db;
    if($quantity > 0) {
        return $db->update("cart_items", ['quantity' => $quantity], "id = :id", ['id' => $cart_item_id]);
    } else {
        return removeFromCart($cart_item_id);
    }
}

function addToWishlist($product_id) {
    global $db;
    
    if(!isset($_SESSION['user_id'])) {
        return ['status' => 'error', 'message' => 'Please login first'];
    }
    
    $exists = $db->selectOne("SELECT id FROM wishlists WHERE user_id = ? AND product_id = ?", 
        [$_SESSION['user_id'], $product_id]);
    
    if($exists) {
        return ['status' => 'error', 'message' => 'Already in wishlist'];
    }
    
    $db->insert("wishlists", [
        'user_id' => $_SESSION['user_id'],
        'product_id' => $product_id
    ]);
    
    return ['status' => 'success', 'message' => 'Added to wishlist'];
}

function getWishlistItems() {
    global $db;
    
    if(!isset($_SESSION['user_id'])) return [];
    
    return $db->select("SELECT w.*, p.name, p.slug, p.price, p.old_price,
        (SELECT image FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image
        FROM wishlists w
        JOIN products p ON w.product_id = p.id
        WHERE w.user_id = ?
        ORDER BY w.created_at DESC", [$_SESSION['user_id']]);
}

function removeFromWishlist($product_id) {
    global $db;
    return $db->delete("DELETE FROM wishlists WHERE user_id = ? AND product_id = ?", 
        [$_SESSION['user_id'], $product_id]);
}

function isInWishlist($product_id) {
    global $db;
    
    if(!isset($_SESSION['user_id'])) return false;
    
    $exists = $db->selectOne("SELECT id FROM wishlists WHERE user_id = ? AND product_id = ?", 
        [$_SESSION['user_id'], $product_id]);
    
    return $exists ? true : false;
}

function generateOrderNumber() {
    return 'ORD-' . time() . '-' . rand(1000, 9999);
}

function validateCoupon($code, $subtotal) {
    global $db;
    
    $coupon = $db->selectOne("SELECT * FROM coupons WHERE code = ? AND status = 1", [$code]);
    
    if(!$coupon) {
        return ['valid' => false, 'message' => 'Invalid coupon code'];
    }
    
    if($coupon['valid_until'] && strtotime($coupon['valid_until']) < time()) {
        return ['valid' => false, 'message' => 'Coupon has expired'];
    }
    
    if($coupon['max_uses'] && $coupon['used_count'] >= $coupon['max_uses']) {
        return ['valid' => false, 'message' => 'Coupon usage limit reached'];
    }
    
    if($subtotal < $coupon['min_order_amount']) {
        return ['valid' => false, 'message' => 'Minimum order amount not met'];
    }
    
    if($coupon['discount_type'] === 'percentage') {
        $discount = ($subtotal * $coupon['discount_value']) / 100;
    } else {
        $discount = $coupon['discount_value'];
    }
    
    return ['valid' => true, 'discount' => $discount, 'coupon' => $coupon];
}

function getOrders($user_id) {
    global $db;
    return $db->select("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC", [$user_id]);
}

function getOrder($order_id, $user_id = null) {
    global $db;
    
    if($user_id) {
        return $db->selectOne("SELECT * FROM orders WHERE id = ? AND user_id = ?", [$order_id, $user_id]);
    }
    
    return $db->selectOne("SELECT * FROM orders WHERE id = ?", [$order_id]);
}

function getOrderItems($order_id) {
    global $db;
    return $db->select("SELECT * FROM order_items WHERE order_id = ?", [$order_id]);
}

function calculateShipping($subtotal) {
    global $settings;
    
    $free_shipping = isset($settings['free_shipping_amount']) ? $settings['free_shipping_amount'] : 100;
    $shipping_cost = isset($settings['shipping_cost']) ? $settings['shipping_cost'] : 0;
    
    if($subtotal >= $free_shipping) {
        return 0;
    }
    
    return $shipping_cost;
}

function calculateTax($subtotal) {
    global $settings;
    
    $tax_rate = isset($settings['tax_rate']) ? $settings['tax_rate'] : 0;
    
    return ($subtotal * $tax_rate) / 100;
}

function getRatingStars($rating) {
    $stars = '';
    for($i = 1; $i <= 5; $i++) {
        if($i <= $rating) {
            $stars .= '<i class="fas fa-star"></i>';
        } elseif($i - 0.5 <= $rating) {
            $stars .= '<i class="fas fa-star-half-alt"></i>';
        } else {
            $stars .= '<i class="far fa-star"></i>';
        }
    }
    return $stars;
}

function getDiscountPercent($price, $old_price) {
    if(!$old_price || $old_price <= $price) return 0;
    return round((($old_price - $price) / $old_price) * 100);
}
