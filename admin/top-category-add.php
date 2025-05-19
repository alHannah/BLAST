<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form1'])) {
	$valid = 1;
	// PHOTO VALIDATION
	$path = $_FILES['tcat_photo']['name'];
    $path_tmp = $_FILES['tcat_photo']['tmp_name'];

    if($path!='') {
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        if(!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $valid = 0;
            $error_message .= 'You must upload a valid photo file (jpg, jpeg, png, gif)<br>';
        }
    } else {
    	$valid = 0;
        $error_message .= 'You must select a photo<br>';
    }

	// LOGO VALIDATION
	$logo_path = $_FILES['tcat_logo']['name'];
    $logo_path_tmp = $_FILES['tcat_logo']['tmp_name'];

    if($logo_path != '') {
        $logo_ext = pathinfo($logo_path, PATHINFO_EXTENSION);
        if(!in_array($logo_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $valid = 0;
            $error_message .= 'You must upload a valid logo file (jpg, jpeg, png, gif)<br>';
        }
    } else {
    	$valid = 0;
        $error_message .= 'You must select a logo<br>';
    }

    if(empty($_POST['tcat_name'])) {
        $valid = 0;
        $error_message .= "Top Category Name cannot be empty<br>";
    } else {
    	// Duplicate Category checking
    	$statement = $pdo->prepare("SELECT * FROM tbl_top_category WHERE tcat_name=?");
    	$statement->execute(array($_POST['tcat_name']));
    	if($statement->rowCount() > 0) {
    		$valid = 0;
        	$error_message .= "Top Category Name already exists<br>";
    	}
    }

    if($valid == 1) {
		// Auto increment ID for file naming
		$statement = $pdo->prepare("SHOW TABLE STATUS LIKE 'tbl_top_category'");
		$statement->execute();
		$result = $statement->fetch();
		$ai_id = $result['Auto_increment'];

		// Save photo
		$final_photo_name = 'tcat-photo-' . $ai_id . '.' . $ext;
        move_uploaded_file($path_tmp, '../assets/uploads/' . $final_photo_name);

		// Save logo
		$final_logo_name = 'tcat-logo-' . $ai_id . '.' . $logo_ext;
        move_uploaded_file($logo_path_tmp, '../assets/uploads/' . $final_logo_name);

		// Insert data into tbl_top_category
		$statement = $pdo->prepare("INSERT INTO tbl_top_category (tcat_name, tcat_photo, tcat_logo, show_on_menu) VALUES (?, ?, ?, ?)");
		$statement->execute([
			$_POST['tcat_name'],
			$final_photo_name,
			$final_logo_name,
			$_POST['show_on_menu']
		]);

    	$success_message = 'Top Category is added successfully.';
    }
}
?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Add Top Level Category</h1>
    </div>
    <div class="content-header-right">
        <a href="top-category.php" class="btn btn-primary btn-sm">View All</a>
    </div>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <?php if($error_message): ?>
            <div class="callout callout-danger">
                <p><?php echo $error_message; ?></p>
            </div>
            <?php endif; ?>

            <?php if($success_message): ?>
            <div class="callout callout-success">
                <p><?php echo $success_message; ?></p>
            </div>
            <?php endif; ?>

            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                <div class="box box-info">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Top Category Name <span>*</span></label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="tcat_name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Photo <span>*</span></label>
                            <div class="col-sm-9" style="padding-top:5px">
                                <input type="file" name="tcat_photo"> (Only jpg, jpeg, gif, and png are allowed)
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Logo <span>*</span></label>
                            <div class="col-sm-9" style="padding-top:5px">
                                <input type="file" name="tcat_logo"> (Only jpg, jpeg, gif, and png are allowed)
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Show on Menu? <span>*</span></label>
                            <div class="col-sm-4">
                                <select name="show_on_menu" class="form-control" style="width:auto;">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"></label>
                            <div class="col-sm-6">
                                <button type="submit" class="btn btn-success pull-left" name="form1">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<?php require_once('footer.php'); ?>