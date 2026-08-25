<?php
    include '../../includes/auth.php';
    include '../../config/database.php';

    if (!isset($_GET['id'])) {
        header("Location: index.php");
        exit();
    }

    $id = intval($_GET['id']);

    // Kiểm tra loại phòng có đang được sử dụng trong bảng rooms không
    $check = mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total
        FROM rooms
        WHERE room_type_id = $id"
    );

    $row = mysqli_fetch_assoc($check);

    if ($row['total'] > 0) {
        echo "<script>
            alert('Không thể xóa loại phòng này vì đang có {$row['total']} phòng sử dụng!');
            window.location.href = 'index.php';
        </script>";
        exit();
    }

    // Nếu không có phòng nào sử dụng thì cho phép xóa
    $delete = mysqli_query(
        $conn,
        "DELETE FROM room_types
        WHERE room_type_id = $id"
    );

    if ($delete) {
        echo "<script>
            alert('Xóa loại phòng thành công!');
            window.location.href = 'index.php';
        </script>";
    } else {
        echo "<script>
            alert('Xóa loại phòng thất bại!');
            window.location.href = 'index.php';
        </script>";
    }

?>