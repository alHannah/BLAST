<?php
require_once 'inc/config.php';

if (isset($_GET['tcat_id'])) {
    $tcat_id = intval($_GET['tcat_id']);
    $statement = $pdo->prepare("SELECT * FROM tbl_mid_category WHERE tcat_id = ? ORDER BY mcat_name ASC");
    $statement->execute([$tcat_id]);
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

    if ($result) {
        echo '<option value="">Select Mid Level Category</option>';
        foreach ($result as $row) {
            echo '<option value="' . htmlspecialchars($row['mcat_id']) . '">' . htmlspecialchars($row['mcat_name']) . '</option>';
        }
    } else {
        echo '<option value="">No mid categories found</option>';
    }
} elseif (isset($_GET['mcat_id'])) {
    $mcat_id = intval($_GET['mcat_id']);
    $statement = $pdo->prepare("SELECT * FROM tbl_end_category WHERE mcat_id = ? ORDER BY ecat_name ASC");
    $statement->execute([$mcat_id]);
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

    if ($result) {
        echo '<option value="">Select End Level Category</option>';
        foreach ($result as $row) {
            echo '<option value="' . htmlspecialchars($row['ecat_id']) . '">' . htmlspecialchars($row['ecat_name']) . '</option>';
        }
    } else {
        echo '<option value="">No end categories found</option>';
    }
} else {
    echo '<option value="">Invalid request</option>';
}
?>