
<?php
    include '../../includes/auth.php';
    include '../../config/database.php';

    if (!isset($_GET['id'])) {
        header("Location: index.php");
        exit();
    }

    $id = intval($_GET['id']);

    // Kiểm tra phòng có đang được sử dụng trong booking_details không
    $check = mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total
        FROM booking_details
        WHERE room_id = $id"
    );

    $row = mysqli_fetch_assoc($check);

    if ($row['total'] > 0) {
        echo "<script>
            alert('Không thể xóa phòng này vì phòng đã có lịch sử đặt phòng!');
            window.location.href = 'index.php';
        </script>";
        exit();
    }

    // Nếu phòng chưa từng được đặt thì cho phép xóa
    $delete = mysqli_query(
        $conn,
        "DELETE FROM rooms
        WHERE room_id = $id"
    );

    if ($delete) {
        echo "<script>
            alert('Xóa phòng thành công!');
            window.location.href = 'index.php';
        </script>";
    } else {
        echo "<script>
            alert('Xóa phòng thất bại!');
            window.location.href = 'index.php';
        </script>";
    }

?>