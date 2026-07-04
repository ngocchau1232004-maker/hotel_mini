<?php

//* |==============================================================
//* |     HOTEL MINI API - Bookings API
//* |  File: api/bookings/edit.php
//* |  URL: http://localhost/hotel_mini/api/bookings/edit.php
//* |==============================================================

header("Content-Type: application/json; charset=UTF-8");
require_once '../auth.php';


if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    if (!isset($_GET['id'])) {
        echo json_encode([
            "success" => false,
            "message" => "Thiếu ID booking."
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    $id = intval($_GET['id']);

    $sql = "
        SELECT
            b.*,
            bd.room_id
        FROM bookings b
        LEFT JOIN booking_details bd
            ON b.booking_id = bd.booking_id
        WHERE b.booking_id='$id'
    ";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 0) {

        echo json_encode([
            "success" => false,
            "message" => "Không tìm thấy booking."
        ], JSON_UNESCAPED_UNICODE);

        exit();
    }

    $booking = mysqli_fetch_assoc($result);

    $customers = [];

    $resultCustomer = mysqli_query($conn,
        "SELECT customer_id,
                full_name
        FROM customers
        ORDER BY full_name");

    while ($row = mysqli_fetch_assoc($resultCustomer)) {
        $customers[] = $row;
    }

    $rooms = [];

    $resultRoom = mysqli_query($conn,
        "SELECT
            r.room_id,
            r.room_number,
            rt.type_name
        FROM rooms r
        JOIN room_types rt
            ON r.room_type_id=rt.room_type_id
        WHERE r.status='Trống'
            OR r.room_id='".$booking['room_id']."'
    ");

    while ($row = mysqli_fetch_assoc($resultRoom)) {
        $rooms[] = $row;
    }

    echo json_encode([
        "success" => true,
        "booking" => $booking,
        "customers" => $customers,
        "rooms" => $rooms
    ], JSON_UNESCAPED_UNICODE);

    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        $data = $_POST;
    }

    if (!isset($data['booking_id'])) {
        echo json_encode([
            "success" => false,
            "message" => "Thiếu booking_id."
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    $id = intval($data['booking_id']);

    mysqli_begin_transaction($conn);

    try {

        $customer_id = intval($data['customer_id']);
        $room_id = intval($data['room_id']);
        $check_in = $data['check_in_date'];
        $check_out = $data['check_out_date'];
        $status = $data['status'];
        $note = $data['note'];

        if (strtotime($check_out) <= strtotime($check_in)) {

            echo json_encode([
                "success" => false,
                "message" => "Ngày trả phải sau ngày nhận."
            ], JSON_UNESCAPED_UNICODE);

            exit();
        }

        // Kiểm tra trùng lịch phòng

        $sql = "
        SELECT b.booking_id

        FROM bookings b

        JOIN booking_details bd
            ON b.booking_id=bd.booking_id

        WHERE bd.room_id='$room_id'

        AND b.booking_id<>'$id'

        AND b.status IN('Đã đặt','Đang thuê')

        AND (
            b.check_in_date<'$check_out'
            AND
            b.check_out_date>'$check_in'
        )
        ";

        $check = mysqli_query($conn, $sql);

        if (mysqli_num_rows($check) > 0) {

            echo json_encode([
                "success" => false,
                "message" => "Phòng đã được đặt trong khoảng thời gian này."
            ], JSON_UNESCAPED_UNICODE);

            exit();
        }

        // Lấy giá phòng

        $priceResult = mysqli_query($conn, "
            SELECT rt.price

            FROM rooms r

            JOIN room_types rt
                ON r.room_type_id=rt.room_type_id

            WHERE r.room_id='$room_id'
        ");

        $priceRow = mysqli_fetch_assoc($priceResult);

        if (!$priceRow) {

            throw new Exception("Không tìm thấy giá phòng.");

        }

        $price = $priceRow['price'];

        $days = ceil(
            (strtotime($check_out) - strtotime($check_in))
            / 86400
        );

        if ($days <= 0) {

            $days = 1;

        }

        $total = $price * $days;

        // Lấy phòng cũ

        $oldRoom = mysqli_query($conn,
            "SELECT room_id
            FROM booking_details
            WHERE booking_id='$id'"
        );

        $old = mysqli_fetch_assoc($oldRoom);

        if ($old) {

            mysqli_query($conn,
                "UPDATE rooms
                SET status='Trống'
                WHERE room_id='".$old['room_id']."'"
            );

        }

        // Update booking

        mysqli_query($conn,"
            UPDATE bookings
            SET
                customer_id='$customer_id',
                check_in_date='$check_in',
                check_out_date='$check_out',
                status='$status',
                total_amount='$total',
                note='$note'
            WHERE booking_id='$id'
        ");

        // Xóa booking detail

        mysqli_query($conn,
            "DELETE FROM booking_details
            WHERE booking_id='$id'"
        );

        // Thêm booking detail mới

        mysqli_query($conn,"
            INSERT INTO booking_details
            (
                booking_id,
                room_id,
                price
            )
            VALUES
            (
                '$id',
                '$room_id',
                '$price'
            )
        ");

        // Cập nhật trạng thái phòng

        if ($status == "Đã đặt") {

            $roomStatus = "Đã đặt";

        } elseif ($status == "Đang thuê") {

            $roomStatus = "Đang thuê";

        } else {

            $roomStatus = "Trống";

        }

        mysqli_query($conn,"
            UPDATE rooms
            SET status='$roomStatus'
            WHERE room_id='$room_id'
        ");

        mysqli_commit($conn);

        echo json_encode([
            "success" => true,
            "message" => "Cập nhật booking thành công.",
            "booking_id" => $id,
            "total_amount" => $total
        ], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {

        mysqli_rollback($conn);

        echo json_encode([
            "success" => false,
            "message" => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);

    }

    exit();
}

echo json_encode([
    "success" => false,
    "message" => "Method không hợp lệ."
], JSON_UNESCAPED_UNICODE);

?>