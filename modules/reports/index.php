<?php
    include '../../includes/auth.php';
    include("../../config/database.php");

    /** @var mysqli $conn */
    $conn = $conn;

    // =======================
    // Lọc theo ngày
    // =======================
    $from = isset($_GET['from']) ? $_GET['from'] : date("Y-m-01");
    $to   = isset($_GET['to']) ? $_GET['to'] : date("Y-m-d");

    // =======================
    // Thống kê tổng
    // =======================
    $sqlSummary = "SELECT COUNT(*) AS total_invoice,
                        IFNULL(SUM(room_total),0) AS room_revenue,
                        IFNULL(SUM(service_total),0) AS service_revenue,
                        IFNULL(SUM(total_amount),0) AS total_revenue
                    FROM invoices
                    WHERE DATE(invoice_date) BETWEEN '$from' AND '$to' ";

    $resultSummary = mysqli_query($conn, $sqlSummary);
    $summary = mysqli_fetch_assoc($resultSummary);

// =======================
// Danh sách hóa đơn
// =======================
$sql = "SELECT
            invoices.invoice_id,
            invoices.invoice_date,
            invoices.room_total,
            invoices.service_total,
            invoices.total_amount,
            customers.full_name
        FROM invoices
        LEFT JOIN bookings ON invoices.booking_id = bookings.booking_id
        LEFT JOIN customers ON bookings.customer_id = customers.customer_id
        WHERE DATE(invoices.invoice_date) BETWEEN '$from' AND '$to'
        ORDER BY invoices.invoice_date DESC ";

    $result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thống kê doanh thu</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet">
    
    <style>
        .card{
            border-radius:15px; 
        }

        .table th{
            background:#0d6efd;
            color:white;
        }

    </style>
</head>

<body>

    <div class="container mt-4">
        <a href="../../dashboard.php"class="btn btn-outline-primary mb-3">
            <i class="bi bi-arrow-left"></i>
            Quay lại danh sách
        </a>

        <h2 class="mb-4">📊 Thống kê doanh thu</h2>

        <form method="GET" class="row g-3 mb-4">

            <div class="col-md-3">
                <label>Từ ngày</label>
                <input type="date" name="from" class="form-control" value="<?= $from ?>">
            </div>

            <div class="col-md-3">
                <label>Đến ngày</label>
                <input type="date" name="to" class="form-control" value="<?= $to ?>">
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100"> Thống kê </button>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <a href="export_excel.php?from=<?= $from ?>&to=<?= $to ?>"
                    class="btn btn-success">
                    Xuất Excel
                </a>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <a href="print.php?from=<?= $from ?>&to=<?= $to ?>"
                    target="_blank" class="btn btn-danger">
                    Xuất PDF
                </a>
            </div>

        </form>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card shadow">
                    <div class="card-body">
                        <h6>Tổng hóa đơn</h6>
                        <h3> <?= $summary['total_invoice']; ?> </h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow">
                    <div class="card-body">
                        <h6>Doanh thu phòng</h6>

                        <h4 class="text-primary">
                            <?= number_format($summary['room_revenue']); ?> đ
                        </h4>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow">
                    <div class="card-body">
                        <h6>Doanh thu dịch vụ</h6>

                        <h4 class="text-success">
                            <?= number_format($summary['service_revenue']); ?> đ
                        </h4>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow">
                    <div class="card-body">
                        <h6>Tổng doanh thu</h6>

                        <h4 class="text-danger">
                            <?= number_format($summary['total_revenue']); ?> đ
                        </h4>

                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header bg-primary text-white">Danh sách hóa đơn</div>

            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th width="70">STT</th>
                            <th>Mã HĐ</th>
                            <th>Khách hàng</th>
                            <th>Ngày lập</th>
                            <th>Tiền phòng</th>
                            <th>Tiền dịch vụ</th>
                            <th>Tổng tiền</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                            $i=1;
                            while($row=mysqli_fetch_assoc($result)) {
                        ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td><?= $row['invoice_id']; ?></td>
                                <td><?= $row['full_name']; ?></td>
                                <td><?= date("d/m/Y",strtotime($row['invoice_date'])); ?></td>
                                <td><?= number_format($row['room_total']); ?> đ</td>
                                <td><?= number_format($row['service_total']); ?> đ</td>

                                <td class="fw-bold text-danger">
                                    <?= number_format($row['total_amount']); ?> đ
                                </td>
                            </tr>

                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</body>
</html>