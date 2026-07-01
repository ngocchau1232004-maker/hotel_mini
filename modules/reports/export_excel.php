<?php
    include '../../includes/auth.php';
    include("../../config/database.php");

    /** @var mysqli $conn */
    $conn = $conn;

    $from = isset($_GET['from']) ? $_GET['from'] : date("Y-m-01");
    $to   = isset($_GET['to']) ? $_GET['to'] : date("Y-m-d");

    $sql = "SELECT
                invoices.invoice_id,
                customers.full_name,
                invoices.invoice_date,
                invoices.room_total,
                invoices.service_total,
                invoices.total_amount
            FROM invoices
            LEFT JOIN bookings ON invoices.booking_id = bookings.booking_id
            LEFT JOIN customers ON bookings.customer_id = customers.customer_id
            WHERE DATE(invoices.invoice_date) BETWEEN '$from' AND '$to'
            ORDER BY invoices.invoice_date DESC";

    $result = mysqli_query($conn,$sql);

    // Header để Excel mở
    header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
    header("Content-Disposition: attachment; filename=BaoCaoDoanhThu.xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    // UTF8
    echo "\xEF\xBB\xBF";
?>

<table border="1">

    <tr style="background:#cccccc">
        <th>Mã HĐ</th>
        <th>Khách hàng</th>
        <th>Ngày lập</th>
        <th>Tiền phòng</th>
        <th>Tiền dịch vụ</th>
        <th>Tổng tiền</th>
    </tr>

    <?php
        $tong = 0;
        while($row=mysqli_fetch_assoc($result)){
            $tong += $row['total_amount'];
    ?>
        <tr>
            <td><?= $row['invoice_id']; ?></td>
            <td><?= $row['full_name']; ?></td>
            <td><?= date("d/m/Y",strtotime($row['invoice_date'])); ?></td>
            <td><?= number_format($row['room_total']); ?></td>
            <td><?= number_format($row['service_total']); ?></td>
            <td><?= number_format($row['total_amount']); ?></td>
        </tr>
    <?php } ?>

    <tr>
        <td colspan="5" align="right">
            <b>Tổng doanh thu</b>
        </td>

        <td>
            <b><?= number_format($tong); ?> VNĐ</b>
        </td>
    </tr>

</table>