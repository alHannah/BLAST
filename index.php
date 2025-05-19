<?php require_once('header.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);          // Inspect fetched results
?>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row)
{
    $cta_title = $row['cta_title'];
    $cta_content = $row['cta_content'];
    $cta_read_more_text = $row['cta_read_more_text'];
    $cta_read_more_url = $row['cta_read_more_url'];
    $cta_photo = $row['cta_photo'];
    $featured_product_title = $row['featured_product_title'];
    $featured_product_subtitle = $row['featured_product_subtitle'];
    $latest_product_title = $row['latest_product_title'];
    $latest_product_subtitle = $row['latest_product_subtitle'];
    $popular_product_title = $row['popular_product_title'];
    $popular_product_subtitle = $row['popular_product_subtitle'];
    $total_featured_product_home = $row['total_featured_product_home'];
    $total_latest_product_home = $row['total_latest_product_home'];
    $total_popular_product_home = $row['total_popular_product_home'];
    $home_service_on_off = $row['home_service_on_off'];
    $home_welcome_on_off = $row['home_welcome_on_off'];
    $home_featured_product_on_off = $row['home_featured_product_on_off'];
    $home_latest_product_on_off = $row['home_latest_product_on_off'];
    $home_popular_product_on_off = $row['home_popular_product_on_off'];

}
?>

<div id="bootstrap-touch-slider" class="carousel control-round fade indicators-line " data-ride="carousel"
    data-pause="hover" data-interval="false">

    <!-- Indicators -->
    <ol class="carousel-indicators">
        <?php
        $i=0;
        $statement = $pdo->prepare("SELECT * FROM tbl_slider");
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
        foreach ($result as $row) {            
            ?>
        <li data-target="#bootstrap-touch-slider" data-slide-to="<?php echo $i; ?>"
            <?php if($i==0) {echo 'class="active"';} ?>></li>
        <?php
            $i++;
        }
        ?>
    </ol>

    <!-- Wrapper For Slides -->
    <div class="carousel-inner" role="listbox">
        <?php
    $i = 0;
    $statement = $pdo->prepare("SELECT * FROM tbl_slider");
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $row) {
    ?>
        <div class="item bs-slider <?php if($i == 0) { echo 'active'; } ?>"
            style="background-image: url(assets/uploads/<?php echo $row['photo']; ?>);">
            <div class="container bs-slider-overlay">
                <div class="col-md-6">
                    <div class="slide-text">
                        <p class="content-top" style="color: <?php echo $row['content2_color']; ?>;"
                            data-animation="animated <?php if($row['position'] == 'Left') {echo 'fadeInLeft';} elseif($row['position'] == 'Down') {echo 'fadeInDown';} elseif($row['position'] == 'Right') {echo 'fadeInRight';} ?>">
                            <?php echo nl2br($row['content2']); ?>
                        </p>
                        <h1 style="color: <?php echo $row['heading_color']; ?>;"
                            data-animation="animated <?php if($row['position'] == 'Left') {echo 'zoomInLeft';} elseif($row['position'] == 'Down') {echo 'flipInX';} elseif($row['position'] == 'Right') {echo 'zoomInRight';} ?>">
                            <?php echo $row['heading']; ?>
                        </h1>
                        <p class="content-bottom" style="color: <?php echo $row['content_color']; ?>;"
                            data-animation="animated <?php if($row['position'] == 'Left') {echo 'fadeInLeft';} elseif($row['position'] == 'Down') {echo 'fadeInDown';} elseif($row['position'] == 'Right') {echo 'fadeInRight';} ?>">
                            <?php echo nl2br($row['content']); ?>
                        </p>
                        <?php if (!empty($row['button_url'])): ?>
                        <a href="<?php echo $row['button_url']; ?>" target="_blank" class="btn btn-primary slider-btn"
                            style="background-color: <?php echo $row['button_color']; ?>; color: <?php echo $row['button_text_color']; ?>; margin-right: 10rem;"
                            data-animation="animated 
                            <?php 
                                if ($row['position'] == 'Left') {
                                    echo 'fadeInLeft';
                                } elseif ($row['position'] == 'Down') {
                                    echo 'fadeInDown';
                                } elseif ($row['position'] == 'Right') {
                                    echo 'fadeInRight';
                                } 
                            ?>">
                            <?php echo $row['button_text']; ?>
                        </a>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
        <?php $i++; } ?>
    </div>

    <!-- Slider Left Control -->
    <a class="left carousel-control scroll-icon" href="#bootstrap-touch-slider" role="button" data-slide="prev"
        style="z-index: 9999;">
        <span class="fa fa-angle-left" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>

    <!-- Slider Right Control -->
    <a class="right carousel-control scroll-icon" href="#bootstrap-touch-slider" role="button" data-slide="next"
        style="z-index: 9999;">
        <span class="fa fa-angle-right" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>

</div>


