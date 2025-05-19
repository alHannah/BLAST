<?php 
ob_start();
session_start();
require_once('header.php'); 
require_once('admin/inc/config.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php
// Fetch banner reset password setting
$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
$statement->execute();
$result = $statement->fetch(PDO::FETCH_ASSOC);
$banner_reset_password = $result['banner_reset_password'];
?>

<?php
$error_message = '';
$error_message2 = '';

// Check if required parameters are present
if (!isset($_GET['email']) || !isset($_GET['token'])) {
    header('location: login.php');
    exit;
}

// Validate email and token
$email = $_GET['email'];
$token = $_GET['token'];

$statement = $pdo->prepare("SELECT * FROM tbl_customer WHERE cust_email = ? AND cust_token = ?");
$statement->execute([$email, $token]);
$result = $statement->fetch(PDO::FETCH_ASSOC);

if (!$result) {
    // Redirect if token or email is invalid
    header('location: login.php');
    exit;
}

$saved_time = $result['cust_timestamp'];

// Check if the token has expired (24 hours)
if (time() - $saved_time > 86400) {
    $error_message2 = "The password reset email time (24 hours) has expired. Please try resetting your password again.";
}

// Handle form submission
if (isset($_POST['form1'])) {
    $valid = true;

    if (empty($_POST['cust_new_password']) || empty($_POST['cust_re_password'])) {
        $valid = false;
        $error_message .= "Please enter new and retype passwords.\\n";
    } elseif ($_POST['cust_new_password'] != $_POST['cust_re_password']) {
        $valid = false;
        $error_message .= "Passwords do not match.\\n";
    }

    if ($valid) {
        $cust_new_password = strip_tags($_POST['cust_new_password']);
        $hashedPassword = password_hash($cust_new_password, PASSWORD_DEFAULT);

        // Update password and reset token
        $statement = $pdo->prepare("UPDATE tbl_customer SET cust_password = ?, cust_token = NULL, cust_timestamp = NULL WHERE cust_email = ?");
        $statement->execute([$hashedPassword, $email]);

        header('location: reset-password-success.php');
        exit;
    }
}
?>

<div class="page-banner"
    style="background-color:#444;background-image: url(assets/uploads/<?php echo $banner_reset_password; ?>);">
    <div class="inner">
        <h1>Change Password</h1>
    </div>
</div>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="user-content">
                    <?php
                    if ($error_message != '') {
                        echo "<script>alert('" . $error_message . "')</script>";
                    }
                    ?>
                    <?php if ($error_message2 != ''): ?>
                    <div class="error"><?php echo $error_message2; ?></div>
                    <?php else: ?>
                    <form action="" method="post">
                        <div class="row">
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">New Password *</label>
                                    <input type="password" class="form-control" name="cust_new_password">
                                </div>
                                <div class="form-group">
                                    <label for="">Retype New Password *</label>
                                    <input type="password" class="form-control" name="cust_re_password">
                                </div>
                                <div class="form-group">
                                    <label for=""></label>
                                    <input type="submit" class="btn btn-primary" value="Change Password" name="form1">
                                </div>
                            </div>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>