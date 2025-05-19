<?php require_once('header.php'); ?>

<section class="content-header">
    <div class="content-header-left">
        <h1>View Top Level Categories</h1>
    </div>
    <div class="content-header-right">
        <a href="top-category-add.php" class="btn btn-primary btn-sm">Add New</a>
    </div>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-body table-responsive">
                    <table id="example1" class="table table-bordered table-hover table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th width="10">#</th>
                                <th width="100">Top Category Photo</th>
                                <th width="100">Top Category Logo</th>
                                <th>Top Category Name</th>
                                <th>Show on Menu?</th>
                                <th width="80">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;
                            $statement = $pdo->prepare("SELECT
                                t1.tcat_id,
                                t1.tcat_name,
                                t1.tcat_photo,
                                t1.tcat_logo,
                                t1.show_on_menu
                                FROM tbl_top_category t1
                                ORDER BY t1.tcat_id DESC");
                            $statement->execute();
                            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($result as $row) {
                                $i++;
                                ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td style="width:82px;">
                                    <img src="../assets/uploads/<?php echo htmlspecialchars($row['tcat_photo']); ?>"
                                        alt="<?php echo htmlspecialchars($row['tcat_name']); ?>"
                                        style="width:80px; height: auto;">
                                </td>
                                <td style="width:82px;">
                                    <img src="../assets/uploads/<?php echo htmlspecialchars($row['tcat_logo']); ?>"
                                        alt="<?php echo htmlspecialchars($row['tcat_name']); ?> Logo"
                                        style="width:80px; height: auto;">
                                </td>
                                <td><?php echo htmlspecialchars($row['tcat_name']); ?></td>
                                <td>
                                    <?php 
                                        echo $row['show_on_menu'] == 1 ? '<span class="badge badge-success" style="background-color:green;">Yes</span>' : '<span class="badge badge-danger" style="background-color:red;">No</span>';
                                        ?>
                                </td>
                                <td>
                                    <a href="top-category-edit.php?id=<?php echo $row['tcat_id']; ?>"
                                        class="btn btn-primary btn-xs">Edit</a>
                                    <a href="#" class="btn btn-danger btn-xs"
                                        data-href="top-category-delete.php?id=<?php echo $row['tcat_id']; ?>"
                                        data-toggle="modal" data-target="#confirm-delete">Delete</a>
                                </td>
                            </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure want to delete this item?</p>
                <p style="color:red;">Be careful! All products, mid-level categories, and end-level categories under
                    this top-level category will be deleted from all the tables like order table, payment table, size
                    table, color table, rating table, etc.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger btn-ok">Delete</a>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>