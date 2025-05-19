<?php require_once('header.php'); ?>

<!-- Fetching banner for login -->
<?php
$statement = $pdo->prepare("SELECT banner_login FROM tbl_settings WHERE id=1");
$statement->execute();
$banner_login = $statement->fetch(PDO::FETCH_ASSOC)['banner_login'];
?>

<!-- Login Form Processing -->
<?php
$error_message = '';
$success_message = '';

if (isset($_POST['form1'])) {
    // CAPTCHA Verification
    if (empty($_POST['g-recaptcha-response'])) {
        $error_message .= 'Please complete the CAPTCHA.<br>';
    } else {
        $secret = '6Lc6WpQqAAAAAN4rU_IEDoXWjseE_2_f92TLOZ8k'; // Replace with your secret key
        $responseKey = $_POST['g-recaptcha-response'];
        $userIP = $_SERVER['REMOTE_ADDR'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'secret' => $secret,
            'response' => $responseKey,
            'remoteip' => $userIP
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $apiResponse = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($apiResponse);
        if (!$response || !$response->success) {
            $error_message .= 'CAPTCHA verification failed. Please try again.<br>';
        }
    }

    // Proceed with login validation if CAPTCHA is successful
    if (empty($error_message)) {
        $cust_email = trim($_POST['cust_email'] ?? '');
        $cust_password = $_POST['cust_password'] ?? '';

        if (empty($cust_email) || empty($cust_password)) {
            $error_message .= 'Email and Password are required.<br>';
        } else {
            $statement = $pdo->prepare("SELECT * FROM tbl_customer WHERE cust_email=?");
            $statement->execute([$cust_email]);
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                // Verify password
                if (password_verify($cust_password, $result['cust_password'])) {
                    if ($result['cust_status'] == 1) {
                        // Set up session variables
                        $_SESSION['customer'] = true; // indicate as logged in
                        $_SESSION['customer'] = [
                            'cust_id' => $result['cust_id'],
                            'cust_name' => $result['cust_name'],
                            'cust_email' => $result['cust_email'],
                            'cust_phone' => $result['cust_phone'],
                            'cust_address' => $result['cust_address'],
                            'cust_city' => $result['cust_city'],
                        ];

                        // Redirect to the dashboard or homepage
                        header("Location: index.php");
                        exit;
                    } else {
                        $error_message .= 'Your account is inactive. Please contact support.<br>';
                    }
                } else {
                    $error_message .= 'Incorrect password.<br>';
                }
            } else {
                $error_message .= 'No account found with that email.<br>';
            }
        }
    }
}
?>

<!-- Page Banner -->
<div class="page-banner"
    style="background-color:#444;background-image: url(assets/uploads/<?php echo htmlspecialchars($banner_login); ?>);">
    <div class="inner">
        <h1>Login</h1>
    </div>
</div>

<!-- Login Form -->
<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="user-content">
                    <form action="" method="post">
                        <?php $csrf->echoInputField(); ?>
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <!-- Display error or success messages -->
                                <?php if ($error_message): ?>
                                <div class="alert alert-danger">
                                    <?php echo $error_message; ?>
                                </div>
                                <?php endif; ?>

                                <?php if ($success_message): ?>
                                <div class="alert alert-success">
                                    <?php echo $success_message; ?>
                                </div>
                                <?php endif; ?>

                                <!-- Login Fields -->
                                <div class="form-group">
                                    <label>Email Address *</label>
                                    <input type="email" class="form-control" name="cust_email"
                                        value="<?php echo htmlspecialchars($_POST['cust_email'] ?? ''); ?>">
                                </div>

                                <div class="form-group">
                                    <label>Password *</label>
                                    <input type="password" class="form-control" name="cust_password">
                                </div>

                                <!-- CAPTCHA -->
                                <div class="form-group">
                                    <div class="g-recaptcha" data-sitekey="6Lc6WpQqAAAAANFIeIhUeszmfoJRn96F6JFoAZ3F">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <input type="submit" class="btn btn-danger" value="Login" name="form1">
                                </div>

                                <div class="form-group">
                                    <p><a href="forgetPassword.php">Forgot Password?</a></p>
                                    <p>Don't have an account? <a href="registration.php">Register here</a>.</p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>