<!-- Exclusive Offers Section -->
<?php if($home_featured_product_on_off == 1): ?>
<div class="exclusive-area-section pt_70 pb_70">
    <div class="exclusive-area-container">
        <div class="exclusive-area-header">
            <div class="text-container">
                <span class="exclusive">EXCLUSIVE</span>
                <span class="offers">OFFERS</span>
            </div>
            <p><?php echo $featured_product_subtitle; ?></p>
        </div>

        <!-- Offer Boxes -->
        <div class="sale-container">

            <div class="sale-container-row">
                <!-- Offer 1 -->
                <div class="offer-box div-animate-thumb">
                    <a href="" class="img-container">
                        <img src="assets/img/exclusive/1.png" alt="Coming Soon">
                        <div class="overlay"></div>
                    </a>
                </div>
                <!-- Offer 2 -->
                <div class="offer-box div-animate-thumb">
                    <a href="" class="img-container">
                        <img src="assets/img/exclusive/2.png" alt="Coming Soon">
                        <div class="overlay"></div>
                    </a>
                </div>
            </div>

            <div class="sale-container-col">
                <!-- Offer 3 -->
                <div class="offer-box div-animate-thumb">
                    <a href="" class="img-container">
                        <img src="assets/img/exclusive/3.png" alt="Coming Soon">
                        <div class="overlay"></div>
                    </a>
                </div>
                <!-- Offer 4 -->
                <div class="offer-box div-animate-thumb">
                    <a href="" class="img-container">
                        <img src="assets/img/exclusive/4.png" alt="Coming Soon">
                        <div class="overlay"></div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>


<!-- Excplore your organization  -->
<?php if($home_latest_product_on_off == 1): ?>
<div class="explore-area-section pt_70 pb_70">
    <div class="explore-area-container">
        <div class="explore-area-header">
            <h2><?php echo $latest_product_title; ?></h2>
            <p><?php echo $latest_product_subtitle; ?></p>
        </div>
        <div class="explore-area-gallery">
            <?php
            // Fetch all top-category photos
            $statement = $pdo->prepare("SELECT * FROM tbl_top_category");
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $row) {
            ?>
            <div class="explore-area-item">
                <a href="product-category.php?id=<?php echo $row['tcat_id']; ?>&type=top-category">
                    <div class="explore-area-thumb"
                        style="background-image: url('assets/uploads/<?php echo $row['tcat_photo']; ?>');">
                    </div>
                    <div class="explore-area-overlay"></div>
                </a>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php endif; ?>


<!-- PRODUCT -->
<?php if($home_popular_product_on_off == 1): ?>
<div class="product-area-section pt_70 pb_70">
    <div class="product-area-container">
        <div class="product-area-header">
            <h2><?php echo $popular_product_title; ?></h2>
            <h3><?php echo $popular_product_subtitle; ?></h3>
        </div>
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
                        <span class="current-price">₱<?php echo htmlspecialchars($product['p_current_price']); ?></span>
                        <span class="old-price">₱<?php echo htmlspecialchars($product['p_old_price']); ?></span>
                        <?php else: ?>
                        <span class="current-price">₱<?php echo htmlspecialchars($product['p_current_price']); ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Product Rating -->
                    <div class="rating-area">
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
        <!-- Offer Boxes -->
        <!-- <div class="other-offer-container">
            <div class="#">
                <div class="sale-container">
                    <div class="offer-box">
                        <a href="">
                            <img src="assets/img/explore/1.png" alt="Coming Soon">
                        </a>
                    </div>
                    <div class="offer-box">
                        <a href="">
                            <img src="assets/img/explore/2.png" alt="Coming Soon">
                        </a>
                    </div>
                </div>
            </div>
        </div> -->
    </div>
</div>
<?php endif; ?>


<!-- ABOUT US -->

<div class="product pt_70 pb_70">


    <?php
    $statement = $pdo->prepare("SELECT * FROM tbl_page WHERE id=1");
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
    foreach ($result as $row) {
    $about_title = $row['about_title'];
        $about_content = $row['about_content'];
        $about_banner = $row['about_banner'];
    }
    ?>
    <div class="about-banner" style="background-image: url('assets/uploads/<?php echo $about_banner; ?>');">
        <div class="about-title">
            <h1 class="about-title ">about</h1>
        </div>
        <div class="content">
            <p>BuLSU-HC Loyalty Apparel and School Treasures (BLAST) is an e-commerce website
                specially created to showcase the diverse merchandise of every organization at
                Bulacan State University - Hagonoy Campus. This platform serves as a marketplace
                for students, alumni, and supporters who want to support their respective
                organizations by purchasing uniquely designed merchandise.
                It aims to elevate the sense of community and pride among students, fostering
                connections while showing support for each organization's initiatives and activities.
            </p>
        </div>
    </div>
</div>


<!-- TEAM -->
<div class="team-container team-header">
    <h1>
        <span>MEET</span> <span>THE TEAM</span>
        <div class="divider"></div>
    </h1>
</div>
<div class="team-section pt_70 pb_70">
    <div class="carousel-scroll parent-team-div">
        <?php
        // Fetch all team members from tbl_team
        $statement = $pdo->prepare("SELECT * FROM tbl_team");
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row) {
        ?>
        <div class="item team-area-holder">
            <div class="team-logo" style="background-image:url('assets/uploads/<?php echo $row['dv_logo']; ?>'); ">
            </div>

            <div class="team-card">
                <div class="photo" style="background-image: url('assets/uploads/<?php echo $row['dv_photo']; ?>');">
                </div>
            </div>

            <div class="team-info">
                <h1><?php echo $row['dv_lname']; ?></h1>
                <p class="team-fname"><?php echo $row['dv_fname']; ?></p>
                <h3><strong><?php echo $row['dv_role']; ?></strong></h3>
                <p class="team-role-info"><?php echo $row['dv_role_info']; ?></p>
            </div>
        </div>
        <?php 
        } 
        ?>
    </div>
</div>

<!-- Footer -->
<?php require_once('footer.php'); ?>