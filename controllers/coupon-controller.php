<?php
require_once '../config/functions.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch($action) {
    case 'validate':
        validateCouponAjax();
        break;
    default:
        echo json_encode(['valid' => false, 'message' => 'Invalid action']);
}

function validateCouponAjax() {
    $code = $_POST['code'] ?? '';
    $subtotal = $_POST['subtotal'] ?? 0;
    
    if(!$code) {
        echo json_encode(['valid' => false, 'message' => 'Please enter a coupon code']);
        return;
    }
    
    $result = validateCoupon($code, $subtotal);
    echo json_encode($result);
}
