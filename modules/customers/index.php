<?php
    include '../../includes/auth.php';
    include '../../config/database.php';

    /** @var mysqli $conn */
    $conn = $conn;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Loại phòng</title>

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
        
        <h2>Quản lý khách hàng</h2>

        <a href="create.php"
            class="btn btn-success mb-3">
            Thêm khách hàng
        </a>

        <table class="table table-bordered">
            <tr>
                <th>ID</th>
                <th>Họ tên</th>
                <th>Email</th>
                <th>Số điện thoại</th>
                <th>Địa chỉ</th>
                <th>Thao tác</th>
            </tr>

            <?php
                $sql = "SELECT * FROM customers";
                $result = mysqli_query($conn,$sql);

                while($row = mysqli_fetch_assoc($result)){
                ?>
                    <tr>
                        <td><?= $row['customer_id']; ?></td>
                        <td><?= $row['full_name']; ?></td>
                        <td><?= $row['email']; ?></td>
                        <td><?= $row['phone']; ?></td>
                        <td><?= $row['address']; ?></td>

                        <td>
                            <a href="edit.php?id=<?= $row['customer_id']; ?>"
                                class="btn btn-warning btn-sm">
                                Sửa
                            </a>

                            <a href="delete.php?id=<?= $row['customer_id']; ?>"
                                class="btn btn-danger btn-sm" onclick="return confirm('Xóa?')">
                                Xóa
                            </a>
                        </td>
                    </tr>
                <?php
                }
            ?>
    </div>

</body>
</html>