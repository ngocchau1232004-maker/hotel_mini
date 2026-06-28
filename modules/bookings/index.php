<?php
    include '../../includes/auth.php';
    include '../../config/database.php';

    $sql = "
        SELECT
            b.booking_id,
            c.full_name,
            GROUP_CONCAT(r.room_number) AS rooms,
            b.check_in_date,
            b.check_out_date,
            b.status,
            b.total_amount
        FROM bookings b

        JOIN customers c
            ON b.customer_id = c.customer_id

        LEFT JOIN booking_details bd
            ON b.booking_id = bd.booking_id

        LEFT JOIN rooms r
            ON bd.room_id = r.room_id

        GROUP BY b.booking_id

        ORDER BY b.booking_id DESC
    ";

    $result = mysqli_query($conn,$sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Quản lý đặt phòng</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet">

</head>


<body>
    
    <div class="container mt-4">        
        <a href="../../dashboard.php"class="btn btn-outline-primary mb-3" >
            <i class="bi bi-arrow-left" "></i>
            Quay lại danh sách
        </a>

        <h2>Danh sách đặt phòng</h2>

        <table class="table table-bordered">
            <a href="create.php" class="btn btn-success mb-3" >
                + Thêm đặt phòng
            </a>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Khách hàng</th>
                    <th>Phòng</th>
                    <th>Ngày nhận phòng</th>
                    <th>Ngày trả phòng</th>
                    <th>Trạng thái</th>
                    <th>Tổng tiền</th>
                    <th>Chức năng</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr>

                        <td><?= $row['booking_id']; ?></td>
                        <td><?= htmlspecialchars($row['full_name']); ?></td>
                        <td><?= $row['rooms']; ?></td>
                        <td><?= $row['check_in_date']; ?></td>
                        <td><?= $row['check_out_date']; ?></td>
                        <td><?= $row['status']; ?></td>

                        <td>
                            <?= number_format($row['total_amount'], 0, ',', '.'); ?> VNĐ
                        </td>

                        <td>
                            <a href="detail.php?id=<?= $row['booking_id']; ?>"
                                class="btn btn-info btn-sm">
                                Chi tiết
                            </a>

                            <a class="btn btn-warning btn-sm"
                            href="edit.php?id=<?= $row['booking_id']; ?>">
                                Sửa
                            </a>

                            <?php if($row['status'] == 'Đã xác nhận'){ ?>
                                <a href="checkin.php?id=<?= $row['booking_id']; ?>"
                                    class="btn btn-success btn-sm"
                                    onclick="return confirm('Xác nhận check-in?')">
                                    Check-in
                                </a>
                            <?php } ?>

                            <?php if($row['status'] == 'Đang thuê'){ ?>
                                <a href="checkout.php?id=<?= $row['booking_id']; ?>"
                                    class="btn btn-secondary btn-sm"
                                    onclick="return confirm('Xác nhận trả phòng?')">
                                    Check-out
                                </a>
                            <?php } ?>

                            <a class="btn btn-danger btn-sm"
                                href="delete.php?id=<?= $row['booking_id']; ?>"
                                onclick="return confirm('Bạn có chắc muốn xóa?')">
                                    Xóa
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>

        </table>
    </div>
</body>
</html>