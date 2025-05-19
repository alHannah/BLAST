<?php
require_once "../../admin/inc/config.php";

// Get the payment data from PayPal's API response (for example, after a successful payment creation)
$paymentStatus = $_POST['payment_status'];  // Assuming this is passed from PayPal API response
$txnId = $_POST['txn_id'];  // PayPal transaction ID
$itemNumber = $_POST['item_number'];  // Item number or order ID from PayPal

// If payment is successful, update the payment status in the database
if ($paymentStatus == 'Completed') {
    // Update payment details in your database
    $statement = $pdo->prepare("UPDATE tbl_payment SET 
                                txnid=?, 
                                payment_status=? 
                                WHERE payment_id=?");
    $sql = $statement->execute(array(
                                $txnId,
                                $paymentStatus,
                                $itemNumber
                            ));
} else {
    // If the payment is not successful, delete the payment record from the database
    $statement = $pdo->prepare("DELETE FROM tbl_payment WHERE payment_id=?");
    $sql = $statement->execute(array($itemNumber));
}

?>