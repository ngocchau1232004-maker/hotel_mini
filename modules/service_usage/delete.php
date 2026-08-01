<?php
include '../../includes/auth.php';
include '../../config/database.php';
require_once __DIR__ . '/update_invoice.php';

/** @var mysqli $conn */

if (!isset($_GET['id'])) {
    header("Location:../invoices/index.php");
    exit();
}

$id = (int)$_GET['id'];

/*=========================================
= Lấy thông tin dịch vụ
=========================================*/

$sql = "
SELECT
    su.*,
    b.status,
    i.invoice_id
FROM service_usage su
JOIN bookings b
    ON su.booking_id = b.booking_id
LEFT JOIN invoices i
    ON su.booking_id = i.booking_id
WHERE su.usage_id = $id
LIMIT 1
";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {

    die("Không tìm thấy dịch vụ.");

}

$data = mysqli_fetch_assoc($result);

$booking_id = (int)$data['booking_id'];
$invoice_id = (int)$data['invoice_id'];

/*=========================================
= Chỉ cho phép xóa khi đang thuê
=========================================*/

if ($data['status'] != 'Đang thuê') {

    echo "<script>
        alert('Khách đã trả phòng. Không thể xóa dịch vụ.');
        window.location='../bookings/index.php';
    </script>";

    exit();
}

/*=========================================
= Kiểm tra hóa đơn đã thanh toán chưa
=========================================*/

if ($invoice_id > 0) {

    $paid = mysqli_query($conn, "
        SELECT payment_id
        FROM payments
        WHERE invoice_id = $invoice_id
        LIMIT 1
    ");

    if (mysqli_num_rows($paid) > 0) {

        $payment = mysqli_fetch_assoc($paid);

        echo "<script>
            alert('Hóa đơn đã thanh toán. Không thể xóa dịch vụ.');
            window.location='../payments/detail.php?id=".$payment['payment_id']."';
        </script>";

        exit();
    }
}

/*=========================================
= Xóa dịch vụ
=========================================*/

mysqli_begin_transaction($conn);

try {

    if (!mysqli_query($conn, "
        DELETE FROM service_usage
        WHERE usage_id = $id
    ")) {

        throw new Exception(mysqli_error($conn));

    }

    if ($invoice_id > 0) {

        updateInvoice($conn, $booking_id);

    }

    mysqli_commit($conn);

} catch (Exception $e) {

    mysqli_rollback($conn);

    die($e->getMessage());

}

/*=========================================
= Quay lại
=========================================*/

if ($invoice_id > 0) {

    header("Location:../invoices/detail.php?id=".$invoice_id);

} else {

    header("Location:../bookings/detail.php?id=".$booking_id);

}

exit();