<?php require_once('header.php'); ?>

<?php
// Fetch site settings
$statement = $pdo->prepare("SELECT banner_forget_password FROM tbl_settings WHERE id=1");
$statement->execute();
$settings = $statement->fetch(PDO::FETCH_ASSOC);
$banner_forget_password = $settings['banner_forget_password'] ?? '';
?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form1'])) {
    $valid = true;
    $errors = [];
    $email = trim($_POST['cust_email']);

    // Validate email
    if (empty($email)) {
        $valid = false;
        $errors[] = "Please enter your email address.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $valid = false;
        $errors[] = "Please enter a valid email address.";
    } else {
        // Check if the email exists in the database
        $statement = $pdo->prepare("SELECT cust_id FROM tbl_customer WHERE cust_email=?");
        $statement->execute([$email]);
        if ($statement->rowCount() === 0) {
            $valid = false;
            $errors[] = "The email address you entered is not registered.";
        }
    }

    if ($valid) {
        // Generate a secure token
        $token = bin2hex(random_bytes(16));
        $timestamp = time();

        // Update token and timestamp in the database
        $statement = $pdo->prepare("UPDATE tbl_customer SET cust_token=?, cust_timestamp=? WHERE cust_email=?");
        $statement->execute([$token, $timestamp, $email]);

        // Prepare email content
        $resetLink = BASE_URL . "reset-password.php?email=" . urlencode($email) . "&token=" . urlencode($token);
        $message = "<p>Click the link below to reset your password:</p><p><a href='$resetLink'>$resetLink</a></p>";

        // Send email
        $subject = "Password Reset Request";
        $headers = [
            "From: noreply@" . parse_url(BASE_URL, PHP_URL_HOST),
            "Reply-To: noreply@" . parse_url(BASE_URL, PHP_URL_HOST),
            "MIME-Version: 1.0",
            "Content-Type: text/html; charset=UTF-8"
        ];

        if (mail($email, $subject, $message, implode("\r\n", $headers))) {
            $success_message = "A password reset link has been sent to your email.";
        } else {
            $errors[] = "Failed to send the reset email. Please try again later.";
        }
    }
}
?>

<div class="page-banner"
    style="background-color:#444;background-image: url(assets/uploads/<?php echo htmlspecialchars($banner_forget_password); ?>);">
    <div class="inner">
        <h1>Forgot Password</h1>
    </div>
</div>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="user-content">
                    <?php
                    if (!empty($errors)) {
                        echo '<div class="alert alert-danger">' . implode('<br>', $errors) . '</div>';
                    }
                    if (!empty($success_message)) {
                        echo '<div class="alert alert-success">' . htmlspecialchars($success_message) . '</div>';
                    }
                    ?>
                    <form action="resetPassword.php" method="post">
                        <?php $csrf->echoInputField(); ?>
                        <div class="form-group">
                            <label for="cust_email">Email Address *</label>
                            <input type="email" class="form-control" name="cust_email"
                                value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary" value="Send Reset Link" name="form1">
                        </div>
                    </form>
                    <a href="login.php" class="text-danger">Back to Login</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>