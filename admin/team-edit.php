<?php require_once('header.php'); ?>

<?php
if (isset($_POST['form1'])) {
    $valid = 1;

    if (empty($_POST['dv_fname']) || empty($_POST['dv_lname']) || empty($_POST['dv_role'])) {
        $valid = 0;
        $error_message .= "All fields are required.<br>";
    }

    $photo_path = '';
    $logo_path = '';

    if (!empty($_FILES['dv_photo']['name'])) {
        $photo_ext = pathinfo($_FILES['dv_photo']['name'], PATHINFO_EXTENSION);
        $allowed_exts = ['jpg', 'jpeg', 'gif', 'png'];

        if (!in_array($photo_ext, $allowed_exts)) {
            $valid = 0;
            $error_message .= "Photo must be in jpg, jpeg, gif, or png format<br>";
        } else {
            $photo_path = 'uploads/' . uniqid() . '.' . $photo_ext;
            move_uploaded_file($_FILES['dv_photo']['tmp_name'], $photo_path);
        }
    }

    if (!empty($_FILES['dv_logo']['name'])) {
        $logo_ext = pathinfo($_FILES['dv_logo']['name'], PATHINFO_EXTENSION);
        if (!in_array($logo_ext, $allowed_exts)) {
            $valid = 0;
            $error_message .= "Logo must be in jpg, jpeg, gif, or png format<br>";
        } else {
            $logo_path = 'uploads/' . uniqid() . '.' . $logo_ext;
            move_uploaded_file($_FILES['dv_logo']['tmp_name'], $logo_path);
        }
    }

    if ($valid == 1) {
        $sql = "UPDATE tbl_team SET dv_fname=?, dv_lname=?, dv_role=?";
        $params = [$_POST['dv_fname'], $_POST['dv_lname'], $_POST['dv_role']];

        if ($photo_path !== '') {
            $sql .= ", dv_photo=?";
            $params[] = $photo_path;
        }

        if ($logo_path !== '') {
            $sql .= ", dv_logo=?";
            $params[] = $logo_path;
        }

        $sql .= " WHERE dv_id=?";
        $params[] = $_REQUEST['id'];

        $statement = $pdo->prepare($sql);
        $statement->execute($params);

        $success_message = 'Team member is updated successfully.';
    }
}

if (!isset($_REQUEST['id'])) {
    header('location: logout.php');
    exit;
} else {
    $statement = $pdo->prepare("SELECT * FROM tbl_team WHERE dv_id=?");
    $statement->execute(array($_REQUEST['id']));
    $total = $statement->rowCount();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    if ($total == 0) {
        header('location: logout.php');
        exit;
    }
}

foreach ($result as $row) {
    $dv_fname = $row['dv_fname'];
    $dv_lname = $row['dv_lname'];
    $dv_role = $row['dv_role'];
    $dv_role_info = $row['dv_role_info'];
    $dv_photo = $row['dv_photo'];
    $dv_logo = $row['dv_logo'];
}
?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Edit Team Member</h1>
    </div>
    <div class="content-header-right">
        <a href="team.php" class="btn btn-primary btn-sm">View All</a>
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
                            <label for="" class="col-sm-2 control-label">First Name <span>*</span></label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="dv_fname"
                                    value="<?php echo $dv_fname; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Last Name <span>*</span></label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="dv_lname"
                                    value="<?php echo $dv_lname; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Role <span>*</span></label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="dv_role" value="<?php echo $dv_role; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Role Information </label>
                            <div class="col-sm-6">
                                <textarea class="form-control" name="dv_role_info" style="height:140px;"
                                    value="<?php echo $dv_role_info; ?>"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Photo</label>
                            <div class="col-sm-9" style="padding-top:5px">
                                <input type="file" name="dv_photo">
                                <?php if ($dv_photo): ?>
                                <img src="<?php echo $dv_photo; ?>" alt="" style="width:150px; margin-top:10px;">
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Logo</label>
                            <div class="col-sm-9" style="padding-top:5px">
                                <input type="file" name="dv_logo">
                                <?php if ($dv_logo): ?>
                                <img src="<?php echo $dv_logo; ?>" alt="" style="width:150px; margin-top:10px;">
                                <?php endif; ?>
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