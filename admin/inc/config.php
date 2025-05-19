<?php
// Error Reporting Turn On
ini_set('error_reporting', E_ALL);

// Setting up the time zone
date_default_timezone_set('America/Los_Angeles');

// Host Name
$dbhost = 'localhost';

// Database Name
$dbname = 'ecommerceweb';

// Database Username
$dbuser = 'root';

// Database Password
$dbpass = '';


define('PAYPAL_CLIENT_ID', 'AYrnzWYj5dJ7CAn-kO7r-EpRDOO3ULVNSVGSiMWIsfMbH7YktNqwEhYMtrgoP3_AXHrl8HZJil-CDy2a');
define('PAYPAL_SECRET_ID', 'ECM4WJVRw94-xCrbzqApPQCI1HL13o5l0tpaT8S7jh3WOfAC3MF_60WdDzqPryUz24o5UJ2f_DU0_1AX');
define('PAYPAL_API', 'https://api.sandbox.paypal.com/v1/');
define('APP_URL', 'https://22c7-112-208-183-156.ngrok-free.app');  // current running ngrok Forwarding

// Defining base url
define("BASE_URL", "http://localhost/my_prj/");

// Getting Admin url
define("ADMIN_URL", BASE_URL . "admin" . "/");

try {
	$pdo = new PDO("mysql:host={$dbhost};dbname={$dbname}", $dbuser, $dbpass);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch( PDOException $exception ) {
	echo "Connection error :" . $exception->getMessage();
}