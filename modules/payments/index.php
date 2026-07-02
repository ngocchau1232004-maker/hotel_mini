<?php
    include '../../includes/auth.php';
    include '../../config/database.php';
    include '../../includes/header.php';

    /** @var mysqli $conn */
    $conn = $conn;

    $sql = "SELECT
                p.payment_id,
                p.payment_method,
                p.payment_date,
                p.amount,
                i.invoice_id,
                b.booking_id,
                c.full_name
            FROM payments p
            JOIN invoices i ON p.invoice_id = i.invoice_id
            JOIN bookings b ON i.booking_id = b.booking_id
            JOIN customers c ON b.customer_id = c.customer_id
            ORDER BY p.payment_date DESC";

    $result = mysqli_query($conn, $sql);
?>

<div class="container-fluid">
    <div class="card shadow">

        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                <i class="bi bi-cash-stack"></i>
                Danh sách thanh toán
            </h4>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-secondary">
                        <tr>
                            <th width="60">#</th>
                            <th>Khách hàng</th>
                            <th>Hóa đơn</th>
                            <th>Phương thức</th>
                            <th>Ngày thanh toán</th>
                            <th class="text-end">Số tiền</th>
                            <th width="180">Thao tác</th>
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
                                <td>#<?= $row['invoice_id'] ?></td>

                                <td>
                                    <?php
                                        switch($row['payment_method']) {
                                            case 'Tiền mặt':
                                                echo "<span class='badge bg-success'>Tiền mặt</span>";
                                                break;
                                            case 'Chuyển khoản':
                                                echo "<span class='badge bg-primary'>Chuyển khoản</span>";
                                                break;
                                            case 'Momo':
                                                echo "<span class='badge bg-danger'>Momo</span>";
                                                break;
                                            default:
                                                echo "<span class='badge bg-secondary'>Khác</span>";
                                        }
                                    ?>
                                </td>

                                <td>
                                    <?= date("d/m/Y H:i", strtotime($row['payment_date'])) ?>
                                </td>

                                <td class="text-end fw-bold text-danger">
                                    <?= number_format($row['amount']) ?> đ
                                </td>

                                <td>
                                    <a href="detail.php?id=<?= $row['payment_id'] ?>"
                                    class="btn btn-info btn-sm">
                                        <i class="fa fa-eye"></i> Chi tiết
                                    </a>

                                    <a href="delete.php?id=<?= $row['payment_id'] ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Bạn có chắc muốn xóa?')">
                                        <i class="fa fa-trash"></i> Xóa
                                    </a>
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