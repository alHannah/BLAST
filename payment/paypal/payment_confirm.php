<?php
session_start();
require_once('../../admin/inc/config.php');
include 'PayPal.php';

// Check if PayPal has redirected the user with payment info
if (isset($_GET['paymentId']) && isset($_GET['PayerID'])) {
    $paymentId = $_GET['paymentId'];
    $payerId = $_GET['PayerID'];

    // Initialize PayPal API
    $paypal = new PayPal(PAYPAL_CLIENT_ID, PAYPAL_SECRET_ID);

    // Execute the payment using PayPal's API
    $paymentResponse = $paypal->executePayment($paymentId, $payerId);

    if ($paymentResponse && $paymentResponse->state == 'approved') {
        // Update the payment status in your database
        $statement = $pdo->prepare("UPDATE tbl_payment SET payment_status = ? WHERE payment_id = ?");
        $statement->execute(['Completed', $paymentId]);

        // Redirect to the success page
        header('Location: payment_success.php');
        exit();
    } else {
        // Payment failed, update the status in your database
        $statement = $pdo->prepare("UPDATE tbl_payment SET payment_status = ? WHERE payment_id = ?");
        $statement->execute(['Failed', $paymentId]);

        // Redirect to the cancel page
        header('Location: ../../checkout.php');
        exit();
    }
} else {
    // If the payment ID or Payer ID is not set, redirect to the cancel page
    header('Location: ../../checkout.php');
    exit();
}