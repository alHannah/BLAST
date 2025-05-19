<?php require_once('header.php'); 
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>

<?php
if (isset($_POST['form1'])) {
    $valid = 1;
    $error_message = '';

    // PHOTO VALIDATION
    $photo_path = $_FILES['dv_photo']['name'];
    $photo_path_tmp = $_FILES['dv_photo']['tmp_name'];

    if ($photo_path != '') {
        $photo_ext = pathinfo($photo_path, PATHINFO_EXTENSION);
        if (!in_array($photo_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $valid = 0;
            $error_message .= 'You must upload a valid photo file (jpg, jpeg, png, gif)<br>';
        }
    } else {
        $valid = 0;
        $error_message .= 'You must select a photo<br>';
    }

    // LOGO VALIDATION
    $logo_path = $_FILES['dv_logo']['name'];
    $logo_path_tmp = $_FILES['dv_logo']['tmp_name'];

    if ($logo_path != '') {
        $logo_ext = pathinfo($logo_path, PATHINFO_EXTENSION);
        if (!in_array($logo_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $valid = 0;
            $error_message .= 'You must upload a valid logo file (jpg, jpeg, png, gif)<br>';
        }
    } else {
        $valid = 0;
        $error_message .= 'You must select a logo<br>';
    }

    // FIRST NAME VALIDATION
    if (empty($_POST['dv_fname'])) {
        $valid = 0;
        $error_message .= 'First name cannot be empty<br>';
    }

    // LAST NAME VALIDATION
    if (empty($_POST['dv_lname'])) {
        $valid = 0;
        $error_message .= 'Last name cannot be empty<br>';
    }

    // ROLE VALIDATION
    if (empty($_POST['dv_role'])) {
        $valid = 0;
        $error_message .= 'Role cannot be empty<br>';
    }

    // ROLE INFO VALIDATION
    if (empty($_POST['dv_role_info'])) {
        $valid = 0;
        $error_message .= 'Role Information cannot be empty<br>';
    }

    if ($valid == 1) {
        // Auto increment ID for file naming
        $statement = $pdo->prepare("SHOW TABLE STATUS LIKE 'tbl_team'");
        $statement->execute();
        $result = $statement->fetch();
        $ai_id = $result['Auto_increment'];

        // Save photo
        $final_photo_name = 'developer-photo-' . $ai_id . '.' . $photo_ext;
        move_uploaded_file($photo_path_tmp, '../assets/uploads/' . $final_photo_name);

        // Save logo
        $final_logo_name = 'developer-logo-' . $ai_id . '.' . $logo_ext;
        move_uploaded_file($logo_path_tmp, '../assets/uploads/' . $final_logo_name);

        // Insert data into tbl_team
        $statement = $pdo->prepare("INSERT INTO tbl_team (dv_photo, dv_logo, dv_fname, dv_lname, dv_role, dv_role_info) VALUES (?, ?, ?, ?, ?, ?)");
        $statement->execute([
            $final_photo_name,
            $final_logo_name,
            $_POST['dv_fname'],
            $_POST['dv_lname'],
            $_POST['dv_role'],
            $_POST['dv_role_info']
        ]);

        $success_message = 'Developer is added successfully.';
    }
}
?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Add Team Member</h1>
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
                                <input type="text" class="form-control" name="dv_fname">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Last Name <span>*</span></label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="dv_lname">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Role <span>*</span></label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="dv_role">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Role Information </label>
                            <div class="col-sm-6">
                                <textarea class="form-control" name="dv_role_info" style="height:140px;"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Photo <span>*</span></label>
                            <div class="col-sm-9" style="padding-top:5px">
                                <input type="file" name="dv_photo"> (Only jpg, jpeg, gif, and png are allowed)
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Logo <span>*</span></label>
                            <div class="col-sm-9" style="padding-top:5px">
                                <input type="file" name="dv_logo"> (Only jpg, jpeg, gif, and png are allowed)
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