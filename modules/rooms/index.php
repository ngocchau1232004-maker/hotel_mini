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
                <i class="bi bi-door-open"></i>
                Quản lý phòng
            </h4>

            <a href="create.php" class="btn btn-light">
                <i class="bi bi-plus-circle"></i>
                Thêm phòng
            </a>

        </div>

        <!-- BODY -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-secondary">
                        <tr>
                            <th>ID</th>
                            <th>Số phòng</th>
                            <th>Giá</th>
                            <th>Số người</th>
                            <th>Loại phòng</th>
                            <th>Trạng thái</th>
                            <th width="120">Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                            $sql = "SELECT r.*, rt.type_name, rt.price, rt.max_people
                                    FROM rooms r
                                    JOIN room_types rt ON r.room_type_id = rt.room_type_id";

                            $result = mysqli_query($conn, $sql);

                            if (!$result) {
                                die("Query lỗi: " . mysqli_error($conn));
                            }

                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                                    <tr>
                                        <td><?= $row['room_id']; ?></td>
                                        <td><?= $row['room_number']; ?></td>
                                        <td><?= number_format($row['price']); ?> VNĐ</td>
                                        <td><?= $row['max_people']; ?></td>
                                        <td><?= $row['type_name']; ?></td>

                                        <td>
                                            <?php if ($row['status'] == 'Trống') { ?>
                                                <span class="badge bg-success">Trống</span>
                                            <?php } elseif ($row['status'] == 'Đang sử dụng') { ?>
                                                <span class="badge bg-danger">Đang sử dụng</span>
                                            <?php } else { ?>
                                                <span class="badge bg-secondary">
                                                    <?= $row['status']; ?>
                                                </span>
                                            <?php } ?>
                                        </td>

                                        <td>
                                            <a href="edit.php?id=<?= $row['room_id']; ?>"
                                               class="btn btn-warning btn-sm">
                                                Sửa
                                            </a>

                                            <a href="delete.php?id=<?= $row['room_id']; ?>"
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Bạn có chắc muốn xóa phòng này?')">
                                                Xóa
                                            </a>
                                        </td>
                                    </tr>
                        <?php
                                }
                            } else {
                                echo "<tr>
                                        <td colspan='7' class='text-center text-muted'>
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