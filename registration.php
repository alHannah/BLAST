<?php 
require_once('header.php'); 

// Fetch banner settings for the registration page
$banner_registration = '';
$query = "SELECT banner_registration FROM tbl_settings WHERE id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([1]);
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $banner_registration = $row['banner_registration'];
}

// Initialize error and success message containers
$error_messages = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form1'])) {

    // CAPTCHA Verification
    if (empty($_POST['g-recaptcha-response'])) {
        $error_messages[] = "Please complete the CAPTCHA.";
    } else {
        $secret = '6Lc6WpQqAAAAAN4rU_IEDoXWjseE_2_f92TLOZ8k';
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
            $error_messages[] = "CAPTCHA verification failed. Please try again.";
        }
    }

    // Sanitize and validate inputs if CAPTCHA is valid
    if (empty($error_messages)) {
        $cust_name = trim($_POST['cust_name'] ?? '');
        $cust_email = trim($_POST['cust_email'] ?? '');
        $cust_phone = trim($_POST['cust_phone'] ?? '');
        $cust_address = trim($_POST['cust_address'] ?? '');
        $cust_region = trim($_POST['cust_region'] ?? '');
        $cust_password = $_POST['cust_password'] ?? '';
        $cust_re_password = $_POST['cust_re_password'] ?? '';

        // Input validation
        if (empty($cust_name)) {
            $error_messages[] = "Name is required.";
        }

        if (empty($cust_email)) {
            $error_messages[] = "Email is required.";
        } elseif (!filter_var($cust_email, FILTER_VALIDATE_EMAIL)) {
            $error_messages[] = "Invalid email format.";
        } else {
            $query = "SELECT COUNT(*) FROM tbl_customer WHERE cust_email = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$cust_email]);
            if ($stmt->fetchColumn() > 0) {
                $error_messages[] = "This email is already registered.";
            }
        }

        if (empty($cust_phone)) {
            $error_messages[] = "Phone number is required.";
        }

        if (empty($cust_address)) {
            $error_messages[] = "Address is required.";
        }

        if (empty($cust_region)) {
            $error_messages[] = "Region is required.";
        }

        if (empty($cust_password) || empty($cust_re_password)) {
            $error_messages[] = "Password and confirmation are required.";
        } elseif ($cust_password !== $cust_re_password) {
            $error_messages[] = "Passwords do not match.";
        }

        if (empty($error_messages)) {
            $hashed_password = password_hash($cust_password, PASSWORD_BCRYPT); 
            $token = md5(uniqid(mt_rand(), true)); 
            $cust_datetime = date('Y-m-d H:i:s');
            $cust_timestamp = time();

            try {
                $query = "INSERT INTO tbl_customer 
                            (cust_name, cust_email, cust_phone, cust_address, cust_region, cust_password, cust_token, cust_datetime, cust_timestamp, cust_status)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($query);
                $stmt->execute([
                    htmlspecialchars($cust_name), 
                    htmlspecialchars($cust_email), 
                    htmlspecialchars($cust_phone), 
                    htmlspecialchars($cust_address), 
                    htmlspecialchars($cust_region), 
                    $hashed_password, 
                    $token, 
                    $cust_datetime, 
                    $cust_timestamp, 
                    1 
                ]);

                $success_message = "Registration successful. Please check your email for verification instructions.";
                header("Location: dashboard.php");
                exit;
            } catch (Exception $e) {
                $error_messages[] = "Something went wrong. Please try again later.";
            }
        }
    }
}
?>

<!-- Page banner -->
<div class="page-banner"
    style="background-color:#444;background-image: url(assets/uploads/<?php echo htmlspecialchars($banner_registration); ?>);">
    <div class="inner">
        <h1>Register</h1>
    </div>
</div>

<!-- Registration form -->
<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="user-content">
                    <form action="" method="post">
                        <?php $csrf->echoInputField(); ?>
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <!-- Display error or success messages -->
                                <?php if (!empty($error_messages)): ?>
                                <div class="alert alert-danger">
                                    <ul>
                                        <?php foreach ($error_messages as $message): ?>
                                        <li><?php echo htmlspecialchars($message); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <?php elseif (!empty($success_message)): ?>
                                <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
                                <?php endif; ?>

                                <!-- Form fields -->
                                <div class="form-group">
                                    <label>Full Name *</label>
                                    <input type="text" class="form-control" name="cust_name"
                                        value="<?php echo htmlspecialchars($_POST['cust_name'] ?? ''); ?>">
                                </div>

                                <div class="form-group">
                                    <label>Email Address *</label>
                                    <input type="email" class="form-control" name="cust_email"
                                        value="<?php echo htmlspecialchars($_POST['cust_email'] ?? ''); ?>">
                                </div>

                                <div class="form-group">
                                    <label>Phone Number *</label>
                                    <input type="text" class="form-control" name="cust_phone"
                                        value="<?php echo htmlspecialchars($_POST['cust_phone'] ?? ''); ?>">
                                </div>

                                <div class="col-md-12 form-group">
                                    <label for="">Region *</label>
                                    <select name="cust_region" class="form-control select2">
                                        <option value="">Select region</option>
                                        <?php
                                    $statement = $pdo->prepare("SELECT * FROM tbl_region ORDER BY region_name ASC");
                                    $statement->execute();
                                    $result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
                                    foreach ($result as $row) {
                                        ?>
                                        <option value="<?php echo $row['region_id']; ?>">
                                            <?php echo $row['region_name']; ?></option>
                                        <?php
                                    }
                                    ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Address *</label>
                                    <textarea name="cust_address"
                                        class="form-control"><?php echo htmlspecialchars($_POST['cust_address'] ?? ''); ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Password *</label>
                                    <input type="password" class="form-control" name="cust_password">
                                </div>

                                <div class="form-group">
                                    <label>Confirm Password *</label>
                                    <input type="password" class="form-control" name="cust_re_password">
                                </div>

                                <div class="form-group">
                                    <!-- Add reCAPTCHA -->
                                    <div class="g-recaptcha" data-sitekey="6Lc6WpQqAAAAANFIeIhUeszmfoJRn96F6JFoAZ3F">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <input type="submit" class="btn btn-danger" value="Register" name="form1">
                                </div>

                                <div class="form-group">
                                    <p>Already have an account? <a href="login.php">Login here</a>.</p>
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