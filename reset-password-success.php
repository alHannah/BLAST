<?php require_once('header.php'); ?>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);                           
foreach ($result as $row) {
    $banner_forget_password = $row['banner_forget_password'];
}
?>

<div class="page-banner"
    style="background-color:#444;background-image: url(assets/uploads/<?php echo $banner_forget_password; ?>);">
    <div class="inner">
        <h1>Change Password</h1>
    </div>
</div>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="user-content">Password is reset successfully. You can now login.<br><br>
                    <a href="<?php echo BASE_URL; ?>login.php" style="color:#e4144d;font-weight:bold;">Click here to
                        login</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>