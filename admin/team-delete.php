<?php require_once('header.php'); ?>

<?php
if (!isset($_REQUEST['id'])) {
    header('location: logout.php');
    exit;
} else {
    // Check if the ID exists
    $statement = $pdo->prepare("SELECT * FROM tbl_team WHERE dv_id=?");
    $statement->execute(array($_REQUEST['id']));
    $total = $statement->rowCount();
    if ($total == 0) {
        header('location: logout.php');
        exit;
    } else {
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row) {
            $dv_photo = $row['dv_photo'];
            $dv_logo = $row['dv_logo'];
        }
    }

    // Delete photo and logo files if they exist
    if ($dv_photo != '' && file_exists('../assets/uploads/' . $dv_photo)) {
        unlink('../assets/uploads/' . $dv_photo);
    }
    if ($dv_logo != '' && file_exists('../assets/uploads/' . $dv_logo)) {
        unlink('../assets/uploads/' . $dv_logo);
    }

    // Delete record from database
    $statement = $pdo->prepare("DELETE FROM tbl_team WHERE dv_id=?");
    $statement->execute(array($_REQUEST['id']));

    $success_message = 'Team member has been deleted successfully.';
}
?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Delete Team Member</h1>
    </div>
    <div class="content-header-right">
        <a href="team.php" class="btn btn-primary btn-sm">View All</a>
    </div>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <?php if ($success_message): ?>
            <div class="callout callout-success">
                <p><?php echo $success_message; ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once('footer.php'); ?>