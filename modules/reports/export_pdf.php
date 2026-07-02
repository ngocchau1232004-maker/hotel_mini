<?php
    include '../../includes/auth.php';
    include '../../config/database.php';

    require '../../vendor/autoload.php';

    use Dompdf\Dompdf;
    use Dompdf\Options;

    /** @var mysqli $conn */

    $from = $_GET['from'] ?? date("Y-m-01");
    $to   = $_GET['to'] ?? date("Y-m-d");

    //=========================
    // Thống kê
    //=========================
    $sqlSummary = "SELECT
                        COUNT(*) AS total_invoice,
                        IFNULL(SUM(room_total),0) AS room_revenue,
                        IFNULL(SUM(service_total),0) AS service_revenue,
                        IFNULL(SUM(total_amount),0) AS total_revenue
                    FROM invoices
                    WHERE DATE(invoice_date)
                    BETWEEN '$from' AND '$to' ";

    $resultSummary = mysqli_query($conn, $sqlSummary);
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
            LEFT JOIN bookings
                ON invoices.booking_id = bookings.booking_id
            LEFT JOIN customers
                ON bookings.customer_id = customers.customer_id
            WHERE DATE(invoices.invoice_date)
                BETWEEN '$from' AND '$to'
            ORDER BY invoices.invoice_date DESC";

    $result = mysqli_query($conn, $sql);

    //=========================
    // Dompdf
    //=========================
    $options = new Options();

    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);
    $options->set('defaultFont', 'DejaVu Sans');

    $dompdf = new Dompdf($options);

    //=========================
    // Bắt đầu tạo HTML
    //=========================
    ob_start();
    ?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size:14px;
            color:#000;
            margin:20px;
        }

        h1{
            text-align:center;
            font-size:24px;
            margin-bottom:8px;
        }

        h4{
            text-align:center;
            font-weight:normal;
            margin-bottom:25px;
        }

        .summary{
            margin-bottom:20px;
        }

        .summary p{
            margin:6px 0;
        }

        .total{
            color:red;
            font-size:16px;
            font-weight:bold;
        }

        table{
            width:100%;
            border-collapse:collapse;
            margin-top:15px;
        }

        table th,
        table td{
            border:1px solid #000;
            padding:8px;
            text-align:center;
        }

        table th{
            background:#e5e5e5;
        }

        .footer{
            margin-top:60px;
            text-align:right;
            line-height:28px;
        }
    </style>
</head>

<body>

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
            <?=number_format($summary['room_revenue']);?> VNĐ
        </p>

        <p>
            <b>Doanh thu dịch vụ:</b>
            <?=number_format($summary['service_revenue']);?> VNĐ
        </p>

        <p class="total">
            Tổng doanh thu:
            <?=number_format($summary['total_revenue']);?> VNĐ
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
            $i = 1;
            while($row = mysqli_fetch_assoc($result)){
            ?>
                <tr>
                    <td><?= $i++; ?></td>
                    <td><?= $row['invoice_id']; ?></td>
                    <td><?= htmlspecialchars($row['full_name']); ?></td>
                    <td><?= date("d/m/Y", strtotime($row['invoice_date'])); ?></td>
                    <td><?= number_format($row['room_total']); ?></td>
                    <td><?= number_format($row['service_total']); ?></td>
                    <td><?= number_format($row['total_amount']); ?></td>
                </tr>

            <?php } ?>
        </tbody>
    </table>

    <div class="footer">
        <p style="margin-right: 30px;">
            Cần Thơ,
            ngày <?=date("d");?>
            tháng <?=date("m");?>
            năm <?=date("Y");?>
        </p>

        <strong style="margin-right: 75px;">
            Người Lập Báo Cáo
        </strong>
        <br><br><br>

        <p style="margin-right: 50px;">
            ................................................
        </p>
    </div>

</body>
</html>

<?php

    //=========================
    // Lấy toàn bộ HTML vừa tạo
    //=========================
    $html = ob_get_clean();

    //=========================
    // Khởi tạo Dompdf
    //=========================
    $dompdf->loadHtml($html, 'UTF-8');

    // Khổ giấy A4 ngang
    $dompdf->setPaper('A4', 'landscape');

    // Render PDF
    $dompdf->render();

    //=========================
    // Thông tin file PDF
    //=========================
    $fileName = "BaoCaoDoanhThu_"
                . date("Ymd_His")
                . ".pdf";

    // true  : tải về máy
    // false : mở PDF trên trình duyệt
    $download = false;

    $dompdf->stream(
        $fileName,
        [
            "Attachment" => $download
        ]
    );

    exit;
?>