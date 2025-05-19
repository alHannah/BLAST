<?php 
require_once('header.php');

// Ensure the customer is logged in
if (!isset($_SESSION['customer'])) {
    header('Location: ' . BASE_URL . 'logout.php');
    exit;
}

// Force logout if the customer is inactive
$statement = $pdo->prepare("SELECT * FROM tbl_customer WHERE cust_id = ? AND cust_status = ?");
$statement->execute([$_SESSION['customer']['cust_id'], 0]);

if ($statement->rowCount() > 0) {
    header('Location: ' . BASE_URL . 'logout.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form1'])) {
    $errors = [];

    // Validate input fields
    if (empty(trim($_POST['cust_name']))) {
        $errors[] = "Name is required.";
    }

    if (empty(trim($_POST['cust_phone']))) {
        $errors[] = "Phone number is required.";
    }

    if (empty(trim($_POST['cust_address']))) {
        $errors[] = "Address is required.";
    }

    if (empty(trim($_POST['cust_region']))) {
        $errors[] = "Region is required.";
    }

    // Proceed if no validation errors
    if (empty($errors)) {
        $statement = $pdo->prepare("
            UPDATE tbl_customer 
            SET cust_name = ?, cust_phone = ?, cust_address = ?, cust_region = ? 
            WHERE cust_id = ?
        ");
        $statement->execute([
            strip_tags($_POST['cust_name']),
            strip_tags($_POST['cust_phone']),
            strip_tags($_POST['cust_address']),
            strip_tags($_POST['cust_region']),
            $_SESSION['customer']['cust_id']
        ]);

        // Update session data
        $_SESSION['customer']['cust_name'] = $_POST['cust_name'];
        $_SESSION['customer']['cust_phone'] = $_POST['cust_phone'];
        $_SESSION['customer']['cust_address'] = $_POST['cust_address'];
        $_SESSION['customer']['cust_region'] = $_POST['cust_region'];

        // Redirect with a success message
        $_SESSION['success_message'] = "Your profile has been updated successfully.";
        header("Location: customer-profile-update.php");
        exit;
    }
}
?>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?php require_once('customer-sidebar.php'); ?>
            </div>
            <div class="col-md-12">
                <div class="user-content">
                    <h3>Update Your Profile</h3>

                    <?php if (!empty($errors)) : ?>
                    <div class="alert alert-danger" role="alert">
                        <ul>
                            <?php foreach ($errors as $error) : ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($_SESSION['success_message'])) : ?>
                    <div class="alert alert-success" role="alert">
                        <?php 
                                echo htmlspecialchars($_SESSION['success_message']); 
                                unset($_SESSION['success_message']); 
                            ?>
                    </div>
                    <?php endif; ?>

                    <form action="" method="post">
                        <?php $csrf->echoInputField(); ?>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="cust_name">Name *</label>
                                <input type="text" id="cust_name" class="form-control" name="cust_name"
                                    value="<?php echo htmlspecialchars($_SESSION['customer']['cust_name']); ?>">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="cust_email">Email *</label>
                                <input type="text" id="cust_email" class="form-control"
                                    value="<?php echo htmlspecialchars($_SESSION['customer']['cust_email']); ?>"
                                    disabled>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="cust_phone">Phone Number *</label>
                                <input type="text" id="cust_phone" class="form-control" name="cust_phone"
                                    value="<?php echo htmlspecialchars($_SESSION['customer']['cust_phone']); ?>">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="">Region *</label>
                                <select name="cust_region" class="form-control">
                                    <?php
                                $statement = $pdo->prepare("SELECT * FROM tbl_region ORDER BY region_name ASC");
                                $statement->execute();
                                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($result as $row) {
                                    ?>
                                    <option value="<?php echo $row['region_id']; ?>"
                                        <?php if($row['region_id'] == $_SESSION['customer']['cust_region']) {echo 'selected';} ?>>
                                        <?php echo $row['region_name']; ?></option>
                                    <?php
                                }
                                ?>
                                </select>
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="cust_address">Address *</label>
                                <textarea id="cust_address" name="cust_address" class="form-control"
                                    rows="4"><?php echo htmlspecialchars($_SESSION['customer']['cust_address']); ?></textarea>
                            </div>

                        </div>
                        <button type="submit" class="btn btn-primary" name="form1">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>