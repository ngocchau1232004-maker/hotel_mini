<?php
header("Content-Type: application/json; charset=UTF-8");

include '../../config/database.php';

/** @var mysqli $conn */

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    echo json_encode([
        "success" => false,
        "message" => "Method không hợp lệ."
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

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

    c.customer_id,
    c.full_name,
    c.gender,
    c.phone,
    c.email,
    c.id_card,
    c.address,

    GROUP_CONCAT(r.room_number SEPARATOR ', ') AS rooms

FROM bookings b

JOIN customers c
    ON b.customer_id = c.customer_id

LEFT JOIN booking_details bd
    ON b.booking_id = bd.booking_id

LEFT JOIN rooms r
    ON bd.room_id = r.room_id

WHERE b.booking_id='$id'

GROUP BY b.booking_id
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode([
        "success" => false,
        "message" => mysqli_error($conn)
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

if (mysqli_num_rows($result) == 0) {
    echo json_encode([
        "success" => false,
        "message" => "Không tìm thấy booking."
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

$row = mysqli_fetch_assoc($result);

echo json_encode([
    "success" => true,
    "data" => [

        "booking_id" => $row['booking_id'],

        "customer" => [
            "customer_id" => $row['customer_id'],
            "full_name" => $row['full_name'],
            "gender" => $row['gender'],
            "phone" => $row['phone'],
            "email" => $row['email'],
            "id_card" => $row['id_card'],
            "address" => $row['address']
        ],

        "rooms" => $row['rooms'],

        "check_in_date" => $row['check_in_date'],
        "check_out_date" => $row['check_out_date'],

        "actual_check_in" => $row['actual_check_in'],
        "actual_check_out" => $row['actual_check_out'],

        "status" => $row['status'],
        "total_amount" => $row['total_amount'],
        "note" => $row['note'],
        "booking_date" => $row['booking_date']

    ]
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

mysqli_close($conn);