<?php
include '../../includes/auth.php';
include '../../config/database.php';

$id = $_GET['id'];

$sql = "
    SELECT
        b.*,
        c.full_name,
        c.phone,
        c.email,
        c.id_card,
        GROUP_CONCAT(r.room_number SEPARATOR ', ') AS rooms

    FROM bookings b

    JOIN customers c
        ON b.customer_id = c.customer_id

    LEFT JOIN booking_details bd
        ON b.booking_id = bd.booking_id

    LEFT JOIN rooms r
        ON bd.room_id = r.room_id

    WHERE b.booking_id = '$id'

    GROUP BY b.booking_id
";

$result = mysqli_query($conn, $sql);

$row = mysqli_fetch_assoc($result);

if(!$row){
    header("Location:index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Chi tiết đặt phòng</title>

    <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body>

<div class="container mt-4">

    <h2>Chi tiết đặt phòng</h2>

    <table class="table table-bordered">

        <tr>
            <th width="250">Mã đặt phòng</th>
            <td><?= $row['booking_id']; ?></td>
        </tr>

        <tr>
            <th>Khách hàng</th>
            <td><?= $row['full_name']; ?></td>
        </tr>

        <tr>
            <th>Số điện thoại</th>
            <td><?= $row['phone']; ?></td>
        </tr>

        <tr>
            <th>Email</th>
            <td><?= $row['email']; ?></td>
        </tr>

        <tr>
            <th>CMND/CCCD</th>
            <td><?= $row['id_card']; ?></td>
        </tr>

        <tr>
            <th>Phòng</th>
            <td><?= $row['rooms']; ?></td>
        </tr>

        <tr>
            <th>Ngày nhận phòng</th>
            <td><?= $row['check_in_date']; ?></td>
        </tr>

        <tr>
            <th>Ngày trả phòng</th>
            <td><?= $row['check_out_date']; ?></td>
        </tr>

        <tr>
            <th>Check-in thực tế</th>
            <td>
                <?= $row['actual_check_in']
                    ? $row['actual_check_in']
                    : 'Chưa check-in'; ?>
            </td>
        </tr>

        <tr>
            <th>Check-out thực tế</th>
            <td>
                <?= $row['actual_check_out']
                    ? $row['actual_check_out']
                    : 'Chưa check-out'; ?>
            </td>
        </tr>

        <tr>
            <th>Trạng thái</th>
            <td><?= $row['status']; ?></td>
        </tr>

        <tr>
            <th>Tổng tiền</th>
            <td>
                <?= number_format($row['total_amount']); ?>
                VNĐ
            </td>
        </tr>

        <tr>
            <th>Ghi chú</th>
            <td>
                <?= $row['note'] ? $row['note'] : 'Không có'; ?>
            </td>
        </tr>

        <tr>
            <th>Ngày tạo đơn</th>
            <td><?= $row['booking_date']; ?></td>
        </tr>

    </table>

    <a href="index.php"
       class="btn btn-secondary">
        Quay lại
    </a>

    <a href="edit.php?id=<?= $row['booking_id']; ?>"
       class="btn btn-warning">
        Sửa
    </a>

</div>

</body>
</html>