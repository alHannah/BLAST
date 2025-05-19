<?php
$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row)
{
	$footer_about = $row['footer_about'];
	$contact_email = $row['contact_email'];
	$contact_phone = $row['contact_phone'];
	$contact_address = $row['contact_address'];
	$footer_copyright = $row['footer_copyright'];
	$total_recent_post_footer = $row['total_recent_post_footer'];
    $total_popular_post_footer = $row['total_popular_post_footer'];
    $newsletter_on_off = $row['newsletter_on_off'];
    $before_body = $row['before_body'];
}
?>


<?php
$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
foreach ($result as $row) {
    $stripe_public_key = $row['stripe_public_key'];
    $stripe_secret_key = $row['stripe_secret_key'];
}
?>

<script src="assets/js/jquery-2.2.4.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="https://js.stripe.com/v2/"></script>
<script src="assets/js/megamenu.js"></script>
<script src="assets/js/owl.carousel.min.js"></script>
<script src="assets/js/owl.animate.js"></script>
<script src="assets/js/jquery.bxslider.min.js"></script>
<script src="assets/js/jquery.magnific-popup.min.js"></script>
<script src="assets/js/rating.js"></script>
<script src="assets/js/jquery.touchSwipe.min.js"></script>
<script src="assets/js/bootstrap-touch-slider.js"></script>
<script src="assets/js/select2.full.min.js"></script>
<script src="assets/js/custom.js"></script>
<script>
function confirmDelete() {
    return confirm("Sure you want to delete this data?");
}
$(document).ready(function() {
    advFieldsStatus = $('#advFieldsStatus').val();

    $('#paypal_form').show();
    $('#stripe_form').hide();
    $('#bank_form').hide();

    $('#advFieldsStatus').on('change', function() {
        advFieldsStatus = $('#advFieldsStatus').val();
        if (advFieldsStatus == '') {
            $('#paypal_form').hide();
            $('#stripe_form').hide();
            $('#bank_form').hide();
        } else if (advFieldsStatus == 'PayPal') {
            $('#paypal_form').show();
            $('#stripe_form').hide();
            $('#bank_form').hide();
        } else if (advFieldsStatus == 'Stripe') {
            $('#paypal_form').hide();
            $('#stripe_form').show();
            $('#bank_form').hide();
        } else if (advFieldsStatus == 'Bank Deposit') {
            $('#paypal_form').hide();
            $('#stripe_form').hide();
            $('#bank_form').show();
        }
    });
});
</script>
<?php echo $before_body; ?>
</body>
<footer style="background-color: #556B2F; color: white; padding: 20px 0;">
    <div class="container">
        <div class="row">
            <!-- Contact Information -->
            <div class="col-md-4">
                <h4>BLAST</h4>
                <p><i class="fa fa-phone"></i> <?php echo $contact_phone; ?></p>
                <p><i class="fa fa-envelope"></i> <?php echo $contact_email; ?></p>
                <p><i class="fa fa-map-marker"></i> <?php echo $contact_address; ?></p>
            </div>

            <!-- Company Links -->
            <div class="col-md-2">
                <h4>Company</h4>
                <ul style="list-style: none; padding: 0;">
                    <li><a href="#" style="color: white;">Careers</a></li>
                    <li><a href="#" style="color: white;">Cookie Notice</a></li>
                    <li><a href="#" style="color: white;">Privacy Policy</a></li>
                    <li><a href="#" style="color: white;">Terms of Use</a></li>
                </ul>
            </div>

            <!-- Quick Links -->
            <div class="col-md-2">
                <h4>Quick Links</h4>
                <ul style="list-style: none; padding: 0;">
                    <li><a href="#" style="color: white;">About Us</a></li>
                    <li><a href="#" style="color: white;">Contact Us</a></li>
                    <li><a href="#" style="color: white;">Meet the Team</a></li>
                    <li><a href="#" style="color: white;">Products</a></li>
                </ul>
            </div>

            <!-- Get Help -->
            <div class="col-md-2">
                <h4>Get Help</h4>
                <ul style="list-style: none; padding: 0;">
                    <li><a href="#" style="color: white;">FAQs</a></li>
                    <li><a href="#" style="color: white;">Return</a></li>
                    <li><a href="#" style="color: white;">Shipping</a></li>
                    <li><a href="#" style="color: white;">Payment Options</a></li>
                </ul>
            </div>

            <!-- Social Media Icons -->
            <div class="col-md-2 text-center">
                <h4>Follow Us</h4>
                <a href="#" style="color: white; margin: 0 5px;"><i class="fa fa-facebook"></i></a>
                <a href="#" style="color: white; margin: 0 5px;"><i class="fa fa-twitter"></i></a>
                <a href="#" style="color: white; margin: 0 5px;"><i class="fa fa-instagram"></i></a>
                <a href="#" style="color: white; margin: 0 5px;"><i class="fa fa-linkedin"></i></a>
                <a href="#" style="color: white; margin: 0 5px;"><i class="fa fa-whatsapp"></i></a>
                <li><a href="admin/index.php" style=" color: white;">LOGIN AS ADMIN</a></li>
            </div>
        </div>

        <hr style="border-color: white; margin: 20px 0;">
        <div class="row">
            <div class="col-md-12 text-center">
                <p><?php echo $footer_copyright; ?></p>
            </div>
        </div>
    </div>
</footer>

</html>