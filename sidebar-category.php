<h3>
    <?php 
    echo "Product Description"; 
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);  
    ?>
</h3>

<div id="left" class="span3">
    <ul id="menu-group-1" class="nav menu">
        <!-- All Products Section -->
        <li class="cat-level-1 deeper parent">
            <a href="all-product.php">
                <span class="sign"><i class="fa fa-shopping-bag"></i></span>
                <span class="lbl">All Products</span>
            </a>
        </li>

        <!-- Dynamic Categories Section -->
        <?php
        $i = 0;
        $statement = $pdo->prepare("SELECT * FROM tbl_top_category WHERE show_on_menu=1");
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row) {
            $i++;
        ?>
        <li class="cat-level-1 deeper parent">
            <a href="product-category.php?id=<?php echo $row['tcat_id']; ?>&type=top-category">
                <span class="sign"><i class="fa fa-plus"></i></span>
                <span class="lbl"><?php echo $row['tcat_name']; ?></span>
            </a>

            <ul class="children nav-child unstyled small collapse" id="cat-lvl1-id-<?php echo $i; ?>">
                <?php
                $j = 0;
                $statement1 = $pdo->prepare("SELECT * FROM tbl_mid_category WHERE tcat_id=?");
                $statement1->execute(array($row['tcat_id']));
                $result1 = $statement1->fetchAll(PDO::FETCH_ASSOC);
                foreach ($result1 as $row1) {
                    $j++;
                ?>
                <li class="deeper parent">
                    <a href="product-category.php?id=<?php echo $row1['mcat_id']; ?>&type=mid-category">
                        <span class="sign"><i class="fa fa-plus"></i></span>
                        <span class="lbl lbl1"><?php echo $row1['mcat_name']; ?></span>
                    </a>

                    <ul class="children nav-child unstyled small collapse" id="cat-lvl2-id-<?php echo $i.$j; ?>">
                        <?php
                        $k = 0;
                        $statement2 = $pdo->prepare("SELECT * FROM tbl_end_category WHERE mcat_id=?");
                        $statement2->execute(array($row1['mcat_id']));
                        $result2 = $statement2->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($result2 as $row2) {
                            $k++; 
                        ?>
                        <li class="item-<?php echo $i.$j.$k; ?>">
                            <a href="product-category.php?id=<?php echo $row2['ecat_id']; ?>&type=end-category">
                                <span class="lbl lbl1"><?php echo $row2['ecat_name']; ?></span>
                            </a>
                        </li>
                        <?php } ?>
                    </ul>
                </li>
                <?php } ?>
            </ul>
        </li>
        <?php } ?>
    </ul>
</div>