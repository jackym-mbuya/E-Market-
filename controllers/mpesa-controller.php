<?php
session_start();
require_once '../config/functions.php';
require_once 'mpesa.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch($action) {
    case 'initiate':
        initiateSTKPush();
        break;
    case 'check':
        checkPaymentStatus();
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}

function initiateSTKPush() {
    global $mpesa;
    
    $phone = $_POST['phone'] ?? '';
    $amount = $_POST['amount'] ?? 0;
    $order_number = $_POST['order_number'] ?? '';
    
    if (!$phone || !$amount || !$order_number) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
        return;
    }
    
    $phone = str_replace('+', '', $phone);
    if (strlen($phone) === 10 && substr($phone, 0, 1) === '0') {
        $phone = '254' . substr($phone, 1);
    }
    
    $result = $mpesa->STKPush($phone, $amount, $order_number);
    
    if (isset($result['ResponseCode']) && $result['ResponseCode'] === '0') {
        $_SESSION['mpesa_checkout_id'] = $result['CheckoutRequestID'];
        $_SESSION['mpesa_order'] = $order_number;
        
        echo json_encode([
            'status' => 'success', 
            'message' => 'Payment request sent to your phone',
            'checkout_id' => $result['CheckoutRequestID']
        ]);
    } else {
        echo json_encode([
            'status' => 'error', 
            'message' => $result['errorMessage'] ?? 'Failed to initiate payment'
        ]);
    }
}

function checkPaymentStatus() {
    global $mpesa;
    
    $checkout_request_id = $_POST['checkout_id'] ?? '';
    
    if (!$checkout_request_id) {
        echo json_encode(['status' => 'error', 'message' => 'Missing checkout ID']);
        return;
    }
    
    $result = $mpesa->checkTransactionStatus($checkout_request_id);
    
    if (isset($result['ResultCode'])) {
        if ($result['ResultCode'] === 0) {
            echo json_encode(['status' => 'success', 'message' => 'Payment successful']);
        } else {
            echo json_encode(['status' => 'pending', 'message' => 'Payment pending or failed']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to check status']);
    }
}
