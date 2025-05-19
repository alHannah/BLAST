<?php require_once('header.php'); 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);   
?>

<!-- BANNER PAGE COVER IMAGE -->
<?php
$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
$statement->execute();
$result = $statement->fetch(PDO::FETCH_ASSOC);
$banner_product_category = $result['banner_product_category'];
?>

<?php
// Fetch Categories
$top_categories = $pdo->query("SELECT * FROM tbl_top_category")->fetchAll(PDO::FETCH_ASSOC);
$mid_categories = $pdo->query("SELECT * FROM tbl_mid_category")->fetchAll(PDO::FETCH_ASSOC);
$end_categories = $pdo->query("SELECT * FROM tbl_end_category")->fetchAll(PDO::FETCH_ASSOC);

// Initialize Final eCat IDs
$final_ecat_ids = [];
$title = "All Products"; // Default title

if (isset($_REQUEST['type']) && isset($_REQUEST['id'])) {
    $category_type = $_REQUEST['type'];
    $category_id = (int)$_REQUEST['id'];

    if ($category_type === 'top-category') {
        // Check if top-category ID exists
        foreach ($top_categories as $top) {
            if ($top['tcat_id'] == $category_id) {
                $title = $top['tcat_name'];
                // Get all related mid-category IDs
                $mid_ids = array_column(array_filter($mid_categories, fn($mid) => $mid['tcat_id'] == $category_id), 'mcat_id');
                // Get all related end-category IDs
                $final_ecat_ids = array_column(array_filter($end_categories, fn($end) => in_array($end['mcat_id'], $mid_ids)), 'ecat_id');
                break;
            }
        }
    } elseif ($category_type === 'mid-category') {
        // Check if mid-category ID exists
        foreach ($mid_categories as $mid) {
            if ($mid['mcat_id'] == $category_id) {
                $title = $mid['mcat_name'];
                // Get all related end-category IDs
                $final_ecat_ids = array_column(array_filter($end_categories, fn($end) => $end['mcat_id'] == $category_id), 'ecat_id');
                break;
            }
        }
    } elseif ($category_type === 'end-category') {
        // Check if end-category ID exists
        foreach ($end_categories as $end) {
            if ($end['ecat_id'] == $category_id) {
                $title = $end['ecat_name'];
                $final_ecat_ids = [$category_id]; // Only this end-category
                break;
            }
        }
    }

    // Redirect if invalid category
    if (empty($title)) {
        header('Location: index.php');
        exit;
    }
}
?>

<div class="page-banner" style="background-image: url(assets/uploads/<?php echo $banner_product_category; ?>)">
    <div class="inner">
        <h1>Category: <?php echo htmlspecialchars($title); ?></h1>
    </div>
</div>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <?php require_once('sidebar-category.php'); ?>
            </div>
            <div class="col-md-9">

                <h3><?php echo htmlspecialchars($title); ?></h3>
                <div class="product product-cat">

                    <div class="row">
                        <!-- Display Products -->
                        <?php
                        $query = "SELECT * FROM tbl_product";
                        $params = [];

                        if (!empty($final_ecat_ids)) {
                            // Filter products by final eCat IDs
                            $placeholders = implode(',', array_fill(0, count($final_ecat_ids), '?'));
                            $query .= " WHERE ecat_id IN ($placeholders)";
                            $params = $final_ecat_ids;
                        }

                        $statement = $pdo->prepare($query);
                        $statement->execute($params);
                        $products = $statement->fetchAll(PDO::FETCH_ASSOC);

                        if (empty($products)) {
                            echo '<div class="pl_15">No Product Found</div>';
                        } else {
                            foreach ($products as $row) {
                        ?>
                        <div class="col-md-4 item item-product-cat">
                            <div class="inner">
                                <div class="thumb">
                                    <div class="photo"
                                        style="background-image:url(assets/uploads/<?php echo $row['p_featured_photo']; ?>);">
                                    </div>
                                    <div class="overlay"></div>
                                </div>
                                <div class="text">
                                    <h3><a
                                            href="product.php?id=<?php echo $row['p_id']; ?>"><?php echo $row['p_name']; ?></a>
                                    </h3>
                                    <h4>
                                        ₱<?php echo $row['p_current_price']; ?>
                                        <?php if ($row['p_old_price'] != ''): ?>
                                        <del>₱<?php echo $row['p_old_price']; ?></del>
                                        <?php endif; ?>
                                    </h4>
                                    <div class="rating">
                                        <?php
                                        $t_rating = 0;
                                        $statement1 = $pdo->prepare("SELECT * FROM tbl_rating WHERE p_id=?");
                                        $statement1->execute([$row['p_id']]);
                                        $tot_rating = $statement1->rowCount();
                                        $avg_rating = $tot_rating > 0 ? array_sum(array_column($statement1->fetchAll(PDO::FETCH_ASSOC), 'rating')) / $tot_rating : 0;

                                        for ($i = 1; $i <= 5; $i++) {
                                            echo $i <= $avg_rating ? '<i class="fa fa-star"></i>' : '<i class="fa fa-star-o"></i>';
                                        }
                                        ?>
                                    </div>
                                    <?php if ($row['p_qty'] == 0): ?>
                                    <div class="out-of-stock">
                                        <div class="inner">Out Of Stock</div>
                                    </div>
                                    <?php else: ?>
                                    <p><a href="product.php?id=<?php echo $row['p_id']; ?>"><i
                                                class="fa fa-shopping-cart"></i> Add to Cart</a></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php
                            }
                        }
                        ?>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>