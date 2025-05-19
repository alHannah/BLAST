<?php require_once('header.php'); ?>

<?php
// Retrieve banner image for the cart page
$statement = $pdo->prepare("SELECT banner_cart FROM tbl_settings WHERE id = 1");
$statement->execute();
$row = $statement->fetch(PDO::FETCH_ASSOC);
$banner_cart = $row['banner_cart'];
?>

<?php
$error_message = '';
if (isset($_POST['form1'])) {
    $productData = $pdo->query("SELECT p_id, p_qty FROM tbl_product")->fetchAll(PDO::FETCH_ASSOC);

    $table_product_id = array_column($productData, 'p_id');
    $table_quantity = array_column($productData, 'p_qty');

    $submitted_product_ids = $_POST['product_id'];
    $submitted_quantities = $_POST['quantity'];
    $submitted_names = $_POST['product_name'];

    $allow_update = true;

    for ($i = 0; $i < count($submitted_product_ids); $i++) {
        $product_id = $submitted_product_ids[$i];
        $quantity_requested = $submitted_quantities[$i];
        $product_name = $submitted_names[$i];

        $index = array_search($product_id, $table_product_id);
        if ($index !== false) {
            if ($table_quantity[$index] < $quantity_requested) {
                $allow_update = false;
                $error_message .= "Only {$table_quantity[$index]} items are available for '{$product_name}'.\n";
            } else {
                $_SESSION['cart_p_qty'][$i + 1] = $quantity_requested;
            }
        }
    }

    if (!$allow_update) {
        $error_message .= "\nSome items were not updated due to insufficient stock.";
    } else {
        $error_message = "All items were successfully updated!";
    }

    echo "<script>alert('" . addslashes($error_message) . "');</script>";
}
?>

<div class="page-banner" style="background-image: url(assets/uploads/<?php echo $banner_cart; ?>)">
    <div class="overlay"></div>
    <div class="page-banner-inner">
        <h1>Shopping Cart</h1>
    </div>
</div>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?php if (!isset($_SESSION['cart_p_id']) || empty($_SESSION['cart_p_id'])): ?>
                <h2 class="text-center">Your Cart is Empty!</h2>
                <h4 class="text-center">Add products to your cart to view them here.</h4>
                <?php else: ?>
                <form action="" method="post">
                    <?php $csrf->echoInputField(); ?>
                    <div class="cart">
                        <table class="table table-responsive table-hover table-bordered">
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Product Name</th>
                                <th>Size</th>
                                <th>Color</th>
                                <th>Unit Price</th>
                                <th>Quantity</th>
                                <th class="text-right">Total Price</th>
                                <th class="text-center">Actions</th>
                            </tr>
                            <?php
                                $table_total_price = 0;

                                foreach ($_SESSION['cart_p_id'] as $key => $value) {
                                    $index = $key + 1;
                                    $product_id = $_SESSION['cart_p_id'][$key];
                                    $product_name = $_SESSION['cart_p_name'][$key];
                                    $product_qty = $_SESSION['cart_p_qty'][$key];
                                    $product_price = $_SESSION['cart_p_current_price'][$key];
                                    $product_photo = $_SESSION['cart_p_featured_photo'][$key];

                                    $row_total_price = $product_price * $product_qty;
                                    $table_total_price += $row_total_price;
                                    ?>
                            <tr>
                                <td><?php echo $index; ?></td>
                                <td><img src="assets/uploads/<?php echo $product_photo; ?>" alt="Product"></td>
                                <td><?php echo $product_name; ?></td>
                                <td><?php echo $_SESSION['cart_size_name'][$key]; ?></td>
                                <td><?php echo $_SESSION['cart_color_name'][$key]; ?></td>
                                <td><?php echo $product_price; ?></td>
                                <td>
                                    <input type="hidden" name="product_id[]" value="<?php echo $product_id; ?>">
                                    <input type="hidden" name="product_name[]" value="<?php echo $product_name; ?>">
                                    <input type="number" name="quantity[]" value="<?php echo $product_qty; ?>" min="1"
                                        class="form-control">
                                </td>
                                <td class="text-right"><?php echo $row_total_price; ?></td>
                                <td class="text-center">
                                    <a href="cart-item-delete.php?id=<?php echo $product_id; ?>"
                                        onclick="return confirm('Are you sure you want to remove this item?');"
                                        class="btn btn-danger">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php } ?>
                            <tr>
                                <th colspan="7" class="text-right">Total</th>
                                <th class="text-right"><?php echo $table_total_price; ?></th>
                                <th></th>
                            </tr>
                        </table>
                    </div>
                    <div class="cart-buttons">
                        <button type="submit" class="btn btn-primary" name="form1">Update Cart</button>
                        <a href="index.php" class="btn btn-secondary">Continue Shopping</a>
                        <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>