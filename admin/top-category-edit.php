<?php require_once('header.php'); ?>

<?php
if (isset($_POST['form1'])) {
    $valid = 1;

    if (empty($_POST['tcat_name'])) {
        $valid = 0;
        $error_message .= "Top Category Name cannot be empty<br>";
    } else {
        // Duplicate Top Category checking
        $statement = $pdo->prepare("SELECT * FROM tbl_top_category WHERE tcat_id=?");
        $statement->execute(array($_REQUEST['id']));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row) {
            $current_tcat_name = $row['tcat_name'];
        }

        $statement = $pdo->prepare("SELECT * FROM tbl_top_category WHERE tcat_name=? AND tcat_name!=?");
        $statement->execute(array($_POST['tcat_name'], $current_tcat_name));
        $total = $statement->rowCount();
        if ($total) {
            $valid = 0;
            $error_message .= 'Top Category name already exists<br>';
        }
    }

    $photo_path = '';
    $logo_path = '';

    if (!empty($_FILES['tcat_photo']['name'])) {
        $photo_ext = pathinfo($_FILES['tcat_photo']['name'], PATHINFO_EXTENSION);
        $allowed_exts = ['jpg', 'jpeg', 'gif', 'png'];

        if (!in_array($photo_ext, $allowed_exts)) {
            $valid = 0;
            $error_message .= "Photo must be in jpg, jpeg, gif, or png format<br>";
        } else {
            $photo_path = 'uploads/' . uniqid() . '.' . $photo_ext;
            move_uploaded_file($_FILES['tcat_photo']['tmp_name'], $photo_path);
        }
    }

    if (!empty($_FILES['tcat_logo']['name'])) {
        $logo_ext = pathinfo($_FILES['tcat_logo']['name'], PATHINFO_EXTENSION);
        if (!in_array($logo_ext, $allowed_exts)) {
            $valid = 0;
            $error_message .= "Logo must be in jpg, jpeg, gif, or png format<br>";
        } else {
            $logo_path = 'uploads/' . uniqid() . '.' . $logo_ext;
            move_uploaded_file($_FILES['tcat_logo']['tmp_name'], $logo_path);
        }
    }

    if ($valid == 1) {
        // Prepare SQL update query
        $sql = "UPDATE tbl_top_category SET tcat_name=?, show_on_menu=?";

        $params = [$_POST['tcat_name'], $_POST['show_on_menu'], $_REQUEST['id']];

        if ($photo_path !== '') {
            $sql .= ", tcat_photo=?";
            $params[] = $photo_path;
        }

        if ($logo_path !== '') {
            $sql .= ", tcat_logo=?";
            $params[] = $logo_path;
        }

        $sql .= " WHERE tcat_id=?";

        $statement = $pdo->prepare($sql);
        $statement->execute($params);

        $success_message = 'Top Category is updated successfully.';
    }
}

if (!isset($_REQUEST['id'])) {
    header('location: logout.php');
    exit;
} else {
    $statement = $pdo->prepare("SELECT * FROM tbl_top_category WHERE tcat_id=?");
    $statement->execute(array($_REQUEST['id']));
    $total = $statement->rowCount();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    if ($total == 0) {
        header('location: logout.php');
        exit;
    }
}

foreach ($result as $row) {
    $tcat_name = $row['tcat_name'];
    $show_on_menu = $row['show_on_menu'];
    $tcat_photo = $row['tcat_photo'];
    $tcat_logo = $row['tcat_logo'];
}
?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Edit Top Level Category</h1>
    </div>
    <div class="content-header-right">
        <a href="top-category.php" class="btn btn-primary btn-sm">View All</a>
    </div>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <?php if ($error_message): ?>
            <div class="callout callout-danger">
                <p><?php echo $error_message; ?></p>
            </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
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
                                <input type="text" class="form-control" name="tcat_name"
                                    value="<?php echo $tcat_name; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Photo <span>*</span></label>
                            <div class="col-sm-9" style="padding-top:5px">
                                <input type="file" name="tcat_photo">
                                <?php if ($tcat_photo): ?>
                                <img src="<?php echo $tcat_photo; ?>" alt="" style="width:150px;">
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Logo <span>*</span></label>
                            <div class="col-sm-9" style="padding-top:5px">
                                <input type="file" name="tcat_logo">
                                <?php if ($tcat_logo): ?>
                                <img src="<?php echo $tcat_logo; ?>" alt="" style="width:150px;">
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Show on Menu? <span>*</span></label>
                            <div class="col-sm-4">
                                <select name="show_on_menu" class="form-control" style="width:auto;">
                                    <option value="0" <?php if ($show_on_menu == 0) echo 'selected'; ?>>No</option>
                                    <option value="1" <?php if ($show_on_menu == 1) echo 'selected'; ?>>Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"></label>
                            <div class="col-sm-6">
                                <button type="submit" class="btn btn-success pull-left" name="form1">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<?php require_once('footer.php'); ?>