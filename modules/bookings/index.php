<?php
    include '../../includes/auth.php';
    include '../../config/database.php';
    include '../../includes/header.php';

    /** @var mysqli $conn */
        $conn = $conn;

    $sql = "SELECT
            b.booking_id,
            c.full_name,
            GROUP_CONCAT(r.room_number) AS rooms,
            b.check_in_date,
            b.check_out_date,
            b.status,
            b.total_amount
        FROM bookings b
        JOIN customers c ON b.customer_id = c.customer_id

        LEFT JOIN booking_details bd
            ON b.booking_id = bd.booking_id

        LEFT JOIN rooms r
            ON bd.room_id = r.room_id

        GROUP BY b.booking_id

        ORDER BY b.booking_id DESC
    ";

    $result = mysqli_query($conn,$sql);

?>

<div class="container-fluid px-0">  
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

            <h4 class="mb-0">
                <i class="bi bi-calendar-check"></i>
                Danh sách đặt phòng
            </h4>

            <a href="create.php" class="btn btn-light">
                <i class="bi bi-plus-circle"></i>
                Thêm đặt phòng
            </a>

        </div>
        
        <div class="table-responsive">
            <?php include '../../includes/alert.php'; ?>

            <table class="table table-bordered table-hover align-middle">
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
                            <td>
                                <?php
                                    $status = $row['status'];

                                    if($status=='Đã đặt'){
                                        echo '<span class="badge bg-warning">Đã đặt</span>';
                                    }
                                    elseif($status=='Đang thuê'){
                                        echo '<span class="badge bg-success">Đang thuê</span>';
                                    }
                                    elseif($status=='Đã trả phòng'){
                                        echo '<span class="badge bg-primary">Đã trả phòng</span>';
                                    }
                                    else{
                                        echo '<span class="badge bg-danger">Đã hủy</span>';
                                    }
                                ?>
                            </td>

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

                                <a class="btn btn-danger btn-sm"
                                    href="delete.php?id=<?= $row['booking_id']; ?>"
                                    onclick="return confirm('Bạn có chắc muốn xóa?')">
                                        Xóa
                                </a>
                                
                                <?php if($row['status'] == 'Đã đặt'){ ?>
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
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>

            </table>
        </div>
    </div>
</div>

    
<?php include '../../includes/footer.php'; ?>