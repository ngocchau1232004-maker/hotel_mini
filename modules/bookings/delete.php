<?php
    include '../../includes/auth.php';
    include '../../config/database.php';

    /** @var mysqli $conn */
    $conn = $conn;

    $id = intval($_GET['id']);

    // Lấy trạng thái booking
    $result = mysqli_query($conn,"SELECT status
                                    FROM bookings 
                                    WHERE booking_id='$id'");

    if(mysqli_num_rows($result) == 0){
        header("Location:index.php?error=notfound");
        exit();
    }

    $row = mysqli_fetch_assoc($result);

    // Không cho xóa booking đang thuê hoặc đã trả phòng
    if($row['status'] == 'Đang thuê' || $row['status'] == 'Đã trả phòng'){
        header("Location:index.php?error=delete_error");
        exit();
    }

    // Trả phòng về trạng thái Trống
    $rooms = mysqli_query($conn,"SELECT room_id
                                FROM booking_details
                                WHERE booking_id='$id'");

    while($room = mysqli_fetch_assoc($rooms)){
        mysqli_query($conn,"UPDATE rooms SET status='Trống'
                            WHERE room_id='".$room['room_id']."'");
    }

    // Xóa booking
    // booking_details sẽ tự xóa nhờ ON DELETE CASCADE
    mysqli_query($conn,"DELETE FROM bookings WHERE booking_id='$id'");
    
    header("Location:index.php?success=delete");
    exit();
?>