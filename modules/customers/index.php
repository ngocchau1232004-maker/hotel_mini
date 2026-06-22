<?php
    include '../../includes/auth.php';
    include '../../config/database.php';

    /** @var mysqli $conn */
    $conn = $conn;

    $sql = "SELECT * FROM customers ORDER BY customer_id DESC";
    $result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quản lý khách hàng</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet">   

</head>

<body>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Danh sách khách hàng</h2>

        <a href="create.php" class="btn btn-success">
            + Thêm khách hàng
        </a>
    </div>

    <table class="table table-bordered table-hover">

        <thead class="table-secondary">
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
                </th>
                <th width="100">Thao tác</th>
            </tr>
        </thead>

        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)) { ?>

                <tr>
                    <td><?php echo $row['customer_id']; ?></td>
                    <td><?php echo $row['full_name']; ?></td>
                    <td><?php echo $row['gender']; ?></td>
                    <td><?php echo $row['phone']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['id_card']; ?></td>
                    <td><?php echo $row['address']; ?></td>
                    <td> số phòng </td>
                    <td><?php echo $row['created_at']; ?></td>

                    <td>
                        <a href="edit.php?id=<?php echo $row['customer_id']; ?>" class="btn btn-warning btn-sm">
                            Sửa
                        </a>

                        <a href="delete.php?id=<?php echo $row['customer_id']; ?>" class="btn btn-danger btn-sm"
                            onclick="return confirm('Bạn có chắc muốn xóa khách hàng này?')">
                            Xóa
                        </a>
                    </td>
                </tr>

            <?php } ?>
        </tbody>
    </table>
    <br><br>

    <a href="../../dashboard.php" class="btn btn-secondary">
        Quay lại
    </a>

</div>

</body>
</html>