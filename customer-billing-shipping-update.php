<?php
require_once 'header.php';

// Ensure the customer is logged in
if (!isset($_SESSION['customer'])) {
    header('Location: ' . BASE_URL . 'logout.php');
    exit;
}

// Check if the customer is inactive and log them out
$statement = $pdo->prepare(
    "SELECT * FROM tbl_customer WHERE cust_id = ? AND cust_status = ?"
);
$statement->execute([$_SESSION['customer']['cust_id'], 0]);

if ($statement->rowCount() > 0) {
    header('Location: ' . BASE_URL . 'logout.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $requiredFields = [
        'cust_b_name' => 'Billing name is required.',
        'cust_b_phone' => 'Billing phone is required.',
        'cust_b_region' => 'Billing region is required.',
        'cust_b_address' => 'Billing address is required.',
        'cust_s_name' => 'Shipping name is required.',
        'cust_s_phone' => 'Shipping phone is required.',
        'cust_s_region' => 'Shipping region is required.',
        'cust_s_address' => 'Shipping address is required.',
    ];

    $data = [];
    $errors = [];
    
    foreach ($requiredFields as $field => $errorMessage) {
        $data[$field] = strip_tags($_POST[$field] ?? '');
        if (empty($data[$field])) {
            $errors[] = $errorMessage;
        }
    }

    if (empty($errors)) {
        try {
            // Update customer information
            $statement = $pdo->prepare(
                "UPDATE tbl_customer SET 
                    cust_b_name = ?, 
                    cust_b_phone = ?, 
                    cust_b_address = ?, 
                    cust_b_region = ?, 
                    cust_s_name = ?, 
                    cust_s_phone = ?, 
                    cust_s_address = ?, 
                    cust_s_region = ? 
                 WHERE cust_id = ?"
            );

            $statement->execute([
                $data['cust_b_name'],
                $data['cust_b_phone'],
                $data['cust_b_address'],
                $data['cust_b_region'],
                $data['cust_s_name'],
                $data['cust_s_phone'],
                $data['cust_s_address'],
                $data['cust_s_region'],
                $_SESSION['customer']['cust_id']
            ]);

            // Update session with the new customer data
            $_SESSION['customer'] = array_merge($_SESSION['customer'], $data);

            $success_message = "Your information has been updated successfully.";
        } catch (Exception $e) {
            $error_message = "An error occurred while updating your information. Please try again later.";
        }
    } else {
        $error_message = implode('<br>', $errors);
    }
}
?>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?php require_once 'customer-sidebar.php'; ?>
            </div>
            <div class="col-md-12">
                <div class="user-content">
                    <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= $error_message; ?>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success" role="alert">
                        <?= $success_message; ?>
                    </div>
                    <?php endif; ?>

                    <form action="" method="post">
                        <?php $csrf->echoInputField(); ?>
                        <div class="row">
                            <div class="col-md-6">
                                <h3>Billing Information</h3>
                                <div class="form-group">
                                    <label for="cust_b_name">Billing Name</label>
                                    <input type="text" class="form-control" name="cust_b_name"
                                        value="<?= htmlspecialchars($_SESSION['customer']['cust_b_name'] ?? '', ENT_QUOTES); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="cust_b_phone">Billing Phone</label>
                                    <input type="text" class="form-control" name="cust_b_phone"
                                        value="<?= htmlspecialchars($_SESSION['customer']['cust_b_phone'] ?? '', ENT_QUOTES); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="">Billing Region</label>
                                    <select name="cust_b_region" class="form-control">
                                        <?php
                                        $statement = $pdo->prepare("SELECT * FROM tbl_region ORDER BY region_name ASC");
                                        $statement->execute();
                                        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($result as $row) {
                                            ?>
                                        <option value="<?php echo $row['region_id']; ?>"
                                            <?php if($row['region_id'] == $_SESSION['customer']['cust_b_region']) {echo 'selected';} ?>>
                                            <?php echo $row['region_name']; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="cust_b_address">Billing Address</label>
                                    <textarea name="cust_b_address" class="form-control"
                                        rows="4"><?= htmlspecialchars($_SESSION['customer']['cust_b_address'] ?? '', ENT_QUOTES); ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h3>Shipping Information</h3>
                                <div class="form-group">
                                    <label for="cust_s_name">Shipping Name</label>
                                    <input type="text" class="form-control" name="cust_s_name"
                                        value="<?= htmlspecialchars($_SESSION['customer']['cust_s_name'] ?? '', ENT_QUOTES); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="cust_s_phone">Shipping Phone</label>
                                    <input type="text" class="form-control" name="cust_s_phone"
                                        value="<?= htmlspecialchars($_SESSION['customer']['cust_s_phone'] ?? '', ENT_QUOTES); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="">Shipping Region</label>
                                    <select name="cust_s_region" class="form-control">
                                        <?php
                                        $statement = $pdo->prepare("SELECT * FROM tbl_region ORDER BY region_name ASC");
                                        $statement->execute();
                                        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($result as $row) {
                                            ?>
                                        <option value="<?php echo $row['region_id']; ?>"
                                            <?php if($row['region_id'] == $_SESSION['customer']['cust_s_region']) {echo 'selected';} ?>>
                                            <?php echo $row['region_name']; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="cust_s_address">Shipping Address</label>
                                    <textarea name="cust_s_address" class="form-control"
                                        rows="4"><?= htmlspecialchars($_SESSION['customer']['cust_s_address'] ?? '', ENT_QUOTES); ?></textarea>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Information</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>