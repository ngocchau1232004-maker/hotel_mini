<?php
    include '../../includes/auth.php';
    include("../../config/database.php");
    include '../../includes/header.php';

    /** @var mysqli $conn */
    $conn = $conn;

    // =======================
    // Lọc theo ngày
    // =======================
    $from = $_GET['from'] ?? date("Y-m-01");
    $to   = $_GET['to'] ?? date("Y-m-d");

    // =======================
    // Thống kê tổng
    // =======================
    $sqlSummary = "SELECT 
                        COUNT(*) AS total_invoice,
                        IFNULL(SUM(room_total),0) AS room_revenue,
                        IFNULL(SUM(service_total),0) AS service_revenue,
                        IFNULL(SUM(total_amount),0) AS total_revenue
                    FROM invoices
                    WHERE DATE(invoice_date) BETWEEN '$from' AND '$to'";

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
            LEFT JOIN bookings 
                ON invoices.booking_id = bookings.booking_id
            LEFT JOIN customers 
                ON bookings.customer_id = customers.customer_id
            WHERE DATE(invoices.invoice_date) BETWEEN '$from' AND '$to'
            ORDER BY invoices.invoice_date DESC";

    $result = mysqli_query($conn, $sql);
?>

<div class="container-fluid">
    <div class="card shadow">

        <!-- HEADER -->
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">
                <i class="bi bi-bar-chart"></i>
                Thống kê doanh thu
            </h4>
        </div>

        <div class="card-body">

            <!-- FILTER -->
            <form method="GET" class="row g-3 mb-3">

                <div class="col-12 col-md-3">
                    <label>Từ ngày</label>
                    <input type="date" name="from" class="form-control" value="<?= $from ?>">
                </div>

                <div class="col-12 col-md-3">
                    <label>Đến ngày</label>
                    <input type="date" name="to" class="form-control" value="<?= $to ?>">
                </div>

                <div class="col-12 col-md-6 d-flex flex-wrap gap-2 align-items-end">

                    <button class="btn btn-primary">
                        Thống kê
                    </button>

                    <a href="export_pdf.php?from=<?= $from ?>&to=<?= $to ?>" class="btn btn-danger">
                        PDF
                    </a>

                    <a href="export_excel.php?from=<?= $from ?>&to=<?= $to ?>" class="btn btn-success">
                        Excel
                    </a>

                    <a href="print.php?from=<?= $from ?>&to=<?= $to ?>" target="_blank" class="btn btn-secondary">
                        Print
                    </a>

                </div>

            </form>

            <!-- STATS -->
            <div class="row mb-4">

                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6>Tổng hóa đơn</h6>
                            <h4><?= $summary['total_invoice'] ?></h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6>Doanh thu phòng</h6>
                            <h4 class="text-primary">
                                <?= number_format($summary['room_revenue']) ?> đ
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6>Doanh thu dịch vụ</h6>
                            <h4 class="text-success">
                                <?= number_format($summary['service_revenue']) ?> đ
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6>Tổng doanh thu</h6>
                            <h4 class="text-danger">
                                <?= number_format($summary['total_revenue']) ?> đ
                            </h4>
                        </div>
                    </div>
                </div>

            </div>

            <!-- TABLE -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th>STT</th>
                            <th>Mã HĐ</th>
                            <th>Khách hàng</th>
                            <th>Ngày lập</th>
                            <th>Tiền phòng</th>
                            <th>Tiền dịch vụ</th>
                            <th>Tổng tiền</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $i = 1; while($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= $row['invoice_id'] ?></td>
                                <td><?= $row['full_name'] ?></td>
                                <td><?= date("d/m/Y", strtotime($row['invoice_date'])) ?></td>
                                <td><?= number_format($row['room_total']) ?> đ</td>
                                <td><?= number_format($row['service_total']) ?> đ</td>
                                <td class="fw-bold text-danger">
                                    <?= number_format($row['total_amount']) ?> đ
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