<?php
    include '../../includes/auth.php';
    include("../../config/database.php");

    /** @var mysqli $conn */
    $conn = $conn;

    $from = isset($_GET['from']) ? $_GET['from'] : date("Y-m-01");
    $to   = isset($_GET['to']) ? $_GET['to'] : date("Y-m-d");

    //=========================
    // Thống kê
    //=========================
    $sqlSummary = "SELECT COUNT(*) total_invoice,
                        IFNULL(SUM(room_total),0) room_revenue,
                        IFNULL(SUM(service_total),0) service_revenue,
                        IFNULL(SUM(total_amount),0) total_revenue
                    FROM invoices 
                    WHERE DATE(invoice_date) BETWEEN '$from' AND '$to' ";

    $resultSummary = mysqli_query($conn,$sqlSummary);
    $summary = mysqli_fetch_assoc($resultSummary);

    //=========================
    // Danh sách hóa đơn
    //=========================
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

    $result = mysqli_query($conn,$sql);

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo doanh thu</title>
    <link rel="stylesheet" href="../../assets/css/report-print.css">

</head>

<body>
    <div class="container">
        <h1>BÁO CÁO DOANH THU KHÁCH SẠN</h1>

        <h4>
            Từ ngày <?=date("d/m/Y",strtotime($from));?>
            đến <?=date("d/m/Y",strtotime($to));?>
        </h4>

        <div class="summary">
            <p>
                <b>Tổng hóa đơn:</b>
                <?=$summary['total_invoice'];?>
            </p>

            <p>
                <b>Doanh thu phòng:</b>
                <?=number_format($summary['room_revenue']);?>
                VNĐ
            </p>

            <p>
                <b>Doanh thu dịch vụ:</b>
                <?=number_format($summary['service_revenue']);?>
                VNĐ
            </p>

            <p class="total">
                <b>Tổng doanh thu:
                    <?=number_format($summary['total_revenue']);?>
                    VNĐ
                </b>
            </p>

        </div>

        <table>
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Mã HĐ</th>
                    <th>Khách hàng</th>
                    <th>Ngày lập</th>
                    <th>Tiền phòng</th>
                    <th>Tiền DV</th>
                    <th>Tổng tiền</th>
                </tr>
            </thead>

            <tbody>
                <?php
                $i=1;
                while($row=mysqli_fetch_assoc($result)){
                ?>
                    <tr>
                        <td><?=$i++;?></td>
                        <td><?=$row['invoice_id'];?></td>
                        <td><?=$row['full_name'];?></td>
                        <td><?=date("d/m/Y",strtotime($row['invoice_date']));?></td>
                        <td><?=number_format($row['room_total']);?></td>
                        <td><?=number_format($row['service_total']);?></td>
                        <td><?=number_format($row['total_amount']);?></td>
                    </tr>
                <?php } ?>
            </tbody>

        </table>

        <div class="footer">
            <p>
                Cần Thơ, ngày <?= date("d"); ?> tháng <?= date("m"); ?> năm <?= date("Y"); ?>
            </p>
            <br><br><br>

            <strong>Người lập báo cáo</strong>
        </div>

    </div>

    <script>
        // Chờ trang tải xong rồi mở hộp thoại in
        window.onload = function () {
            window.print();
        };

        // Sau khi người dùng In hoặc Save PDF thì đóng tab
        window.onafterprint = function () {
            window.close();
        };
    </script>

</body>
</html>