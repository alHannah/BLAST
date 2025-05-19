<?php require_once('header.php'); 
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>

<?php
if (isset($_POST['form1'])) {
    $valid = 1;
    $error_message = '';

    // Validate required fields
    if (empty($_POST['tcat_id'])) {
        $valid = 0;
        $error_message .= "Please select a top-level category.<br>";
    }
    if (empty($_POST['mcat_id'])) {
        $valid = 0;
        $error_message .= "Please select a mid-level category.<br>";
    }
    if (empty($_POST['p_name'])) {
        $valid = 0;
        $error_message .= "Product name cannot be empty.<br>";
    }
    if (empty($_POST['p_current_price'])) {
        $valid = 0;
        $error_message .= "Current price cannot be empty.<br>";
    }
    if (empty($_POST['p_qty'])) {
        $valid = 0;
        $error_message .= "Quantity cannot be empty.<br>";
    }

    // Validate featured photo
    $featured_path = $_FILES['p_featured_photo']['name'];
    $featured_tmp = $_FILES['p_featured_photo']['tmp_name'];

    if ($featured_path != '') {
        $featured_ext = pathinfo($featured_path, PATHINFO_EXTENSION);
        if (!in_array($featured_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $valid = 0;
            $error_message .= "The featured photo must be in JPG, JPEG, PNG, or GIF format.<br>";
        }
    } else {
        $valid = 0;
        $error_message .= "Please select a featured photo.<br>";
    }

    // Process form if valid
    if ($valid) {
        // Fetch the next product ID
        $statement = $pdo->prepare("SHOW TABLE STATUS LIKE 'tbl_product'");
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        $ai_id = $result['Auto_increment'];

        // Handle featured photo upload
        $featured_filename = "product-featured-{$ai_id}.{$featured_ext}";
        move_uploaded_file($featured_tmp, "../assets/uploads/{$featured_filename}");

        // Insert product data
        $statement = $pdo->prepare("INSERT INTO tbl_product (
            p_name, p_old_price, p_current_price, p_qty, p_featured_photo, p_description,
            p_short_description, p_feature, p_condition, p_return_policy,
            p_total_view, p_is_featured, p_is_active, ecat_id, tcat_id, mcat_id 
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $statement->execute([
            $_POST['p_name'],
            $_POST['p_old_price'],
            $_POST['p_current_price'],
            $_POST['p_qty'],
            $featured_filename,
            strip_tags($_POST['p_description']),
            strip_tags($_POST['p_short_description']),
            strip_tags($_POST['p_feature']),
            strip_tags($_POST['p_condition']),
            strip_tags($_POST['p_return_policy']),
            0, // p_total_view default
            $_POST['p_is_featured'],
            $_POST['p_is_active'],
            $_POST['ecat_id'],
            $_POST['tcat_id'],
            $_POST['mcat_id']
        ]);
        

        // Handle additional photos upload
        if (!empty($_FILES['photo']['name'][0])) {
            $photos = $_FILES['photo']['name'];
            $photos_tmp = $_FILES['photo']['tmp_name'];

            foreach ($photos as $key => $photo) {
                if ($photo != '') {
                    $photo_ext = pathinfo($photo, PATHINFO_EXTENSION);
                    if (in_array($photo_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                        $photo_filename = "product-photo-{$ai_id}-{$key}.{$photo_ext}";
                        move_uploaded_file($photos_tmp[$key], "../assets/uploads/product_photos/{$photo_filename}");

                        $statement = $pdo->prepare("INSERT INTO tbl_product_photo (photo, p_id) VALUES (?, ?)");
                        $statement->execute([$photo_filename, $ai_id]);
                    }
                }
            }
        }

        // Handle sizes
        if (!empty($_POST['size'])) {
            foreach ($_POST['size'] as $size_id) {
                $statement = $pdo->prepare("INSERT INTO tbl_product_size (size_id, p_id) VALUES (?, ?)");
                $statement->execute([$size_id, $ai_id]);
            }
        }

        // Handle colors
        if (!empty($_POST['color'])) {
            foreach ($_POST['color'] as $color_id) {
                $statement = $pdo->prepare("INSERT INTO tbl_product_color (color_id, p_id) VALUES (?, ?)");
                $statement->execute([$color_id, $ai_id]);
            }
        }

        $success_message = "Product added successfully.";
    }
}
?>


<section class="content-header">
    <div class="content-header-left">
        <h1>Add Product</h1>
    </div>
    <div class="content-header-right">
        <a href="product.php" class="btn btn-primary btn-sm">View All</a>
    </div>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <?php if (!empty($error_message)): ?>
            <div class="callout callout-danger">
                <p><?php echo $error_message; ?></p>
            </div>
            <?php endif; ?>

            <?php if (!empty($success_message)): ?>
            <div class="callout callout-success">
                <p><?php echo $success_message; ?></p>
            </div>
            <?php endif; ?>

            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">

                <div class="box box-info">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="tcat_id" class="col-sm-3 control-label">Top Level Category Name
                                <span>*</span></label>
                            <div class="col-sm-4">
                                <select name="tcat_id" id="tcat_id" class="form-control select2"
                                    onchange="updateMidCategories(this.value)">
                                    <option value="">Select Top Level Category</option>
                                    <?php
            $statement = $pdo->prepare("SELECT * FROM tbl_top_category ORDER BY tcat_name ASC");
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $row) {
                echo '<option value="' . $row['tcat_id'] . '">' . htmlspecialchars($row['tcat_name']) . '</option>';
            }
            ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="mcat_id" class="col-sm-3 control-label">Mid Level Category Name
                                <span>*</span></label>
                            <div class="col-sm-4">
                                <select name="mcat_id" id="mcat_id" class="form-control select2"
                                    onchange="updateEndCategories(this.value)">
                                    <option value="">Select Mid Level Category</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="ecat_id" class="col-sm-3 control-label">End Level Category Name
                                <span>*</span></label>
                            <div class="col-sm-4">
                                <select name="ecat_id" id="ecat_id" class="form-control select2">
                                    <option value="">Select End Level Category</option>
                                </select>
                            </div>
                        </div>


                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Product Name <span>*</span></label>
                        <div class="col-sm-4">
                            <input type="text" name="p_name" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Old Price <br><span
                                style="font-size:10px;font-weight:normal;">(In USD)</span></label>
                        <div class="col-sm-4">
                            <input type="text" name="p_old_price" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Current Price <span>*</span><br><span
                                style="font-size:10px;font-weight:normal;">(In USD)</span></label>
                        <div class="col-sm-4">
                            <input type="text" name="p_current_price" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Quantity <span>*</span></label>
                        <div class="col-sm-4">
                            <input type="text" name="p_qty" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Select Size</label>
                        <div class="col-sm-4">
                            <select name="size[]" class="form-control select2" multiple="multiple">
                                <?php
									$statement = $pdo->prepare("SELECT * FROM tbl_size ORDER BY size_id ASC");
									$statement->execute();
									$result = $statement->fetchAll(PDO::FETCH_ASSOC);			
									foreach ($result as $row) {
										?>
                                <option value="<?php echo $row['size_id']; ?>"><?php echo $row['size_name']; ?>
                                </option>
                                <?php
									}
									?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Select Color</label>
                        <div class="col-sm-4">
                            <select name="color[]" class="form-control select2" multiple="multiple">
                                <?php
									$statement = $pdo->prepare("SELECT * FROM tbl_color ORDER BY color_id ASC");
									$statement->execute();
									$result = $statement->fetchAll(PDO::FETCH_ASSOC);			
									foreach ($result as $row) {
										?>
                                <option value="<?php echo $row['color_id']; ?>"><?php echo $row['color_name']; ?>
                                </option>
                                <?php
									}
									?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Featured Photo <span>*</span></label>
                        <div class="col-sm-4" style="padding-top:4px;">
                            <input type="file" name="p_featured_photo">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Other Photos</label>
                        <div class="col-sm-4" style="padding-top:4px;">
                            <table id="ProductTable" style="width:100%;">
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="upload-btn">
                                                <input type="file" name="photo[]" style="margin-bottom:5px;">
                                            </div>
                                        </td>
                                        <td style="width:28px;"><a href="javascript:void()"
                                                class="Delete btn btn-danger btn-xs">X</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-sm-2">
                            <input type="button" id="btnAddNew" value="Add Item"
                                style="margin-top: 5px;margin-bottom:10px;border:0;color: #fff;font-size: 14px;border-radius:3px;"
                                class="btn btn-warning btn-xs">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Description</label>
                        <div class="col-sm-8">
                            <textarea name="p_description" class="form-control" cols="30" rows="10"
                                id="editor1"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Short Description</label>
                        <div class="col-sm-8">
                            <textarea name="p_short_description" class="form-control" cols="30" rows="10"
                                id="editor2"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Features</label>
                        <div class="col-sm-8">
                            <textarea name="p_feature" class="form-control" cols="30" rows="10" id="editor3"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Conditions</label>
                        <div class="col-sm-8">
                            <textarea name="p_condition" class="form-control" cols="30" rows="10"
                                id="editor4"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Return Policy</label>
                        <div class="col-sm-8">
                            <textarea name="p_return_policy" class="form-control" cols="30" rows="10"
                                id="editor5"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Is Featured?</label>
                        <div class="col-sm-8">
                            <select name="p_is_featured" class="form-control" style="width:auto;">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Is Active?</label>
                        <div class="col-sm-8">
                            <select name="p_is_active" class="form-control" style="width:auto;">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label"></label>
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-success pull-left" name="form1">Add
                                Product</button>
                        </div>
                    </div>
                </div>
        </div>

        </form>


    </div>
    </div>

</section>

<?php require_once('footer.php'); ?>