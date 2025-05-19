<?php require_once('header.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);          // Inspect fetched results
?>



<!-- TEAM -->
<div class="team-container team-header">
    <h1>
        <span>MEET</span> <span>THE TEAM</span>
        <div class="divider"></div>
    </h1>
</div>
<div class="team-section pt_70 pb_70">
    <div class="carousel-scroll parent-team-div">
        <?php
        // Fetch all team members from tbl_team
        $statement = $pdo->prepare("SELECT * FROM tbl_team");
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row) {
        ?>
        <div class="item team-area-holder">
            <div class="team-logo" style="background-image:url('assets/uploads/<?php echo $row['dv_logo']; ?>'); ">
            </div>

            <div class="team-card">
                <div class="photo" style="background-image: url('assets/uploads/<?php echo $row['dv_photo']; ?>');">
                </div>
            </div>

            <div class="team-info">
                <h1><?php echo $row['dv_lname']; ?></h1>
                <p class="team-fname"><?php echo $row['dv_fname']; ?></p>
                <h3><strong><?php echo $row['dv_role']; ?></strong></h3>
                <p class="team-role-info"><?php echo $row['dv_role_info']; ?></p>
            </div>
        </div>
        <?php 
        } 
        ?>
    </div>
</div>


<?php require_once('footer.php'); ?>