<?php require_once('header.php'); ?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form1'])) {
    $valid = true;
    $error_message = '';
    $success_message = '';

    // Handle file upload
    if (!empty($_FILES['photo']['name'])) {
        $path = $_FILES['photo']['name'];
        $path_tmp = $_FILES['photo']['tmp_name'];
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($ext, $allowed_extensions)) {
            $valid = false;
            $error_message .= 'Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.<br>';
        }
    } else {
        $valid = false;
        $error_message .= 'You must select a photo to upload.<br>';
    }

    if ($valid) {
        try {
            // Get auto-increment ID for the filename
            $statement = $pdo->prepare("SHOW TABLE STATUS LIKE 'tbl_slider'");
            $statement->execute();
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            $ai_id = $row['Auto_increment'];

            $final_name = 'slider-' . $ai_id . '.' . $ext;
            $upload_path = '../assets/uploads/' . $final_name;

            if (move_uploaded_file($path_tmp, $upload_path)) {
                // Handle default colors if inputs are empty
                $heading_color = $_POST['heading_color'] ?? '#000000';
                $content_color = $_POST['content_color'] ?? '#000000';
                $content2_color = $_POST['content2_color'] ?? '#000000';
                $button_text_color = $_POST['button_text_color'] ?? '#000000';
                $button_color = $_POST['button_color'] ?? '#000000';

                // Insert into the database
                $statement = $pdo->prepare(
                    "INSERT INTO tbl_slider 
                    (photo, heading, content, content2, button_text, button_url, position, heading_color, content_color, content2_color, button_text_color, button_color) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );

                $statement->execute([
                    $final_name,
                    $_POST['heading'],
                    $_POST['content'],
                    $_POST['content2'],
                    $_POST['button_text'],
                    $_POST['button_url'],
                    $_POST['position'],
                    $heading_color,
                    $content_color,
                    $content2_color,
                    $button_text_color,
                    $button_color
                ]);

                $success_message = 'Slider added successfully!';

                // Clear POST data to avoid form resubmission
                $_POST = [];
            } else {
                $error_message .= 'File upload failed. Please try again.<br>';
            }
        } catch (Exception $e) {
            $error_message .= 'An error occurred: ' . $e->getMessage() . '<br>';
        }
    }
}
?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Add Slider</h1>
    </div>
    <div class="content-header-right">
        <a href="slider.php" class="btn btn-primary btn-sm">View All</a>
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
                            <label class="col-sm-2 control-label">Photo <span>*</span></label>
                            <div class="col-sm-9" style="padding-top:5px">
                                <input type="file" name="photo"> (Only JPG, JPEG, PNG, and GIF files are allowed)
                            </div>
                        </div>

                        <!-- Form fields for slider details -->

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Heading</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="heading"
                                    value="<?php echo $_POST['heading'] ?? ''; ?>">
                                <input type="color" name="heading_color" value="#000000">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Content</label>
                            <div class="col-sm-6">
                                <textarea class="form-control" name="content"
                                    style="height:140px;"><?php echo $_POST['content'] ?? ''; ?></textarea>
                                <input type="color" name="content_color" value="#000000">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Content 2</label>
                            <div class="col-sm-6">
                                <textarea class="form-control" name="content2"
                                    style="height:140px;"><?php echo $_POST['content2'] ?? ''; ?></textarea>
                                <input type="color" name="content2_color" value="#000000">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Button Text</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="button_text"
                                    value="<?php echo $_POST['button_text'] ?? ''; ?>">
                                <input type="color" name="button_text_color" value="#000000">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Button URL</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="button_url"
                                    value="<?php echo $_POST['button_url'] ?? ''; ?>">
                                <input type="color" name="button_color" value="#000000">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Position</label>
                            <div class="col-sm-6">
                                <select name="position" class="form-control">
                                    <option value="Left"
                                        <?php echo (($_POST['position'] ?? '') === 'Left') ? 'selected' : ''; ?>>Left
                                    </option>
                                    <option value="Center"
                                        <?php echo (($_POST['position'] ?? '') === 'Center') ? 'selected' : ''; ?>>
                                        Center</option>
                                    <option value="Right"
                                        <?php echo (($_POST['position'] ?? '') === 'Right') ? 'selected' : ''; ?>>Right
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-6 col-sm-offset-2">
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