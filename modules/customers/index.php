<?php
    include '../../includes/auth.php';
    include '../../config/database.php';
    include '../../includes/header.php';

    $sql = "SELECT * FROM customers ORDER BY customer_id DESC";
    $result = mysqli_query($conn, $sql);
?>

<div class="container-fluid">
    <div class="card shadow">

        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                <i class="bi bi-people"></i>
                Danh sách khách hàng
            </h4>

            <a href="create.php" class="btn btn-light">
                <i class="bi bi-plus-circle"></i>
                Thêm khách hàng
            </a>
        </div>

        <div class="card-body">
            <?php include '../../includes/alert.php'; ?>

            <!-- BẮT BUỘC để có cuộn ngang -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">

                    <thead>
                        <tr>
                            <th>Mã KH</th>
                            <th>Họ tên</th>
                            <th>Giới tính</th>
                            <th>SĐT</th>
                            <th>Email</th>
                            <th>CCCD</th>
                            <th>Địa chỉ</th>
                            <th>Số phòng</th>
                            <th>Ngày tạo</th>
                            <th width="160">Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)) { ?>

                            <tr>
                                <td><?= $row['customer_id']; ?></td>
                                <td><?= $row['full_name']; ?></td>
                                <td><?= $row['gender']; ?></td>
                                <td><?= $row['phone']; ?></td>
                                <td><?= $row['email']; ?></td>
                                <td><?= $row['id_card']; ?></td>
                                <td><?= $row['address']; ?></td>

                                <!-- chưa có join nên để tạm -->
                                <td>---</td>

                                <td><?= $row['created_at']; ?></td>

                                <td>
                                    <a href="edit.php?id=<?= $row['customer_id']; ?>"
                                       class="btn btn-warning btn-sm">
                                        Sửa
                                    </a>

                                    <a href="delete.php?id=<?= $row['customer_id']; ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Bạn có chắc muốn xóa?')">
                                        Xóa
                                    </a>
                                </td>
                            </tr>

                        <?php } ?>
                    </tbody>

                </table>

            </div>
        </div>

    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>