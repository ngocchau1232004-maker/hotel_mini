<?php
    include '../../includes/auth.php';
    include '../../config/database.php';

    $id = $_GET['id'];

    // Lấy danh sách phòng của booking
    $rooms = mysqli_query($conn,"SELECT room_id FROM booking_details WHERE booking_id='$id' ");

    while($room=mysqli_fetch_assoc($rooms)){
        mysqli_query($conn,"UPDATE rooms SET status='Trống' 
                            WHERE room_id='".$room['room_id']."' ");
    }

    // Xóa booking
    // booking_details sẽ tự xóa do ON DELETE CASCADE
    mysqli_query($conn,"DELETE FROM bookings WHERE booking_id='$id' ");

    header("Location:index.php");
    exit();
?>