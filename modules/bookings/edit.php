<?php
    include '../../includes/auth.php';
    include '../../config/database.php';

    if(isset($_POST['save'])){

        $customer_id = $_POST['customer_id'];
        $room_id = $_POST['room_id'];
        $check_in = $_POST['check_in_date'];
        $check_out = $_POST['check_out_date'];

        $price_sql = "
            SELECT rt.price
            FROM rooms r
            JOIN room_types rt
            ON r.room_type_id = rt.room_type_id
            WHERE r.room_id='$room_id'
        ";

        $price_result = mysqli_query($conn,$price_sql);
        $price = mysqli_fetch_assoc($price_result);

        $days = max(1,
            (strtotime($check_out) -
            strtotime($check_in)) / 86400);

        $total = $days * $price['price'];

        mysqli_query($conn,"
            INSERT INTO bookings(
                customer_id,
                check_in_date,
                check_out_date,
                total_amount
            )
            VALUES(
                '$customer_id',
                '$check_in',
                '$check_out',
                '$total'
            )
        ");

        $booking_id = mysqli_insert_id($conn);

        mysqli_query($conn,"
            INSERT INTO booking_details(
                booking_id,
                room_id,
                price
            )
            VALUES(
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

        header("Location:index.php");
    }
?>