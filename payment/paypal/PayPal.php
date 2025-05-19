<?php 

class PayPal {
    private $clientId;
    private $clientSecretId;
    private $apiEndPoint = PAYPAL_API;

    function __construct($clientId, $clientSecretId){
        $this->clientId = $clientId;
        $this->clientSecretId = $clientSecretId;
    }

    function createPayment($amount){
        // Initialize cURL handle
        $ch = curl_init();

        $apiUrl = $this->apiEndPoint .'payments/payment';
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Basic '.base64_encode($this->clientId.':'.$this->clientSecretId)
        );

        $postData = array(
            'intent' => 'sale',
            'payer' => array(
                'payment_method' => 'paypal'
            ),
            'transactions' => array(
                array(
                    'amount' => array(
                        'total' => $amount,
                        'currency' => 'USD'
                    ),
                    'description' => 'Jomaddar IT Paypal Payment',
                )
            ),
            'redirect_urls' => array(
                'return_url' => APP_URL . '/my_prj/payment/paypal/payment_success', // goes to payment_success
                'cancel_url' => APP_URL . '/my_prj/checkout' // back to checkout
            )   
        );

        // cURL options
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute cURL request and capture response
        $response = curl_exec($ch);

        // Check for cURL errors
        if(curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }

        // Close cURL handle
        curl_close($ch);

        // Decode response
        $res = json_decode($response);
        
        // Log the response (optional)
        file_put_contents('paypal_response.log', print_r($res, true), FILE_APPEND);

        return $res;
    }

    function executePayment($paymentId, $PayerID){
        // Initialize cURL handle
        $ch = curl_init();

        $apiUrl = $this->apiEndPoint .'payments/payment/'.$paymentId.'/execute';
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Basic '.base64_encode($this->clientId.':'.$this->clientSecretId)
        );
        
        $postData = array(
            'payer_id' => $PayerID
        );

        // cURL options
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute cURL request and capture response
        $response = curl_exec($ch);

        // Check for cURL errors
        if(curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }

        // Close cURL handle
        curl_close($ch);

        // Decode response
        $res = json_decode($response);

        return $res;
    }
}