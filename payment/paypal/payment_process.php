<?php
ob_start();
session_start();
require_once('../../admin/inc/config.php');
include 'PayPal.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$error_message = '';

// Fetch PayPal email from settings
$statement = $pdo->prepare("SELECT paypal_email FROM tbl_settings WHERE id = 1");
$statement->execute();
$row = $statement->fetch(PDO::FETCH_ASSOC);
$paypal_email = $row['paypal_email'] ?? '';

$return_url = 'payment_confirm.php'; // Change this to the new confirmation page
$cancel_url = '../../checkout.php';

$item_name = 'Product Item(s)';
$item_amount = $_POST['final_total'];
$item_number = time(); // Unique transaction ID
$payment_date = date('Y-m-d H:i:s');

$statement = $pdo->prepare("INSERT INTO tbl_payment (
    customer_id,
    customer_name,
    customer_email,
    payment_date,
    txnid, 
    paid_amount,
    card_number,
    card_cvv,
    card_month,
    card_year,
    bank_transaction_info,
    payment_method,
    payment_status,
    shipping_status,
    payment_id
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$sql = $statement->execute([
    $_SESSION['customer']['cust_id'],
    $_SESSION['customer']['cust_name'],
    $_SESSION['customer']['cust_email'],
    $payment_date,
    '', // txnid will be updated after payment confirmation
    $item_amount,
    '', // card_number
    '', // card_cvv
    '', // card_month
    '', // card_year
    '', // bank_transaction_info
    'PayPal', // payment_method
    'Pending', // payment_status
    'Pending', // shipping_status
    $item_number, // payment_id
]);

// Prepare cart data for tbl_order
$arr_cart_p_id = $_SESSION['cart_p_id'] ?? [];
$arr_cart_p_name = $_SESSION['cart_p_name'] ?? [];
$arr_cart_size_name = $_SESSION['cart_size_name'] ?? [];
$arr_cart_color_name = $_SESSION['cart_color_name'] ?? [];
$arr_cart_p_qty = $_SESSION['cart_p_qty'] ?? [];
$arr_cart_p_current_price = $_SESSION['cart_p_current_price'] ?? [];

// Fetch product inventory data
$statement = $pdo->prepare("SELECT p_id, p_qty FROM tbl_product");
$statement->execute();
$product_data = $statement->fetchAll(PDO::FETCH_ASSOC);

foreach ($arr_cart_p_id as $i => $product_id) {
    $statement = $pdo->prepare("INSERT INTO tbl_order (
        product_id,
        product_name,
        size,
        color,
        quantity,
        unit_price,
        payment_id
    ) VALUES (?, ?, ?, ?, ?, ?, ?)");

    $statement->execute([
        $arr_cart_p_id[$i],
        $arr_cart_p_name[$i],
        $arr_cart_size_name[$i],
        $arr_cart_color_name[$i],
        $arr_cart_p_qty[$i],
        $arr_cart_p_current_price[$i],
        $item_number,
    ]);

    // Update stock quantities
    foreach ($product_data as $product) {
        if ($product['p_id'] == $arr_cart_p_id[$i]) {
            $final_quantity = $product['p_qty'] - $arr_cart_p_qty[$i];
            $statement = $pdo->prepare("UPDATE tbl_product SET p_qty = ? WHERE p_id = ?");
            $statement->execute([$final_quantity, $arr_cart_p_id[$i]]);
            break;
        }
    }
}

// Clear cart session
unset($_SESSION['cart_p_id'], $_SESSION['cart_size_name'], $_SESSION['cart_color_name']);
unset($_SESSION['cart_p_qty'], $_SESSION['cart_p_current_price'], $_SESSION['cart_p_name']);

// Redirect to PayPal for payment approval
$paypal = new PayPal(PAYPAL_CLIENT_ID, PAYPAL_SECRET_ID);
$paymentResponse = $paypal->createPayment($item_amount, $return_url, $cancel_url);

if ($paymentResponse && isset($paymentResponse->links)) {
    foreach ($paymentResponse->links as $link) {
        if ($link->rel === 'approval_url') {
            header('Location: ' . $link->href);
            exit();
        }
    }
}

echo 'Error processing payment.';