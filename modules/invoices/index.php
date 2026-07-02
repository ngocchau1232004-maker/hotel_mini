<?php
    include '../../includes/auth.php';
    include '../../config/database.php';
    include '../../includes/header.php';

    /** @var mysqli $conn */
    $conn = $conn;

    $sql = "SELECT
                i.invoice_id,
                i.invoice_date,
                i.room_total,
                i.service_total,
                i.total_amount,
                b.booking_id,
                c.full_name,
                GROUP_CONCAT(r.room_number SEPARATOR ', ') AS rooms,
                (SELECT COUNT(*) FROM payments p 
                    WHERE p.invoice_id = i.invoice_id) AS paid
            FROM invoices i
            JOIN bookings b ON i.booking_id = b.booking_id
            JOIN customers c ON b.customer_id = c.customer_id
            LEFT JOIN booking_details bd ON b.booking_id = bd.booking_id
            LEFT JOIN rooms r ON bd.room_id = r.room_id
            GROUP BY i.invoice_id
            ORDER BY i.invoice_id DESC";

    $result = mysqli_query($conn, $sql);
?>

<div class="container-fluid">
    <div class="card shadow">

        <!-- HEADER GIỐNG TRANG KHÁCH HÀNG -->
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

            <h4 class="mb-0">
                <i class="bi bi-receipt"></i>
                Danh sách hóa đơn
            </h4>

        </div>

        <div class="card-body">
            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-secondary">
                        <tr>
                            <th>#</th>
                            <th>Khách hàng</th>
                            <th>Phòng</th>
                            <th>Ngày lập</th>
                            <th class="text-end">Tiền phòng</th>
                            <th class="text-end">Dịch vụ</th>
                            <th class="text-end">Tổng tiền</th>
                            <th class="text-center">Thanh toán</th>
                            <th width="220">Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php 
                        $stt = 1; 
                        while($row = mysqli_fetch_assoc($result)) { 
                        ?>
                            <tr>
                                <td><?= $stt++ ?></td>
                                <td><?= htmlspecialchars($row['full_name']) ?></td>
                                <td><?= $row['rooms'] ?></td>

                                <td>
                                    <?= date("d/m/Y H:i", strtotime($row['invoice_date'])) ?>
                                </td>

                                <td class="text-end">
                                    <?= number_format($row['room_total']) ?> đ
                                </td>

                                <td class="text-end">
                                    <?= number_format($row['service_total']) ?> đ
                                </td>

                                <td class="text-end fw-bold text-danger">
                                    <?= number_format($row['total_amount']) ?> đ
                                </td>

                                <td class="text-center">
                                    <?php if($row['paid'] > 0) { ?>
                                        <span class="badge bg-success">Đã thanh toán</span>
                                    <?php } else { ?>
                                        <span class="badge bg-warning text-dark">Chưa thanh toán</span>
                                    <?php } ?>
                                </td>

                                <td>
                                    <a href="detail.php?id=<?= $row['invoice_id'] ?>"
                                       class="btn btn-info btn-sm">
                                        Chi tiết
                                    </a>
                                    <?php if($row['paid'] == 0) { ?>
                                        <a href="../payments/create.php?id=<?= $row['invoice_id'] ?>"
                                           class="btn btn-success btn-sm">
                                            Thanh toán
                                        </a>
                                    <?php } else { ?>
                                        <button class="btn btn-secondary btn-sm" disabled>
                                            Đã thanh toán
                                        </button>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>

                    </tbody>
                </table>

            </div>
        </div>

    </div>
</div>

<?php include __DIR__ .'/../../includes/footer.php'; ?>