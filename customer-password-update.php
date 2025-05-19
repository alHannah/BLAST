<?php require_once('header.php'); ?>

<?php
// Check if the customer is logged in
if (!isset($_SESSION['customer'])) {
    header('location: ' . BASE_URL . 'logout.php');
    exit;
} else {
    // If the customer is logged in but inactive, force logout
    $statement = $pdo->prepare("SELECT * FROM tbl_customer WHERE cust_id = ? AND cust_status = ?");
    $statement->execute([$_SESSION['customer']['cust_id'], 0]);
    if ($statement->rowCount() > 0) {
        header('location: ' . BASE_URL . 'logout.php');
        exit;
    }
}
?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valid = true;
    $error_message = '';

    // Input sanitization and validation
    $old_password = $_POST['cust_old_password'] ?? '';
    $new_password = $_POST['cust_password'] ?? '';
    $confirm_password = $_POST['cust_re_password'] ?? '';

    if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
        $valid = false;
        $error_message = "All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $valid = false;
        $error_message = "New password and confirm password do not match.";
    } elseif (strlen($new_password) < 8) {
        $valid = false;
        $error_message = "Password must be at least 8 characters long.";
    }

    if ($valid) {
        // Verify old password
        $statement = $pdo->prepare("SELECT cust_password FROM tbl_customer WHERE cust_id = ?");
        $statement->execute([$_SESSION['customer']['cust_id']]);
        $customer = $statement->fetch(PDO::FETCH_ASSOC);

        if ($customer && password_verify($old_password, $customer['cust_password'])) {
            // Update new password
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $update_statement = $pdo->prepare("UPDATE tbl_customer SET cust_password = ? WHERE cust_id = ?");
            $update_statement->execute([$hashed_password, $_SESSION['customer']['cust_id']]);

            $_SESSION['customer']['cust_password'] = $hashed_password;
            $success_message = "Password updated successfully.";
        } else {
            $valid = false;
            $error_message = "The old password is incorrect.";
        }
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
                    <h3 class="text-center">
                        <?php echo "Your profile has been updated successfully."; ?>
                    </h3>
                    <form action="" method="post">
                        <?php $csrf->echoInputField(); ?>
                        <div class="row">
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <?php
                                if (!empty($error_message)) {
                                    echo "<div class='alert alert-danger' role='alert'>$error_message</div>";
                                }
                                if (!empty($success_message)) {
                                    echo "<div class='alert alert-success' role='alert'>$success_message</div>";
                                }
                                ?>
                                <div class="form-group">
                                    <label for="cust_old_password">Old Password *</label>
                                    <input type="password" class="form-control" name="cust_old_password" required>
                                </div>
                                <div class="form-group">
                                    <label for="cust_password">New Password *</label>
                                    <input type="password" class="form-control" name="cust_password" required>
                                </div>
                                <div class="form-group">
                                    <label for="cust_re_password">Confirm New Password *</label>
                                    <input type="password" class="form-control" name="cust_re_password" required>
                                </div>
                                <input type="submit" class="btn btn-primary" value="Update Password">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>