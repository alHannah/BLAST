<?php 
ob_start();
session_start();
session_unset();
session_destroy();
include 'admin/inc/config.php';
unset($_SESSION['customer']);
header("location:" . 'index.php'); 
exit;
?>