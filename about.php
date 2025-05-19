<?php require_once('header.php'); ?>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_page WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
foreach ($result as $row) {
   $about_title = $row['about_title'];
    $about_content = $row['about_content'];
    $about_banner = $row['about_banner'];
}
?>

<!-- ABOUT US -->


<div class="about-banner" style="background-image: url('assets/uploads/<?php echo $about_banner; ?>');">

    <div class="about-title">
        <h1 class="about-title ">about</h1>
    </div>
    <div class="content">
        <p>BuLSU-HC Loyalty Apparel and School Treasures (BLAST) is an e-commerce website
            specially created to showcase the diverse merchandise of every organization at
            Bulacan State University - Hagonoy Campus. This platform serves as a marketplace
            for students, alumni, and supporters who want to support their respective
            organizations by purchasing uniquely designed merchandise.
            It aims to elevate the sense of community and pride among students, fostering
            connections while showing support for each organization's initiatives and activities.
        </p>
    </div>
</div>
<?php require_once('footer.php'); ?>