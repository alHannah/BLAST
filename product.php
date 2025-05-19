<?php require_once('header.php'); 
?>

<?php
if(!isset($_REQUEST['id'])) {
    header('location: index.php');
    exit;
} else {
    // Check the id is valid or not
    $statement = $pdo->prepare("SELECT * FROM tbl_product WHERE p_id=?");
    $statement->execute(array($_REQUEST['id']));
    $total = $statement->rowCount();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    if( $total == 0 ) {
        header('location: index.php');
        exit;
    }
}

foreach($result as $row) {
    $p_name = $row['p_name'];
    $p_old_price = $row['p_old_price'];
    $p_current_price = $row['p_current_price'];
    $p_qty = $row['p_qty'];
    $p_featured_photo = $row['p_featured_photo'];
    $p_description = $row['p_description'];
    $p_short_description = $row['p_short_description'];
    $p_feature = $row['p_feature'];
    $p_condition = $row['p_condition'];
    $p_return_policy = $row['p_return_policy'];
    $p_total_view = $row['p_total_view'];
    $p_is_featured = $row['p_is_featured'];
    $p_is_active = $row['p_is_active'];
    $ecat_id = $row['ecat_id'];
}




// Getting all categories name for breadcrumb
$statement = $pdo->prepare("SELECT
                        t1.ecat_id,
                        t1.ecat_name,
                        t1.mcat_id,

                        t2.mcat_id,
                        t2.mcat_name,
                        t2.tcat_id,

                        t3.tcat_id,
                        t3.tcat_name

                        FROM tbl_end_category t1
                        JOIN tbl_mid_category t2
                        ON t1.mcat_id = t2.mcat_id
                        JOIN tbl_top_category t3
                        ON t2.tcat_id = t3.tcat_id
                        WHERE t1.ecat_id=?");
$statement->execute(array($ecat_id));
$total = $statement->rowCount();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
foreach ($result as $row) {
    $ecat_name = $row['ecat_name'];
    $mcat_id = $row['mcat_id'];
    $mcat_name = $row['mcat_name'];
    $tcat_id = $row['tcat_id'];
    $tcat_name = $row['tcat_name'];
}

// ADDING TO TOTAL VIEW OF EACH PRODUCT
$p_total_view = $p_total_view + 1;

$statement = $pdo->prepare("UPDATE tbl_product SET p_total_view=? WHERE p_id=?");
$statement->execute(array($p_total_view,$_REQUEST['id']));


$statement = $pdo->prepare("SELECT * FROM tbl_product_size WHERE p_id=?");
$statement->execute(array($_REQUEST['id']));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
foreach ($result as $row) {
    $size[] = $row['size_id'];
}

$statement = $pdo->prepare("SELECT * FROM tbl_product_color WHERE p_id=?");
$statement->execute(array($_REQUEST['id']));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
foreach ($result as $row) {
    $color[] = $row['color_id'];
}

//  FORM REVIEW LOGIC
if (isset($_POST['form_review'])) {
    try {
        // Validate and sanitize input data
        $p_id = filter_var($_REQUEST['id'], FILTER_SANITIZE_NUMBER_INT);
        $cust_id = $_SESSION['customer']['cust_id'] ?? null;
        $comment = htmlspecialchars(trim($_POST['comment']));
        $rating = filter_var($_POST['rating'], FILTER_SANITIZE_NUMBER_INT);

        if (!$p_id || !$cust_id || !$rating || $rating < 1 || $rating > 5) {
            $error_message = "Invalid input. Please ensure all fields are filled out correctly.";
        } else {
            // Check if the customer has already submitted a review
            $statement = $pdo->prepare("SELECT * FROM tbl_rating WHERE p_id = ? AND cust_id = ?");
            $statement->execute([$p_id, $cust_id]);
            $total = $statement->rowCount();

            if ($total > 0) {
                $error_message = "You have already submitted a review for this product.";
            } else {
                // Insert the new review
                $statement = $pdo->prepare("INSERT INTO tbl_rating (p_id, cust_id, comment, rating) VALUES (?, ?, ?, ?)");
                $statement->execute([$p_id, $cust_id, $comment, $rating]);
                $success_message = "Thank you for your review! Your feedback has been submitted.";
            }
        }
    } catch (PDOException $e) {
        $error_message = "An error occurred while processing your review. Please try again later.";
    }
}


// Getting the average rating for this product
$t_rating = 0;
$statement = $pdo->prepare("SELECT * FROM tbl_rating WHERE p_id=?");
$statement->execute(array($_REQUEST['id']));
$tot_rating = $statement->rowCount();
if($tot_rating == 0) {
    $avg_rating = 0;
} else {
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
    foreach ($result as $row) {
        $t_rating = $t_rating + $row['rating'];
    }
    $avg_rating = $t_rating / $tot_rating;
}

if(isset($_POST['form_add_to_cart'])) {

	// getting the currect stock of this product
	$statement = $pdo->prepare("SELECT * FROM tbl_product WHERE p_id=?");
	$statement->execute(array($_REQUEST['id']));
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
	foreach ($result as $row) {
		$current_p_qty = $row['p_qty'];
	}
	if($_POST['p_qty'] > $current_p_qty):
		$temp_msg = 'Sorry! There are only '.$current_p_qty.' item(s) in stock';
		?>
<script type="text/javascript">
alert('<?php echo $temp_msg; ?>');
</script>
<?php
	else:
    if(isset($_SESSION['cart_p_id']))
    {
        $arr_cart_p_id = array();
        $arr_cart_size_id = array();
        $arr_cart_color_id = array();
        $arr_cart_p_qty = array();
        $arr_cart_p_current_price = array();

        $i=0;
        foreach($_SESSION['cart_p_id'] as $key => $value) 
        {
            $i++;
            $arr_cart_p_id[$i] = $value;
        }

        $i=0;
        foreach($_SESSION['cart_size_id'] as $key => $value) 
        {
            $i++;
            $arr_cart_size_id[$i] = $value;
        }

        $i=0;
        foreach($_SESSION['cart_color_id'] as $key => $value) 
        {
            $i++;
            $arr_cart_color_id[$i] = $value;
        }


        $added = 0;
        if(!isset($_POST['size_id'])) {
            $size_id = 0;
        } else {
            $size_id = $_POST['size_id'];
        }
        if(!isset($_POST['color_id'])) {
            $color_id = 0;
        } else {
            $color_id = $_POST['color_id'];
        }
        for($i=1;$i<=count($arr_cart_p_id);$i++) {
            if( ($arr_cart_p_id[$i]==$_REQUEST['id']) && ($arr_cart_size_id[$i]==$size_id) && ($arr_cart_color_id[$i]==$color_id) ) {
                $added = 1;
                break;
            }
        }
        if($added == 1) {
           $error_message1 = 'This product is already added to the shopping cart.';
        } else {

            $i=0;
            foreach($_SESSION['cart_p_id'] as $key => $res) 
            {
                $i++;
            }
            $new_key = $i+1;

            if(isset($_POST['size_id'])) {

                $size_id = $_POST['size_id'];

                $statement = $pdo->prepare("SELECT * FROM tbl_size WHERE size_id=?");
                $statement->execute(array($size_id));
                $result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
                foreach ($result as $row) {
                    $size_name = $row['size_name'];
                }
            } else {
                $size_id = 0;
                $size_name = '';
            }
            
            if(isset($_POST['color_id'])) {
                $color_id = $_POST['color_id'];
                $statement = $pdo->prepare("SELECT * FROM tbl_color WHERE color_id=?");
                $statement->execute(array($color_id));
                $result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
                foreach ($result as $row) {
                    $color_name = $row['color_name'];
                }
            } else {
                $color_id = 0;
                $color_name = '';
            }
          

            $_SESSION['cart_p_id'][$new_key] = $_REQUEST['id'];
            $_SESSION['cart_size_id'][$new_key] = $size_id;
            $_SESSION['cart_size_name'][$new_key] = $size_name;
            $_SESSION['cart_color_id'][$new_key] = $color_id;
            $_SESSION['cart_color_name'][$new_key] = $color_name;
            $_SESSION['cart_p_qty'][$new_key] = $_POST['p_qty'];
            $_SESSION['cart_p_current_price'][$new_key] = $_POST['p_current_price'];
            $_SESSION['cart_p_name'][$new_key] = $_POST['p_name'];
            $_SESSION['cart_p_featured_photo'][$new_key] = $_POST['p_featured_photo'];

            $success_message1 = 'Product is added to the cart successfully!';
        }
        
    }
    else
    {

        if(isset($_POST['size_id'])) {

            $size_id = $_POST['size_id'];

            $statement = $pdo->prepare("SELECT * FROM tbl_size WHERE size_id=?");
            $statement->execute(array($size_id));
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
            foreach ($result as $row) {
                $size_name = $row['size_name'];
            }
        } else {
            $size_id = 0;
            $size_name = '';
        }
        
        if(isset($_POST['color_id'])) {
            $color_id = $_POST['color_id'];
            $statement = $pdo->prepare("SELECT * FROM tbl_color WHERE color_id=?");
            $statement->execute(array($color_id));
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
            foreach ($result as $row) {
                $color_name = $row['color_name'];
            }
        } else {
            $color_id = 0;
            $color_name = '';
        }
        

        $_SESSION['cart_p_id'][1] = $_REQUEST['id'];
        $_SESSION['cart_size_id'][1] = $size_id;
        $_SESSION['cart_size_name'][1] = $size_name;
        $_SESSION['cart_color_id'][1] = $color_id;
        $_SESSION['cart_color_name'][1] = $color_name;
        $_SESSION['cart_p_qty'][1] = $_POST['p_qty'];
        $_SESSION['cart_p_current_price'][1] = $_POST['p_current_price'];
        $_SESSION['cart_p_name'][1] = $_POST['p_name'];
        $_SESSION['cart_p_featured_photo'][1] = $_POST['p_featured_photo'];

        $success_message1 = 'Product is added to the cart successfully!';
    }
	endif;
}
?>


<?php
if($error_message1 != '') {
    echo "<script>alert('".$error_message1."')</script>";
}
if($success_message1 != '') {
    echo "<script>alert('".$success_message1."')</script>";
    header('location: product.php?id='.$_REQUEST['id']);
}
?>


<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <!-- BREADCRUMB NAVIGATION -->
                <div class="breadcrumb mb_30">
                    <ul>
                        <li><a href="<?php echo BASE_URL.'all-product.php'; ?>">All Products</a></li>
                        <li>></li>
                        <li><a
                                href="<?php echo BASE_URL.'product-category.php?id='.$tcat_id.'&type=top-category' ?>"><?php echo $tcat_name; ?></a>
                        </li>
                        <li>></li>
                        <li><a
                                href="<?php echo BASE_URL.'product-category.php?id='.$mcat_id.'&type=mid-category' ?>"><?php echo $mcat_name; ?></a>
                        </li>
                        <li>></li>
                        <li><a
                                href="<?php echo BASE_URL.'product-category.php?id='.$ecat_id.'&type=end-category' ?>"><?php echo $ecat_name; ?></a>
                        </li>
                        <li>></li>
                        <li><?php echo $p_name; ?></li>
                    </ul>
                </div>

                <div class="product">
                    <div class="row">
                        <div class="col-md-5">
                            <ul class="prod-slider">

                                <li style="background-image: url(assets/uploads/<?php echo $p_featured_photo; ?>);">
                                    <a class="popup" href="assets/uploads/<?php echo $p_featured_photo; ?>"></a>
                                </li>
                                <?php
                                $statement = $pdo->prepare("SELECT * FROM tbl_product_photo WHERE p_id=?");
                                $statement->execute(array($_REQUEST['id']));
                                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($result as $row) {
                                    ?>
                                <li
                                    style="background-image: url(assets/uploads/product_photos/<?php echo $row['photo']; ?>);">
                                    <a class="popup"
                                        href="assets/uploads/product_photos/<?php echo $row['photo']; ?>"></a>
                                </li>
                                <?php
                                }
                                ?>
                            </ul>

                            <div id="prod-pager">
                                <a data-slide-index="0" href="">
                                    <div class="prod-pager-thumb"
                                        style="background-image: url(assets/uploads/<?php echo $p_featured_photo; ?>">
                                    </div>
                                </a>
                                <?php
                                $i=1;
                                $statement = $pdo->prepare("SELECT * FROM tbl_product_photo WHERE p_id=?");
                                $statement->execute(array($_REQUEST['id']));
                                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($result as $row) {
                                    ?>
                                <a data-slide-index="<?php echo $i; ?>" href="">
                                    <div class="prod-pager-thumb"
                                        style="background-image: url(assets/uploads/product_photos/<?php echo $row['photo']; ?>">
                                    </div>
                                </a>
                                <?php
                                    $i++;
                                }
                                ?>
                            </div>
                        </div>


                        <div class="col-md-7">
                            <div class="p-title">
                                <h2><?php echo $p_name; ?></h2>
                            </div>
                            <div class="p-review">
                                <!-- Product Rating -->
                                <div class="rating">
                                    <?php
        // Ensure the database connection is established
        if (isset($_REQUEST['id'])) {
            // Fetching product ratings
            $ratingStatement = $pdo->prepare("SELECT rating FROM tbl_rating WHERE p_id = ?");
            $ratingStatement->execute([$_REQUEST['id']]);
            $ratings = $ratingStatement->fetchAll(PDO::FETCH_COLUMN);

            // Calculate total ratings and average rating
            $totalRatings = count($ratings);
            $averageRating = $totalRatings > 0 ? round(array_sum($ratings) / $totalRatings, 1) : 0;

            // Display star ratings
            for ($i = 1; $i <= 5; $i++) {
                if ($i <= $averageRating) {
                    echo '<i class="fa fa-star"></i>';
                } else {
                    echo '<i class="fa fa-star-o"></i>';
                }
            }
        ?>
                                    <span><?php echo $averageRating; ?>★</span>
                                    <small>(<?php echo $totalRatings; ?> sold)</small>
                                    <?php
        } else {
            echo "<span>No ratings available</span>";
        }
        ?>
                                </div>
                            </div>


                            <div class="p-short-des">
                                <p>
                                    <?php echo $p_short_description; ?>
                                </p>
                            </div>


                            <form action="" method="post">
                                <div class="p-quantity">
                                    <div class="row">
                                        <!-- Size Selection -->
                                        <?php if (isset($size)): ?>
                                        <div class="col-md-12 mb_20">
                                            <label for="size_id">Select Size:</label>
                                            <select name="size_id" id="size_id" class="form-control select2"
                                                style="width:auto;">
                                                <?php
                                                $statement = $pdo->prepare("SELECT * FROM tbl_size");
                                                $statement->execute();
                                                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                                foreach ($result as $row) {
                                                    if (in_array($row['size_id'], $size)) {
                                                        echo '<option value="' . htmlspecialchars($row['size_id']) . '">' . htmlspecialchars($row['size_name']) . '</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <?php endif; ?>

                                        <!-- Color Selection -->
                                        <?php if (isset($color)): ?>
                                        <div class="col-md-12">
                                            <label for="color_id">Select Color:</label>
                                            <select name="color_id" id="color_id" class="form-control select2"
                                                style="width:auto;">
                                                <?php
                                                $statement = $pdo->prepare("SELECT * FROM tbl_color");
                                                $statement->execute();
                                                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                                foreach ($result as $row) {
                                                    if (in_array($row['color_id'], $color)) {
                                                        echo '<option value="' . htmlspecialchars($row['color_id']) . '">' . htmlspecialchars($row['color_name']) . '</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <?php endif; ?>

                                    </div>
                                </div>

                                <!-- Price Display -->
                                <div class="p-price">
                                    <span style="font-size:14px;">Price:</span><br>
                                    <span>
                                        <?php if (!empty($p_old_price)): ?>
                                        <del><?php echo htmlspecialchars($p_old_price); ?></del>
                                        <?php endif; ?>
                                        <?php echo htmlspecialchars($p_current_price); ?>
                                    </span>
                                </div>

                                <!-- Hidden Fields -->
                                <input type="hidden" name="p_current_price"
                                    value="<?php echo htmlspecialchars($p_current_price); ?>">
                                <input type="hidden" name="p_name" value="<?php echo htmlspecialchars($p_name); ?>">
                                <input type="hidden" name="p_featured_photo"
                                    value="<?php echo htmlspecialchars($p_featured_photo); ?>">

                                <!-- Quantity Selection -->
                                <div class="p-quantity">
                                    <label for="p_qty">Quantity:</label><br>
                                    <input type="number" class="input-text qty" step="1" min="1" name="p_qty" id="p_qty"
                                        value="1" title="Quantity" size="4" pattern="[0-9]*" inputmode="numeric">
                                </div>

                                <!-- Add to Cart Button -->
                                <div class="btn-cart btn-cart1">
                                    <input type="submit" value="Add to Cart" name="form_add_to_cart">
                                </div>
                            </form>

                            <div class="share">
                                <p>Share this product:</p>
                                <div class="sharethis-inline-share-buttons"></div>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <!-- Navigation Tabs -->
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active">
                                    <a href="#description" aria-controls="description" role="tab"
                                        data-toggle="tab">Description</a>
                                </li>
                                <li role="presentation">
                                    <a href="#feature" aria-controls="feature" role="tab" data-toggle="tab">Features</a>
                                </li>
                                <li role="presentation">
                                    <a href="#condition" aria-controls="condition" role="tab"
                                        data-toggle="tab">Condition</a>
                                </li>
                                <li role="presentation">
                                    <a href="#return_policy" aria-controls="return_policy" role="tab"
                                        data-toggle="tab">Return
                                        Policy</a>
                                </li>
                                </li>
                                <li role="presentation">
                                    <a href="#review" aria-controls="review" role="tab" data-toggle="tab">Reviews</a>
                                </li>
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content">
                                <!-- Description Tab -->
                                <div role="tabpanel" class="tab-pane active" id="description"
                                    style="margin-top: -30px;">
                                    <p><?php echo empty($p_description) ? "Description not available." : htmlspecialchars($p_description); ?>
                                    </p>
                                </div>

                                <!-- Features Tab -->
                                <div role="tabpanel" class="tab-pane" id="feature" style="margin-top: -30px;">
                                    <p><?php echo empty($p_feature) ? "Features not available." : htmlspecialchars($p_feature); ?>
                                    </p>
                                </div>

                                <!-- Condition Tab -->
                                <div role="tabpanel" class="tab-pane" id="condition" style="margin-top: -30px;">
                                    <p><?php echo empty($p_condition) ? "Condition information not available." : htmlspecialchars($p_condition); ?>
                                    </p>
                                </div>

                                <!-- Return Policy Tab -->
                                <div role="tabpanel" class="tab-pane" id="return_policy" style="margin-top: -30px;">
                                    <p><?php echo empty($p_return_policy) ? "Return policy information not available." : htmlspecialchars($p_return_policy); ?>
                                    </p>
                                </div>

                                <!-- Reviews Tab -->
                                <div role="tabpanel" class="tab-pane" id="review" style="margin-top: -30px;">
                                    <div class="review-form">
                                        <?php
                                        // Fetch reviews
                                        $statement = $pdo->prepare(
                                            " SELECT
                                            t1.*, t2.cust_name 
                                            FROM tbl_rating t1 
                                            JOIN tbl_customer t2 
                                            ON t1.cust_id = t2.cust_id 
                                            WHERE t1.p_id=?"
                                        );
                                        
                                        $statement->execute([$_REQUEST['id']]);
                                        $totalReviews = $statement->rowCount();

                                        echo "<h2>Customer Reviews ($totalReviews)</h2>";
                                        if ($totalReviews) {
                                            $reviews = $statement->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($reviews as $index => $review) {
                                                echo "<div class='mb_10'><b><u>Review " . ($index + 1) . "</u></b></div>";
                                                echo "<table class='table table-bordered'>";
                                                echo "<tr><th>Name</th><td>" . htmlspecialchars($review['cust_name']) . "</td></tr>";
                                                echo "<tr><th>Comment</th><td>" . htmlspecialchars($review['comment']) . "</td></tr>";
                                                echo "<tr><th>Rating</th><td>";
                                                for ($i = 1; $i <= 5; $i++) {
                                                    echo $i <= $review['rating'] ? "<i class='fa fa-star'></i>" : "<i class='fa fa-star-o'></i>";
                                                }
                                                echo "</td></tr>";
                                                echo "</table>";
                                            }
                                        } else {
                                            echo "<p>No reviews yet.</p>";
                                        }
                                        ?>

                                        <h2>Write a Review</h2>
                                        <?php
                                        if (!empty($error_message)) {
                                            echo "<div class='alert alert-danger'>" . htmlspecialchars($error_message) . "</div>";
                                        }
                                        if (!empty($success_message)) {
                                            echo "<div class='alert alert-success'>" . htmlspecialchars($success_message) . "</div>";
                                        }
                                        ?>

                                        <?php if (isset($_SESSION['customer'])): ?>
                                        <?php
                                        $statement = $pdo->prepare("SELECT * FROM tbl_rating WHERE p_id=? AND cust_id=?");
                                        $statement->execute([$_REQUEST['id'], $_SESSION['customer']['cust_id']]);
                                        $alreadyReviewed = $statement->rowCount();
                                        ?>
                                        <?php if (!$alreadyReviewed): ?>

                                        <form action="" method="post">
                                            <div class="form-group">
                                                <label>Rating:</label>
                                                <div class="rating-section">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <label>
                                                        <input type="radio" name="rating" value="<?php echo $i; ?>"
                                                            <?php echo $i === 5 ? 'checked' : ''; ?>>
                                                        <?php echo $i; ?> Star
                                                    </label>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="comment">Comment (optional):</label>
                                                <textarea name="comment" id="comment" class="form-control" rows="5"
                                                    placeholder="Write your comment"></textarea>
                                            </div>
                                            <button type="submit" name="form_review" class="btn btn-primary">Submit
                                                Review</button>
                                        </form>

                                        <?php else: ?>
                                        <p style="color: red;">You have already submitted a review for this product.</p>
                                        <?php endif; ?>
                                        <?php else: ?>
                                        <p class="error">
                                            Please <a href="login.php"
                                                style="color: red; text-decoration: underline;">log
                                                in</a> to leave a
                                            review.
                                        </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="product bg-gray pt_70 pb_70">

    <div class="row">
        <div class="col-md-12">
            <div class="headline">
                <h2>Recommended Products</h2>
                <h3>Explore similar items you may like</h3>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">

            <div class="carousel-scroll parent-product-div">
                <?php
            // Define the limit for the number of products
            $limit = 10;

            // Fetching featured products with associated category logos
            $statement = $pdo->prepare(
            "SELECT 
                p.p_id, 
                p.p_name, 
                p.p_featured_photo, 
                p.p_current_price, 
                p.p_old_price, 
                p.p_qty, 
                p.p_is_featured, 
                p.p_is_active, 
                tc.tcat_logo 
            FROM tbl_product p 
            LEFT JOIN tbl_top_category tc 
            ON p.tcat_id = tc.tcat_id 
            WHERE p.p_is_featured = ? AND p.p_is_active = ? 
            LIMIT $limit"
            );
            $statement->execute([1, 1]);
            $products = $statement->fetchAll(PDO::FETCH_ASSOC);

            foreach ($products as $product): ?>

                <div class="item product-area-holder">


                    <!-- Top Category Logo -->
                    <div class="category-logo"
                        style="background-image: url('assets/uploads/<?php echo htmlspecialchars($product['tcat_logo']); ?>');">
                    </div>

                    <div class="price-rating-container">
                        <!-- Product Price with Discount -->
                        <div class="price">
                            <?php if ($product['p_current_price'] < $product['p_old_price']): ?>
                            <span
                                class="current-price">₱<?php echo htmlspecialchars($product['p_current_price']); ?></span>
                            <span class="old-price">₱<?php echo htmlspecialchars($product['p_old_price']); ?></span>
                            <?php else: ?>
                            <span
                                class="current-price">₱<?php echo htmlspecialchars($product['p_current_price']); ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Product Rating -->
                        <div class="rating">
                            <?php
                        // Fetching product ratings
                        $ratingStatement = $pdo->prepare("SELECT rating FROM tbl_rating WHERE p_id = ?");
                        $ratingStatement->execute([$product['p_id']]);
                        $ratings = $ratingStatement->fetchAll(PDO::FETCH_COLUMN);

                        $totalRatings = count($ratings);
                        $averageRating = $totalRatings > 0 ? round(array_sum($ratings) / $totalRatings, 1) : 0;
                        ?>
                            <span><?php echo $averageRating; ?>★</span>
                            <small>(<?php echo $totalRatings; ?> sold)</small>
                        </div>
                    </div>


                    <div class="product-card">

                        <!-- Product Image -->
                        <div class="photo"
                            style="background-image: url('assets/uploads/<?php echo htmlspecialchars($product['p_featured_photo']); ?>');">

                        </div>

                        <!-- Dynamic Badges -->
                        <?php if ($product['p_qty'] > 100): ?>
                        <span class="badge badge-top-sale">TOP SALE</span>
                        <?php elseif ($averageRating >= 4.5): ?>
                        <span class="badge badge-best-seller">BEST SELLER</span>
                        <?php endif; ?>



                        <!-- Stock Availability -->
                        <?php if ((int)$product['p_qty'] === 0): ?>
                        <div class="out-of-stock">Out Of Stock</div>
                        <?php else: ?>
                    </div>

                    <div class="product-title">
                        <!-- Product Name -->

                        <h3 class="product-name-area">
                            <a class="product-name-area"
                                href="product.php?id=<?php echo htmlspecialchars($product['p_id']); ?>">
                                <?php echo htmlspecialchars($product['p_name']); ?>
                            </a>
                        </h3>
                        <!-- But Now Btn -->

                        <p>
                            <a href="product.php?id=<?php echo htmlspecialchars($product['p_id']); ?>"
                                class="product-btn-area">
                                BUY NOW
                            </a>
                        </p>
                    </div>
                    <?php endif; ?>
                </div>

                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
</div>

<?php require_once('footer.php'); ?>