<?php require_once('header.php'); ?>

<section class="content-header">
    <div class="content-header-left">
        <h1>View Developer Team</h1>
    </div>
    <div class="content-header-right">
        <a href="team-add.php" class="btn btn-primary btn-sm">Add New</a>
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
                                <th width="100">Developer Photo</th>
                                <th width="100">Developer Logo</th>
                                <th>Developer First Name</th>
                                <th>Developer Last Name</th>
                                <th>Developer Role</th>
                                <th width="100">Developer Role Information</th>
                                <th width="80">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;
                            $statement = $pdo->prepare("SELECT 
                                dv_id, 
                                dv_photo, 
                                dv_logo, 
                                dv_fname, 
                                dv_lname, 
                                dv_role, 
                                dv_role_info 
                                FROM tbl_team 
                                ORDER BY dv_lname DESC");
                            $statement->execute();
                            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($result as $row) {
                                $i++;
                                ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td style="width:82px;">
                                    <img src="../assets/uploads/<?php echo htmlspecialchars($row['dv_photo']); ?>"
                                        alt="Developer Photo" style="width:80px; height:auto;">
                                </td>
                                <td style="width:82px;">
                                    <img src="../assets/uploads/<?php echo htmlspecialchars($row['dv_logo']); ?>"
                                        alt="Developer Logo" style="width:80px; height:auto;">
                                </td>
                                <td><?php echo htmlspecialchars($row['dv_fname']); ?></td>
                                <td><?php echo htmlspecialchars($row['dv_lname']); ?></td>
                                <td><?php echo htmlspecialchars($row['dv_role']); ?></td>
                                <td><?php echo htmlspecialchars($row['dv_role_info']); ?></td>
                                <td>
                                    <a href="team-edit.php?id=<?php echo $row['dv_id']; ?>"
                                        class="btn btn-primary btn-xs">Edit</a>
                                    <a href="#" class="btn btn-danger btn-xs"
                                        data-href="team-delete.php?id=<?php echo $row['dv_id']; ?>" data-toggle="modal"
                                        data-target="#confirm-delete">Delete</a>
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