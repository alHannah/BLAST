<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Check if reCAPTCHA response is provided
    if (empty($_POST['g-recaptcha-response'])) {
        echo "<script>alert('Please complete the reCAPTCHA.'); window.location.href='../signup.html';</script>";
        exit;
    }

    // sitekey = 6Lc6WpQqAAAAANFIeIhUeszmfoJRn96F6JFoAZ3F
    $secret = '6Lc6WpQqAAAAAN4rU_IEDoXWjseE_2_f92TLOZ8k';
    $responseKey = $_POST['g-recaptcha-response'];
    $userIP = $_SERVER['REMOTE_ADDR'];

    // Send the request to Google's reCAPTCHA API
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

    // Check reCAPTCHA validation result
    if ($response && $response->success) {
        // Redirect or handle successful validation
        echo "<script>alert('Registration successful.'); window.location.href='../signin.html';</script>";
    } else {
        // Handle failure
        echo "<script>alert('reCAPTCHA verification failed. Please try again.'); window.location.href='../signup.html';</script>";
    }
}
?>