<?php
    include '../../includes/auth.php';
    include '../../config/database.php';
    include '../../includes/header.php';

    /** @var mysqli $conn */
    $sql = "SELECT * FROM room_types ORDER BY room_type_id DESC";
    $result = mysqli_query($conn, $sql);
?>

<div class="container-fluid">
    <div class="card shadow">

        <!-- HEADER -->
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            
            <h4 class="mb-0">
                <i class="bi bi-grid"></i>
                Danh sách loại phòng
            </h4>

            <a href="create.php" class="btn btn-light">
                <i class="bi bi-plus-circle"></i>
                Thêm loại phòng
            </a>

        </div>

        <!-- BODY -->
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-secondary">
                        <tr>
                            <th>ID</th>
                            <th>Tên loại</th>
                            <th>Giá</th>
                            <th>Số người</th>
                            <th>Mô tả</th>
                            <th width="120">Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0) { ?>
                            <?php while($row = mysqli_fetch_assoc($result)) { ?>
                                <tr>
                                    <td><?php echo $row['room_type_id']; ?></td>
                                    <td><?php echo $row['type_name']; ?></td>
                                    <td><?php echo number_format($row['price']); ?> VNĐ</td>
                                    <td><?php echo $row['max_people']; ?></td>
                                    <td><?php echo $row['description']; ?></td>

                                    <td>
                                        <a href="edit.php?id=<?php echo $row['room_type_id']; ?>"
                                           class="btn btn-warning btn-sm">
                                            Sửa
                                        </a>

                                        <a href="delete.php?id=<?php echo $row['room_type_id']; ?>"
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Bạn có chắc muốn xóa loại phòng này?')">
                                            Xóa
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    Không có dữ liệu
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>

                </table>
            </div>

        </div>
    </div>
</div>

<?php include __DIR__.'/../../includes/footer.php'; ?>