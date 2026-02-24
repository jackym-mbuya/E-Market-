<?php
require_once '../config/database.php';

header('Content-Type: application/json');

$data = file_get_contents('php://input');
$log_file = '../logs/mpesa.log';

if (!file_exists('../logs')) {
    mkdir('../logs', 0777, true);
}

file_put_contents($log_file, date('Y-m-d H:i:s') . " - Callback: " . $data . "\n", FILE_APPEND);

$result = json_decode($data, true);

if (isset($result['Body']['stkCallback'])) {
    $callback = $result['Body']['stkCallback'];
    
    $result_code = $callback['ResultCode'];
    $result_desc = $callback['ResultDesc'];
    $checkout_request_id = $callback['CheckoutRequestID'];
    $merchant_id = $callback['MerchantRequestID'];
    
    $items = $callback['CallbackMetadata']['Item'] ?? [];
    
    $amount = 0;
    $mpesa_receipt_number = '';
    $phone = '';
    
    foreach ($items as $item) {
        if ($item['Name'] === 'Amount') {
            $amount = $item['Value'];
        } elseif ($item['Name'] === 'MpesaReceiptNumber') {
            $mpesa_receipt_number = $item['Value'];
        } elseif ($item['Name'] === 'PhoneNumber') {
            $phone = $item['Value'];
        }
    }
    
    if ($result_code === 0) {
        $db->update('orders', [
            'payment_status' => 'completed',
            'payment_method' => 'mpesa',
            'transaction_id' => $mpesa_receipt_number
        ], "CheckoutRequestID = :id", ['id' => $checkout_request_id]);
        
        file_put_contents($log_file, date('Y-m-d H:i:s') . " - Payment SUCCESS: $mpesa_receipt_number\n", FILE_APPEND);
    } else {
        file_put_contents($log_file, date('Y-m-d H:i:s') . " - Payment FAILED: $result_desc\n", FILE_APPEND);
    }
}

echo json_encode(['status' => 'success']);
