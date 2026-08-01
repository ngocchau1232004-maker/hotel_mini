<?php

/**
 * Cập nhật lại tổng tiền hóa đơn sau khi thay đổi dịch vụ
 *
 * @param mysqli $conn
 * @param int $booking_id
 * @return bool
 */
function updateInvoice(mysqli $conn, int $booking_id): bool
{
    //==========================================
    // Lấy hóa đơn
    //==========================================

    $invoiceResult = mysqli_query($conn, "
        SELECT
            invoice_id,
            room_total
        FROM invoices
        WHERE booking_id = $booking_id
        LIMIT 1
    ");

    if (!$invoiceResult) {
        throw new Exception(mysqli_error($conn));
    }

    if (mysqli_num_rows($invoiceResult) == 0) {
        // Booking chưa tạo hóa đơn
        return false;
    }

    $invoice = mysqli_fetch_assoc($invoiceResult);

    //==========================================
    // Tính tổng tiền dịch vụ
    //==========================================

    $serviceResult = mysqli_query($conn, "
        SELECT
            COALESCE(SUM(s.price * su.quantity),0) AS service_total
        FROM service_usage su
        JOIN services s
            ON su.service_id = s.service_id
        WHERE su.booking_id = $booking_id
    ");

    if (!$serviceResult) {
        throw new Exception(mysqli_error($conn));
    }

    $service = mysqli_fetch_assoc($serviceResult);

    $service_total = (int)$service['service_total'];

    //==========================================
    // Tổng tiền
    //==========================================

    $room_total = (int)$invoice['room_total'];

    $total_amount = $room_total + $service_total;

    //==========================================
    // Cập nhật hóa đơn
    //==========================================

    if (!mysqli_query($conn, "
        UPDATE invoices
        SET
            service_total = $service_total,
            total_amount = $total_amount
        WHERE invoice_id = {$invoice['invoice_id']}
    ")) {
        throw new Exception(mysqli_error($conn));
    }

    //==========================================
    // Cập nhật booking
    //==========================================

    if (!mysqli_query($conn, "
        UPDATE bookings
        SET
            total_amount = $total_amount
        WHERE booking_id = $booking_id
    ")) {
        throw new Exception(mysqli_error($conn));
    }

    return true;
}