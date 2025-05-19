<?php
session_start();
require_once('header.php');
require_once('admin/inc/config.php');

//error handling
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Fetching settings from the database
$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id = 1");
$statement->execute();
$settings = $statement->fetch(PDO::FETCH_ASSOC);
$banner_checkout = $settings['banner_checkout'] ?? '';

// $_SESSION['cart_p_id'] = $_SESSION['cart_p_id'] ?? [];


// Initialize cart arrays for better readability
$cartDetails = [
    'product_id' => $_SESSION['cart_p_id'] ?? [],
    'size_id' => $_SESSION['cart_size_id'] ?? [],
    'size_name' => $_SESSION['cart_size_name'] ?? [],
    'color_id' => $_SESSION['cart_color_id'] ?? [],
    'color_name' => $_SESSION['cart_color_name'] ?? [],
    'quantity' => $_SESSION['cart_p_qty'] ?? [],
    'price' => $_SESSION['cart_p_current_price'] ?? [],
    'name' => $_SESSION['cart_p_name'] ?? [],
    'photo' => $_SESSION['cart_p_featured_photo'] ?? []
];

// Function to fetch region name
function getRegionName($regionId, $pdo) {
    $statement = $pdo->prepare("SELECT region_name FROM tbl_region WHERE region_id = ?");
    $statement->execute([$regionId]);
    return $statement->fetchColumn();
}

// Calculate total price and shipping cost
$totalPrice = 0;
foreach ($cartDetails['product_id'] as $i => $productId) {
    $totalPrice += $cartDetails['price'][$i] * $cartDetails['quantity'][$i];
}

// Fetch shipping cost based on region
$regionId = $_SESSION['customer']['cust_region'] ?? null;
$shippingCost = 0;
if ($regionId) {
    $statement = $pdo->prepare("SELECT amount FROM tbl_shipping_cost WHERE region_id = ?");
    $statement->execute([$regionId]);
    $shippingCost = $statement->fetchColumn() ?: 0;
} else {
    $statement = $pdo->prepare("SELECT amount FROM tbl_shipping_cost_all WHERE sca_id = 1");
    $statement->execute();
    $shippingCost = $statement->fetchColumn() ?: 0;
}

$finalTotal = $totalPrice + $shippingCost;
?>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <!-- Check if logged in -->
                <?php if (!isset($_SESSION['customer'])): ?>
                <p>
                    <a href="login.php" class="btn btn-md btn-danger">Please login as customer to checkout</a>
                </p>
                <?php else: ?>
                <h3 class="special">Your Cart</h3>
                <div class="cart">
                    <table class="table table-responsive table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>Size</th>
                                <th>Color</th>
                                <th>Unit Price</th>
                                <th>Quantity</th>
                                <th>Total Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartDetails['product_id'] as $i => $productId): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td>
                                    <img src="assets/uploads/<?= $cartDetails['photo'][$i] ?>" alt="">
                                </td>
                                <td><?= $cartDetails['size_name'][$i] ?></td>
                                <td><?= $cartDetails['color_name'][$i] ?></td>
                                <td>₱<?= $cartDetails['price'][$i] ?></td>
                                <td><?= $cartDetails['quantity'][$i] ?></td>
                                <td>₱<?= $cartDetails['price'][$i] * $cartDetails['quantity'][$i] ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr>
                                <th colspan="6" class="total-text">Subtotal</th>
                                <th class="total-amount">₱<?= $totalPrice ?></th>
                            </tr>
                            <tr>
                                <td colspan="6" class="total-text">Shipping Cost</td>
                                <td class="total-amount">₱<?= $shippingCost ?></td>
                            </tr>
                            <tr>
                                <th colspan="6" class="total-text">Grand Total</th>
                                <th class="total-amount">₱<?= $finalTotal ?></th>
                            </tr>
                        </tbody>
                    </table>

                    <div class="billing-address">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="special">Billing Information</h3>
                                <table class="table table-responsive table-bordered">
                                    <tr>
                                        <td>Name</td>
                                        <td><?= $_SESSION['customer']['cust_b_name'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>Phone</td>
                                        <td><?= $_SESSION['customer']['cust_b_phone'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>Region</td>
                                        <td><?= getRegionName($_SESSION['customer']['cust_b_region'], $pdo) ?></td>
                                    </tr>
                                    <tr>
                                        <td>Address</td>
                                        <td><?= nl2br($_SESSION['customer']['cust_b_address']) ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h3 class="special">Shipping Information</h3>
                                <table class="table table-responsive table-bordered">
                                    <tr>
                                        <td>Name</td>
                                        <td><?= $_SESSION['customer']['cust_s_name'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>Phone</td>
                                        <td><?= $_SESSION['customer']['cust_s_phone'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>Region</td>
                                        <td><?= getRegionName($_SESSION['customer']['cust_s_region'], $pdo) ?></td>
                                    </tr>
                                    <tr>
                                        <td>Address</td>
                                        <td><?= nl2br($_SESSION['customer']['cust_s_address']) ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="cart-buttons">
                        <a>
                            <li><button type="button" class="btn btn-primary"
                                    onclick="window.location.href='cart.php';">Back to Cart</button></li>
                        </a>
                    </div>


                    <h3 class="special">Order Summary</h3>
                    <div class="row">
                        <?php if (empty($_SESSION['customer']['cust_b_name']) || empty($_SESSION['customer']['cust_b_phone']) || empty($_SESSION['customer']['cust_b_region']) || empty($_SESSION['customer']['cust_b_address']) || empty($_SESSION['customer']['cust_s_name']) || empty($_SESSION['customer']['cust_s_phone']) || empty($_SESSION['customer']['cust_s_region']) || empty($_SESSION['customer']['cust_s_address'])): 
                                        ?>
                        <div class="col-md-12">
                            <div class="alert alert-danger">
                                You must complete all billing and shipping information in your dashboard before
                                proceeding to checkout. Please update your information <a
                                    href="customer-billing-shipping-update.php">here</a>.
                            </div>
                        </div>
                        <?php else: ?>

                        <form class="paypal" action="payment/paypal/payment_process.php" method="post" id="paypal_form"
                            target="_blank">
                            <input type="hidden" name="final_total" value="<?= $finalTotal ?>">
                            <button type="submit" class="btn btn-primary" style="display: block !important;">Pay Now
                                with PayPal</button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>

                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>