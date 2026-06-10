<?php
    include '../../includes/auth.php';
    include '../../config/database.php';

    /** @var mysqli $conn */
    $conn = $conn;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Phòng</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet">
</head>

<body>

    <div class="container mt-4">
        <a href="../../dashboard.php"class="btn btn-outline-primary mb-3">
            <i class="bi bi-arrow-left"></i>
            Quay lại danh sách
        </a>
        
        <h2>Quản lý phòng</h2>

        <a href="create.php"
            class="btn btn-success mb-3">
            Thêm phòng
        </a>

        <table class="table table-bordered">
            <tr>
                <th>ID</th>
                <th>Số phòng</th>
                <th>Giá</th>
                <th>Số người</th>
                <th>Mô tả</th>
                <th>Thao tác</th>
            </tr>

            <?php
                $sql = "SELECT r.*, rt.type_name, rt.price, rt.max_people FROM rooms r JOIN room_types rt ON r.room_type_id = rt.room_type_id";
                $result = mysqli_query($conn,$sql);

                while($row = mysqli_fetch_assoc($result)){
                ?>
                    <tr>
                        <td><?= $row['room_id']; ?></td>
                        <td><?= $row['room_name']; ?></td>
                        <td><?= number_format($row['price']); ?></td>
                        <td><?= $row['max_people']; ?></td>
                        <td><?= $row['description']; ?></td>

                        <td>
                            <a href="edit.php?id=<?= $row['room_id']; ?>"
                                class="btn btn-warning btn-sm">
                                Sửa
                            </a>

                            <a href="delete.php?id=<?= $row['room_id']; ?>"
                                class="btn btn-danger btn-sm" onclick="return confirm('Xóa?')">
                                Xóa
                            </a>
                        </td>
                    </tr>
                <?php
                }
            ?>  

        </table>
    </div>

</body>
</html>