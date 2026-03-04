<?php
require_once 'config.php';

class Mpesa {
    private $consumer_key = 'oQgZPIF9WvssNI5NvPvNoKnzVRRe8Sn2hBZMDl2GGpqSbRBG';
    private $consumer_secret = '7nMJqGkeDtcJZ8L7uYqPmcGlmubhMm5DkzZ89ylzEeOXEMMZcSYEPxR6yZ4EUxl4';
    private $shortcode = 'N/A';
    private $passkey = 'YOUR_PASSKEY';
    private $callback_url = 'https://yourdomain.com/controllers/mpesa-callback.php';
    private $environment = 'sandbox'; // sandbox or live
    
    private $base_url = 'https://sandbox.safaricom.co.ke';
    
    public function __construct() {
        if ($this->environment === 'live') {
            $this->base_url = 'https://api.safaricom.co.ke';
        }
    }
    
    public function setCredentials($consumer_key, $consumer_secret, $shortcode, $passkey, $callback_url) {
        $this->consumer_key = $consumer_key;
        $this->consumer_secret = $consumer_secret;
        $this->shortcode = $shortcode;
        $this->passkey = $passkey;
        $this->callback_url = $callback_url;
    }
    
    private function getAccessToken() {
        $url = $this->base_url . '/oauth/v1/generate?grant_type=client_credentials';
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERPWD, $this->consumer_key . ':' . $this->consumer_secret);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
        $result = curl_exec($curl);
        curl_close($curl);
        
        $response = json_decode($result, true);
        return $response['access_token'] ?? null;
    }
    
    public function STKPush($phone, $amount, $order_number) {
        $access_token = $this->getAccessToken();
        
        if (!$access_token) {
            return ['status' => 'error', 'message' => 'Failed to get access token'];
        }
        
        $timestamp = date('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);
        
        $url = $this->base_url . '/mpesa/stkpush/v1/processrequest';
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $access_token,
            'Content-Type: application/json'
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
        $data = [
            'BusinessShortCode' => $this->shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerBuyGoodsOnline',
            'Amount' => round($amount),
            'PartyA' => $phone,
            'PartyB' => $this->shortcode,
            'PhoneNumber' => $phone,
            'CallBackURL' => $this->callback_url,
            'AccountReference' => $order_number,
            'TransactionDesc' => 'Payment for order ' . $order_number
        ];
        
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        
        $result = curl_exec($curl);
        curl_close($curl);
        
        return json_decode($result, true);
    }
    
    public function checkTransactionStatus($checkout_request_id) {
        $access_token = $this->getAccessToken();
        
        if (!$access_token) {
            return ['status' => 'error', 'message' => 'Failed to get access token'];
        }
        
        $url = $this->base_url . '/mpesa/stkpushquery/v1/query';
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $access_token,
            'Content-Type: application/json'
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
        $data = [
            'BusinessShortCode' => $this->shortcode,
            'CheckoutRequestID' => $checkout_request_id,
            'Password' => base64_encode($this->shortcode . $this->passkey . date('YmdHis')),
            'Timestamp' => date('YmdHis')
        ];
        
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        
        $result = curl_exec($curl);
        curl_close($curl);
        
        return json_decode($result, true);
    }
}

$mpesa = new Mpesa();
