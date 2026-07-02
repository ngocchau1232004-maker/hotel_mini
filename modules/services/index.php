<?php
    include '../../includes/auth.php';
    include '../../config/database.php';
    include '../../includes/header.php';

    /** @var mysqli $conn */
?>

<div class="container-fluid">
    <div class="card shadow">

        <!-- HEADER -->
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

            <h4 class="mb-0">
                <i class="bi bi-cup-hot"></i>
                Quản lý dịch vụ
            </h4>

            <a href="create.php" class="btn btn-light">
                <i class="bi bi-plus-circle"></i>
                Thêm dịch vụ
            </a>

        </div>

        <!-- BODY -->
        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-secondary">
                        <tr>
                            <th>ID</th>
                            <th>Tên dịch vụ</th>
                            <th>Giá</th>
                            <th>Mô tả</th>
                            <th width="120">Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                            $sql = "SELECT * FROM services ORDER BY service_id DESC";
                            $result = mysqli_query($conn, $sql);

                            if (!$result) {
                                die("Query lỗi: " . mysqli_error($conn));
                            }

                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                                    <tr>
                                        <td><?= $row['service_id']; ?></td>
                                        <td><?= $row['service_name']; ?></td>
                                        <td><?= number_format($row['price']); ?> VNĐ</td>
                                        <td><?= $row['description']; ?></td>

                                        <td>
                                            <a href="edit.php?id=<?= $row['service_id']; ?>"
                                               class="btn btn-warning btn-sm">
                                                Sửa
                                            </a>

                                            <a href="delete.php?id=<?= $row['service_id']; ?>"
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Bạn có chắc muốn xóa dịch vụ này?')">
                                                Xóa
                                            </a>
                                        </td>
                                    </tr>
                        <?php
                                }
                            } else {
                                echo "<tr>
                                        <td colspan='5' class='text-center text-muted'>
                                            Không có dữ liệu
                                        </td>
                                      </tr>";
                            }
                        ?>
                    </tbody>

                </table>

            </div>

        </div>
    </div>
</div>

<?php include __DIR__.'/../../includes/footer.php'; ?>