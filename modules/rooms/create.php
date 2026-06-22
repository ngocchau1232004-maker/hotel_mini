<?php
    include '../../includes/auth.php';
    include '../../config/database.php';

    /** @var mysqli $conn */
    $conn = $conn;

    if(isset($_POST['save'])){

        $room_number = $_POST['room_number'];
        $room_type_id = $_POST['room_type_id'];
        $status = $_POST['status'];

        $sql = "INSERT INTO rooms(room_number,room_type_id,status)
                VALUES('$room_number','$room_type_id','$status')";

        mysqli_query($conn,$sql);

        header("Location:index.php");
        exit();
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Thêm loại phòng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
            rel="stylesheet">

</head>

<body>

    <div class="container mt-4">
        <h2>Thêm phòng</h2>

        <form method="POST">
            <label>Số phòng</label>
            <input type="text" name="room_number" class="form-control">
            <br>

            <label>Loại phòng</label>
            <select name="room_type_id" class="form-control">
                <?php
                $sql = "SELECT * FROM room_types";
                $result = mysqli_query($conn, $sql);

                while($row = mysqli_fetch_assoc($result)){
                    echo "<option value= '".$row['room_type_id']."' > ".$row['type_name']." </option>";
                }
                ?>
            </select>
            <br>

            <label>Trạng thái</label>
            <select name="status" class="form-control">
                <option value="Trống">Trống</option>
                <option value="Đã đặt">Đã đặt</option>
                <option value="Đang thuê">Đang thuê</option>
                <option value="Bảo trì">Đang bảo trì</option>
            </select>
            <br>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success" name="save">
                    Thêm
                </button>

                <a href="index.php" class="btn btn-secondary">
                    Trở về
                </a>
            </div>


        </form>

    </div>

</body>
</html>