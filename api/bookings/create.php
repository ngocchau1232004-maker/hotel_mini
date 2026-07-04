<?php

//* |==============================================================
//* |     HOTEL MINI API - Bookings API
//* |  File: api/bookings/create.php
//* |  URL: http://localhost/hotel_mini/api/bookings/create.php
//* |==============================================================

require_once '../auth.php';


if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    // Danh sách khách hàng
    $customers = [];
    $result = mysqli_query($conn, "SELECT customer_id, full_name, phone
                                   FROM customers
                                   ORDER BY full_name");

    while ($row = mysqli_fetch_assoc($result)) {
        $customers[] = $row;
    }

    // Danh sách phòng trống
    $rooms = [];
    $result = mysqli_query($conn, "
        SELECT
            r.room_id,
            r.room_number,
            rt.type_name,
            rt.price
        FROM rooms r
        JOIN room_types rt
            ON r.room_type_id = rt.room_type_id
        WHERE r.status='Trống'
    ");

    while ($row = mysqli_fetch_assoc($result)) {
        $rooms[] = $row;
    }

    echo json_encode([
        "success" => true,
        "customers" => $customers,
        "rooms" => $rooms
    ], JSON_UNESCAPED_UNICODE);

    exit();
}

/*==========================
= POST
==========================*/

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        $data = $_POST;
    }

    mysqli_begin_transaction($conn);

    try {

        $customer_type = $data['customer_type'];

        //========================
        // Khách mới
        //========================
        if ($customer_type == "new") {

            $full_name = trim($data['full_name']);
            $gender = $data['gender'];
            $phone = trim($data['phone']);
            $email = trim($data['email']);
            $id_card = trim($data['id_card']);
            $address = trim($data['address']);

            if ($full_name == "") {

                echo json_encode([
                    "success" => false,
                    "message" => "Vui lòng nhập họ tên."
                ]);
                exit();
            }

            // kiểm tra CCCD

            $check = mysqli_query($conn,
                "SELECT customer_id
                FROM customers
                WHERE id_card='$id_card'"
            );

            if (mysqli_num_rows($check) > 0) {

                echo json_encode([
                    "success" => false,
                    "message" => "CCCD đã tồn tại."
                ]);
                exit();
            }

            mysqli_query($conn,"
                INSERT INTO customers
                (
                    full_name,
                    gender,
                    phone,
                    email,
                    id_card,
                    address
                )
                VALUES
                (
                    '$full_name',
                    '$gender',
                    '$phone',
                    '$email',
                    '$id_card',
                    '$address'
                )
            ");

            $customer_id = mysqli_insert_id($conn);

        } else {

            $customer_id = $data['customer_id'];

        }

        //====================
        // Booking
        //====================

        $room_id = $data['room_id'];
        $check_in = $data['check_in_date'];
        $check_out = $data['check_out_date'];

        if (strtotime($check_out) <= strtotime($check_in)) {

            echo json_encode([
                "success"=>false,
                "message"=>"Ngày trả phải sau ngày nhận."
            ]);
            exit();
        }

        // Kiểm tra trùng lịch

        $sql = "
        SELECT b.booking_id
        FROM bookings b

        JOIN booking_details bd
            ON b.booking_id=bd.booking_id

        WHERE bd.room_id='$room_id'

        AND b.status IN('Đã đặt','Đang thuê')

        AND (
            b.check_in_date<'$check_out'
            AND
            b.check_out_date>'$check_in'
        )
        ";

        $check = mysqli_query($conn,$sql);

        if(mysqli_num_rows($check)>0){

            echo json_encode([
                "success"=>false,
                "message"=>"Phòng đã được đặt."
            ]);

            exit();
        }

        // lấy giá

        $sql="
        SELECT rt.price

        FROM rooms r

        JOIN room_types rt
        ON r.room_type_id=rt.room_type_id

        WHERE r.room_id='$room_id'
        ";

        $price=mysqli_fetch_assoc(mysqli_query($conn,$sql));

        $days=ceil(
            (strtotime($check_out)-strtotime($check_in))/86400
        );

        if($days<=0){
            $days=1;
        }

        $total=$price['price']*$days;

        mysqli_query($conn,"
            INSERT INTO bookings
            (
                customer_id,
                check_in_date,
                check_out_date,
                total_amount,
                status
            )
            VALUES
            (
                '$customer_id',
                '$check_in',
                '$check_out',
                '$total',
                'Đã đặt'
            )
        ");

        $booking_id=mysqli_insert_id($conn);

        mysqli_query($conn,"
            INSERT INTO booking_details
            (
                booking_id,
                room_id,
                price
            )
            VALUES
            (
                '$booking_id',
                '$room_id',
                '".$price['price']."'
            )
        ");

        mysqli_query($conn,"
            UPDATE rooms
            SET status='Đã đặt'
            WHERE room_id='$room_id'
        ");

        mysqli_commit($conn);

        echo json_encode([
            "success"=>true,
            "message"=>"Đặt phòng thành công.",
            "booking_id"=>$booking_id,
            "total_amount"=>$total
        ],JSON_UNESCAPED_UNICODE);

    } catch(Exception $e){

        mysqli_rollback($conn);

        echo json_encode([
            "success"=>false,
            "message"=>$e->getMessage()
        ]);
    }

    exit();
}

echo json_encode([
    "success"=>false,
    "message"=>"Method không hợp lệ."
]);

?>