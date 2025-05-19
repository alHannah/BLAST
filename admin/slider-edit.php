<?php require_once('header.php'); ?>

<?php
if (!isset($_REQUEST['id'])) {
    header('location: logout.php');
    exit;
} else {
    // Check if the ID is valid
    $statement = $pdo->prepare("SELECT * FROM tbl_slider WHERE id=?");
    $statement->execute([$_REQUEST['id']]);
    $total = $statement->rowCount();
    if ($total == 0) {
        header('location: logout.php');
        exit;
    }
}

$error_message = '';
$success_message = '';

if (isset($_POST['form1'])) {
    $valid = 1;

    $path = $_FILES['photo']['name'];
    $path_tmp = $_FILES['photo']['tmp_name'];

    if ($path != '') {
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $valid = 0;
            $error_message .= 'You must upload a jpg, jpeg, png, or gif file.<br>';
        }
    }

    if ($valid == 1) {
        if ($path == '') {
            $statement = $pdo->prepare("UPDATE tbl_slider SET heading=?, content=?, content2=?, button_text=?, button_url=?, position=?, heading_color=?, content_color=?, content2_color=?, button_text_color=?, button_color=? WHERE id=?");
            $statement->execute([
                $_POST['heading'],
                $_POST['content'],
                $_POST['content2'],
                $_POST['button_text'],
                $_POST['button_url'],
                $_POST['position'],
                $_POST['heading_color'],
                $_POST['content_color'],
                $_POST['content2_color'],
                $_POST['button_text_color'],
                $_POST['button_color'],
                $_REQUEST['id']
            ]);
        } else {
            if (!empty($_POST['current_photo'])) {
                unlink('../assets/uploads/' . $_POST['current_photo']);
            }

            $final_name = 'slider-' . $_REQUEST['id'] . '.' . $ext;
            move_uploaded_file($path_tmp, '../assets/uploads/' . $final_name);

            $statement = $pdo->prepare("UPDATE tbl_slider SET photo=?, heading=?, content=?, content2=?, button_text=?, button_url=?, position=?, heading_color=?, content_color=?, content2_color=?, button_text_color=?, button_color=? WHERE id=?");
            $statement->execute([
                $final_name,
                $_POST['heading'],
                $_POST['content'],
                $_POST['content2'],
                $_POST['button_text'],
                $_POST['button_url'],
                $_POST['position'],
                $_POST['heading_color'],
                $_POST['content_color'],
                $_POST['content2_color'],
                $_POST['button_text_color'],
                $_POST['button_color'],
                $_REQUEST['id']
            ]);
        }

        $success_message = 'Slider is updated successfully!';
    }
}

$statement = $pdo->prepare("SELECT * FROM tbl_slider WHERE id=?");
$statement->execute([$_REQUEST['id']]);
$result = $statement->fetch(PDO::FETCH_ASSOC);

$photo = $result['photo'] ?? '';
$heading = $result['heading'] ?? '';
$content = $result['content'] ?? '';
$content2 = $result['content2'] ?? '';
$button_text = $result['button_text'] ?? '';
$button_url = $result['button_url'] ?? '';
$position = $result['position'] ?? '';
$heading_color = $result['heading_color'] ?? '';
$content_color = $result['content_color'] ?? '';
$content2_color = $result['content2_color'] ?? '';
$button_text_color = $result['button_text_color'] ?? '';
$button_color = $result['button_color'] ?? '';
?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Edit Slider</h1>
    </div>
    <div class="content-header-right">
        <a href="slider.php" class="btn btn-primary btn-sm">View All</a>
    </div>
</section>

<section class="content">

    <div class="row">
        <div class="col-md-12">

            <?php if($error_message): ?>
            <div class="callout callout-danger">
                <p>
                    <?php echo $error_message; ?>
                </p>
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
                            <label for="" class="col-sm-2 control-label">Photo <span>*</span></label>
                            <div class="col-sm-9" style="padding-top:5px">
                                <input type="file" name="photo">(Only jpg, jpeg, gif and png are allowed)
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Content </label>
                            <div class="col-sm-6">
                                <textarea class="form-control" name="content2"
                                    style="height:140px;"><?php if(isset($_POST['content2'])){echo $_POST['content2'];} ?></textarea>
                                <input type="color" name="content2_color" value="#000000">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Heading </label>
                            <div class="col-sm-6">
                                <input type="text" autocomplete="off" class="form-control" name="heading"
                                    value="<?php if(isset($_POST['heading'])){echo $_POST['heading'];} ?>">
                                <input type="color" name="heading_color" value="#000000">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Content </label>
                            <div class="col-sm-6">
                                <textarea class="form-control" name="content"
                                    style="height:140px;"><?php if(isset($_POST['content'])){echo $_POST['content'];} ?></textarea>
                                <input type="color" name="content_color" value="#000000">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Button Text </label>
                            <div class="col-sm-6">
                                <input type="text" autocomplete="off" class="form-control" name="button_text"
                                    value="<?php if(isset($_POST['button_text'])){echo $_POST['button_text'];} ?>">
                                <input type="color" name="button_text_color" value="#000000">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Button URL </label>
                            <div class="col-sm-6">
                                <input type="text" autocomplete="off" class="form-control" name="button_url"
                                    value="<?php if(isset($_POST['button_url'])){echo $_POST['button_url'];} ?>">
                                <input type="color" name="button_color" value="#000000">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Position </label>
                            <div class="col-sm-6">
                                <select name="position" class="form-control">
                                    <option value="Left">Left</option>
                                    <option value="Center">Center</option>
                                    <option value="Right">Right</option>
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