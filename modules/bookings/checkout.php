<?php
include '../../includes/auth.php';
include '../../config/database.php';

/** @var mysqli $conn */
$conn = $conn;

if (!isset($_GET['id'])) {
    header("Location:index.php");
    exit();
}

$id = intval($_GET['id']);

mysqli_begin_transaction($conn);

try {

    /*====================================================
    = Kiểm tra booking
    ====================================================*/

    $sql = "
        SELECT *
        FROM bookings
        WHERE booking_id = $id
        FOR UPDATE
    ";

    $result = mysqli_query($conn, $sql);

    if (!$result || mysqli_num_rows($result) == 0) {
        throw new Exception("Không tìm thấy booking.");
    }

    $booking = mysqli_fetch_assoc($result);

    /*====================================================
    = Chỉ checkout khi đang thuê
    ====================================================*/

    if ($booking['status'] != 'Đang thuê') {
        throw new Exception("Booking chưa ở trạng thái Đang thuê.");
    }

    /*====================================================
    = Không tạo hóa đơn trùng
    ====================================================*/

    $checkInvoice = mysqli_query($conn, "
        SELECT invoice_id
        FROM invoices
        WHERE booking_id = $id
        LIMIT 1
    ");

    if (!$checkInvoice) {
        throw new Exception(mysqli_error($conn));
    }

    if (mysqli_num_rows($checkInvoice) > 0) {

        $invoice = mysqli_fetch_assoc($checkInvoice);

        mysqli_commit($conn);

        header("Location:../invoices/detail.php?id=".$invoice['invoice_id']);
        exit();
    }

    /*====================================================
    = Tính số ngày ở
    ====================================================*/

    if (empty($booking['actual_check_in'])) {
        throw new Exception("Booking chưa Check-in.");
    }

    $checkin = strtotime($booking['actual_check_in']);
    $checkout = time();

    $days = ceil(($checkout - $checkin) / 86400);

    if ($days < 1) {
        $days = 1;
    }

    /*====================================================
    = Tính tiền phòng
    ====================================================*/

    $room_total = 0;

    $sqlRoom = "
        SELECT price
        FROM booking_details
        WHERE booking_id = $id
    ";

    $resultRoom = mysqli_query($conn, $sqlRoom);

    if (!$resultRoom) {
        throw new Exception(mysqli_error($conn));
    }

    while ($room = mysqli_fetch_assoc($resultRoom)) {

        $room_total += $room['price'] * $days;

    }

    /*====================================================
    = Tính tiền dịch vụ
    ====================================================*/

    $service_total = 0;

    $sqlService = "
        SELECT
            su.quantity,
            s.price
        FROM service_usage su
        JOIN services s
            ON su.service_id = s.service_id
        WHERE su.booking_id = $id
    ";

    $resultService = mysqli_query($conn, $sqlService);

    if (!$resultService) {
        throw new Exception(mysqli_error($conn));
    }

    while ($service = mysqli_fetch_assoc($resultService)) {

        $service_total +=
            $service['price'] * $service['quantity'];

    }

    /*====================================================
    = Tổng tiền
    ====================================================*/

    $total = $room_total + $service_total;

    /*====================================================
    = Tạo hóa đơn
    ====================================================*/

    $insertInvoice = mysqli_query($conn, "
        INSERT INTO invoices(
            booking_id,
            room_total,
            service_total,
            total_amount
        )
        VALUES(
            $id,
            $room_total,
            $service_total,
            $total
        )
    ");

    if (!$insertInvoice) {
        throw new Exception(mysqli_error($conn));
    }

    $invoice_id = mysqli_insert_id($conn);

    /*====================================================
    = Lưu tổng tiền vào booking
    ====================================================*/

    $updateBooking = mysqli_query($conn, "
        UPDATE bookings
        SET total_amount = $total
        WHERE booking_id = $id
    ");

    if (!$updateBooking) {
        throw new Exception(mysqli_error($conn));
    }

    mysqli_commit($conn);

    header("Location:../invoices/detail.php?id=".$invoice_id);
    exit();

} catch (Exception $e) {

    mysqli_rollback($conn);

    echo "
    <div style='padding:30px;font-family:Arial'>
        <h3>Checkout thất bại</h3>
        <p>".$e->getMessage()."</p>
        <a href='index.php'>Quay lại</a>
    </div>";
}