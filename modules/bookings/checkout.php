<?php
include '../../includes/auth.php';
include '../../config/database.php';

$id = $_GET['id'];

// Kiểm tra đơn đặt phòng
$sql = "
    SELECT *
    FROM bookings
    WHERE booking_id = '$id'
";

$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) == 0){
    header("Location:index.php");
    exit();
}

$booking = mysqli_fetch_assoc($result);

// Chỉ checkout khi đang thuê
if($booking['status'] == 'Đang thuê'){

    // Cập nhật đơn đặt phòng
    mysqli_query($conn,"
        UPDATE bookings
        SET
            status='Đã trả phòng',
            actual_check_out=NOW()
        WHERE booking_id='$id'
    ");

    // Cập nhật trạng thái phòng
    mysqli_query($conn,"
        UPDATE rooms r
        JOIN booking_details bd
            ON r.room_id = bd.room_id
        SET r.status='Trống'
        WHERE bd.booking_id='$id'
    ");
}

header("Location:index.php");
exit();
?>