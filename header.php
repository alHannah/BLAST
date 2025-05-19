<!-- This is main configuration File -->
<?php
ob_start();
session_start();
require_once("admin/inc/config.php");
require_once("admin/inc/functions.php");
require_once("admin/inc/CSRF_Protect.php");
$csrf = new CSRF_Protect();
$error_message = '';
$success_message = '';
$error_message1 = '';
$success_message1 = '';


$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row)
{
	$logo = $row['logo'];
	$favicon = $row['favicon'];
	$contact_email = $row['contact_email'];
	$contact_phone = $row['contact_phone'];
	$meta_title_home = $row['meta_title_home'];
    $meta_keyword_home = $row['meta_keyword_home'];
    $meta_description_home = $row['meta_description_home'];
    $before_head = $row['before_head'];
    $after_body = $row['after_body'];
}

// Checking the order table and removing the pending transaction that are 24 hours+ old. Very important
$current_date_time = date('Y-m-d H:i:s');
$statement = $pdo->prepare("SELECT * FROM tbl_payment WHERE payment_status=?");
$statement->execute(array('Pending'));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
foreach ($result as $row) {
	$ts1 = strtotime($row['payment_date']);
	$ts2 = strtotime($current_date_time);     
	$diff = $ts2 - $ts1;
	$time = $diff/(3600);
	if($time>24) {

		// Return back the stock amount
		$statement1 = $pdo->prepare("SELECT * FROM tbl_order WHERE payment_id=?");
		$statement1->execute(array($row['payment_id']));
		$result1 = $statement1->fetchAll(PDO::FETCH_ASSOC);
		foreach ($result1 as $row1) {
			$statement2 = $pdo->prepare("SELECT * FROM tbl_product WHERE p_id=?");
			$statement2->execute(array($row1['product_id']));
			$result2 = $statement2->fetchAll(PDO::FETCH_ASSOC);							
			foreach ($result2 as $row2) {
				$p_qty = $row2['p_qty'];
			}
			$final = $p_qty+$row1['quantity'];

			$statement = $pdo->prepare("UPDATE tbl_product SET p_qty=? WHERE p_id=?");
			$statement->execute(array($final,$row1['product_id']));
		}
		
		// Deleting data from table
		$statement1 = $pdo->prepare("DELETE FROM tbl_order WHERE payment_id=?");
		$statement1->execute(array($row['payment_id']));

		$statement1 = $pdo->prepare("DELETE FROM tbl_payment WHERE id=?");
		$statement1->execute(array($row['id']));
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Meta Tags -->
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/img/logo/favicon.png">

    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/owl.theme.default.min.css">
    <link rel="stylesheet" href="assets/css/jquery.bxslider.min.css">
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/rating.css">
    <link rel="stylesheet" href="assets/css/spacing.css">
    <link rel="stylesheet" href="assets/css/bootstrap-touch-slider.css">
    <link rel="stylesheet" href="assets/css/animate.min.css">
    <link rel="stylesheet" href="assets/css/tree-menu.css">
    <link rel="stylesheet" href="assets/css/select2.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/owl.theme.default.min.css">
    <link rel="stylesheet" href="assets/css/jquery.bxslider.min.css">
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/rating.css">
    <link rel="stylesheet" href="assets/css/spacing.css">
    <!-- SLIDER INDEX CAROUSEL -->
    <link rel="stylesheet" href="assets/css/bootstrap-touch-slider.css">
    <link rel="stylesheet" href="assets/css/animate.min.css">
    <link rel="stylesheet" href="assets/css/tree-menu.css">
    <link rel="stylesheet" href="assets/css/select2.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="assets/css/about.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="stylesheet" href="assets/css/product-index.css">

    <!-- Bootstrap CSS -->

    <!-- Bootstrap Bundle with Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>


    <?php

	$statement = $pdo->prepare("SELECT * FROM tbl_page WHERE id=1");
	$statement->execute();
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
	foreach ($result as $row) {
		$about_meta_title = $row['about_meta_title'];
		$about_meta_keyword = $row['about_meta_keyword'];
		$about_meta_description = $row['about_meta_description'];
		$faq_meta_title = $row['faq_meta_title'];
		$faq_meta_keyword = $row['faq_meta_keyword'];
		$faq_meta_description = $row['faq_meta_description'];
		$blog_meta_title = $row['blog_meta_title'];
		$blog_meta_keyword = $row['blog_meta_keyword'];
		$blog_meta_description = $row['blog_meta_description'];
		$contact_meta_title = $row['contact_meta_title'];
		$contact_meta_keyword = $row['contact_meta_keyword'];
		$contact_meta_description = $row['contact_meta_description'];
		$pgallery_meta_title = $row['pgallery_meta_title'];
		$pgallery_meta_keyword = $row['pgallery_meta_keyword'];
		$pgallery_meta_description = $row['pgallery_meta_description'];
		$vgallery_meta_title = $row['vgallery_meta_title'];
		$vgallery_meta_keyword = $row['vgallery_meta_keyword'];
		$vgallery_meta_description = $row['vgallery_meta_description'];
	}

	$cur_page = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
	
	if($cur_page == 'index.php' || $cur_page == 'login.php' || $cur_page == 'registration.php' || $cur_page == 'cart.php' || $cur_page == 'checkout.php' || $cur_page == 'forget-password.php' || $cur_page == 'reset-password.php' || $cur_page == 'product-category.php' || $cur_page == 'product.php') {
		?>

    <title><?php echo $meta_title_home; ?></title>
    <meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
    <meta name="description" content="<?php echo $meta_description_home; ?>">
    <?php
	}

	if($cur_page == 'about.php') {
		?>
    <title><?php echo $about_meta_title; ?></title>
    <meta name="keywords" content="<?php echo $about_meta_keyword; ?>">
    <meta name="description" content="<?php echo $about_meta_description; ?>">
    <?php
	}
	if($cur_page == 'contact.php') {
		?>
    <title><?php echo $contact_meta_title; ?></title>
    <meta name="keywords" content="<?php echo $contact_meta_keyword; ?>">
    <meta name="description" content="<?php echo $contact_meta_description; ?>">
    <?php
	}

    // RODUCTP
	if($cur_page == 'product.php')
	{
		$statement = $pdo->prepare("SELECT * FROM tbl_product WHERE p_id=?");
		$statement->execute(array($_REQUEST['id']));
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
		foreach ($result as $row) 
		{
		    $og_photo = $row['p_featured_photo'];
		    $og_title = $row['p_name'];
		    $og_slug = 'product.php?id='.$_REQUEST['id'];
			$og_description = substr(strip_tags($row['p_description']),0,200).'...';
		}
	}

	if($cur_page == 'dashboard.php') {
		?>
    <title>Dashboard - <?php echo $meta_title_home; ?></title>
    <meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
    <meta name="description" content="<?php echo $meta_description_home; ?>">
    <?php
	}
	if($cur_page == 'customer-profile-update.php') {
		?>
    <title>Update Profile - <?php echo $meta_title_home; ?></title>
    <meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
    <meta name="description" content="<?php echo $meta_description_home; ?>">
    <?php
	}
	if($cur_page == 'customer-billing-shipping-update.php') {
		?>
    <title>Update Billing and Shipping Info - <?php echo $meta_title_home; ?></title>
    <meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
    <meta name="description" content="<?php echo $meta_description_home; ?>">
    <?php
	}
	if($cur_page == 'customer-password-update.php') {
		?>
    <title>Update Password - <?php echo $meta_title_home; ?></title>
    <meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
    <meta name="description" content="<?php echo $meta_description_home; ?>">
    <?php
	}
	if($cur_page == 'customer-order.php') {
		?>
    <title>Order History - <?php echo $meta_title_home; ?></title>
    <meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
    <meta name="description" content="<?php echo $meta_description_home; ?>">
    <?php
	}
	?>

    <?php if($cur_page == 'product.php'): ?>
    <meta property="og:title" content="<?php echo $og_title; ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo BASE_URL.$og_slug; ?>">
    <meta property="og:description" content="<?php echo $og_description; ?>">
    <meta property="og:image" content="assets/uploads/<?php echo $og_photo; ?>">
    <?php endif; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>

    <script type="text/javascript"
        src="//platform-api.sharethis.com/js/sharethis.js#property=5993ef01e2587a001253a261&product=inline-share-buttons">
    </script>


    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <?php echo $before_head; ?>

</head>

<body>

    <?php echo $after_body; ?>

    <div class="header" style="<?php 
         if ($cur_page == 'index.php') {
             echo 'background-color: rgba(255, 255, 255, 0); z-index: 9999999; border-bottom: #000 2px solid; position: absolute;';
         } else {
             echo 'background-color: white;';
         }
     ?>">



        <div class="container-area">

            <div class="container-area-logo">

                <!-- LOGO DIV 1-->
                <div class="logo-area ">
                    <a href="index.php"><img src="assets/logo/blast_logo.png" alt="logo image"></a>
                </div>

                <!-- NAV DIV 2-->

                <div class="nav-area">
                    <ul class="ul-nav-area">
                        <div class="dropdown">
                            <div class="#">
                                <a href="side-category.php" class="nav-link" id="dropdownMenuButton" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    PRODUCTS<i class="fa fa-angle-down fa-fw dropdown-toggle"></i>
                                </a>
                                <ul class="dropdown-menu text-small" aria-labelledby="dropdownMenuButton">
                                    <li><a href="login.php" class="dropdown-item"><?php
                                // Query to get the top categories that should be displayed in the menu
                                $statement = $pdo->prepare("SELECT * FROM tbl_top_category WHERE show_on_menu=1");
                                $statement->execute();
                                $result = $statement->fetchAll(PDO::FETCH_ASSOC);

                                // Loop through top categories and display them
                                foreach ($result as $row) { ?>
                                    <li><a href="product-category.php?id=<?php echo $row['tcat_id']; ?>&type=top-category"
                                            class="dropdown-item">
                                            <?php echo htmlspecialchars($row['tcat_name']); ?> </a></li>
                                    <?php } ?></a></li>

                                </ul>

                            </div>
                        </div>
                        <li class="nav-item">
                            <a href="about.php" class="nav-link">ABOUT US</a>
                        </li>
                        <li class="nav-item">
                            <a href="team.php" class="nav-link">TEAM</a>
                        </li>
                    </ul>
                </div>

            </div>




            <div class="container-area-register">

                <!-- SEARCH FIELD DIV 3-->
                <form class="navbar-form navbar-left search-form" role="search" action="search-result.php" method="get">
                    <?php $csrf->echoInputField(); ?>
                    <div class="form-group">
                        <input type="text" class="form-control <?php if ($cur_page == 'index.php') {
             echo "search-top-light-header";
         } else {
             echo "search-top";
         }?> " placeholder="Search for products" name="search_text">
                    </div>
                    <button type="submit" class="btn">
                        <i class="fa fa-search fa-fw <?php if ($cur_page == 'index.php') {
             echo "fa-icon";
         } else {
             echo "fa-icon-other";
         }?> "> </i>
                    </button>
                </form>


                <!-- GUEST AND USER CAN SEE CART DIV 4 -->

                <a href=" cart.php"><i class="fa fa-shopping-cart fa-2x fa-fw <?php if ($cur_page == 'index.php') {
             echo "fa-icon";
         } else {
             echo "fa-icon-other";
         }?> "></i>(<?php
                    if (isset($_SESSION['cart_p_qty']) && is_array($_SESSION['cart_p_qty'])) {
                        $total_qty = array_sum($_SESSION['cart_p_qty']);
                        echo $total_qty;
                    } else {
                        echo '0';
                    }
                    ?>)
                </a>

                <div class=" dropdown text-end">
                    <?php 
                                // Check if the user is logged in
                                if (isset($_SESSION['customer'])) {
                                    // Ensure the user is active
                                    $statement = $pdo->prepare("SELECT * FROM tbl_customer WHERE cust_id=? AND cust_status=?");
                                    $statement->execute(array($_SESSION['customer']['cust_id'], 0));
                                    $isInactive = $statement->rowCount() > 0;
    
                                    // Force logout inactive users
                                    if ($isInactive) {
                                        header('Location: '.BASE_URL.'logout.php');
                                        exit;
                                    }
                            ?>

                    <!-- Logged-in User Menu -->
                    <div>
                        <a href="dashboard.php" class="d-block link-body-emphasis text-decoration-none dropdown-toggle"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-user fa-2x fa-fw <?php if ($cur_page == 'index.php') {
                                echo "fa-icon";
                            } else {
                                echo "fa-icon-other";
                            }?> ">
                            </i>
                        </a>
                        <ul class="dropdown-menu text-small">
                            <li class="dropdown-header">
                                <i class="fa fa-user fa-fw"></i> Welcome,
                                <?php echo htmlspecialchars($_SESSION['customer']['cust_name']); ?>
                            </li>
                            <li><a class="dropdown-item" href="dashboard.php">
                                    Dashboard</a></li>
                            <li><a class="dropdown-item" href="customer-profile-update.php">Update
                                    Profile</a>
                            </li>
                            <li><a class="dropdown-item" href="customer-billing-shipping-update.php">Update
                                    Billing
                                    and Shipping Info</a></li>
                            <li><a class="dropdown-item" href="customer-password-update.php">Update
                                    Password</a>
                            </li>
                            <li><a class="dropdown-item" href="customer-order.php">Order History</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fa fa-sign-out"></i>
                                    Sign
                                    out</a></li>
                        </ul>

                        <?php } else { ?>
                    </div>

                    <!-- Guest User Menu -->
                    <div class="dropdown">
                        <div>
                            <i class="fa fa-user-plus fa-2x fa-fw dropdown-toggle <?php if ($cur_page == 'index.php') {
                                echo "fa-icon";
                            } else {
                                echo "fa-icon-other";
                            }?> " id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            </i>
                            <ul class="dropdown-menu text-small" aria-labelledby="dropdownMenuButton">
                                <li><a href="registration.php" class="dropdown-item">Register</a></li>
                                <li><a href="login.php" class="dropdown-item">Login</a></li>
                            </ul>

                        </div>
                    </div>

                    <?php } ?>

                </div>
            </div>
        </div>
    </div>
    </div>
</body>

</html>