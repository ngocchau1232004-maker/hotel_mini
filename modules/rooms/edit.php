<?php
    include '../../includes/auth.php';
    include '../../config/database.php';

    /** @var mysqli $conn */
        $conn = $conn;

    $id = $_GET['id'];

    $sql = "SELECT * FROM rooms WHERE room_id = '$id'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if(isset($_POST['update'])){

        $room_number = $_POST['room_number'];
        $room_type_id = $_POST['room_type_id'];
        $status = $_POST['status'];

        $sql = "UPDATE rooms SET
                    room_number = '$room_number',
                    room_type_id = '$room_type_id',
                    status = '$status'
                WHERE room_id = '$id'";

        mysqli_query($conn, $sql);

        header("Location:index.php");
        exit();
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sửa phòng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body>

    <div class="container mt-4">

        <h2>Sửa phòng</h2>

        <form method="POST">

            <label>Số phòng</label>
            <input type="text"
                name="room_number"
                class="form-control"
                value="<?php echo $row['room_number']; ?>">
            <br>

            <label>Loại phòng</label>
            <select name="room_type_id" class="form-control">

                <?php
                    $sql_type = "SELECT * FROM room_types";
                    $result_type = mysqli_query($conn, $sql_type);

                    while($type = mysqli_fetch_assoc($result_type)){

                        $selected = "";

                        if($type['room_type_id'] == $row['room_type_id']){
                            $selected = "selected";
                        }

                        echo "<option value='".$type['room_type_id']."' $selected>"
                                .$type['type_name'].
                            "</option>";
                    }
                ?>

            </select>
            <br>

            <label>Trạng thái</label>
            <select name="status" class="form-control">

                <option value="Trống"
                    <?php if($row['status']=="Trống") echo "selected"; ?>>
                    Trống
                </option>

                <option value="Đã đặt"
                    <?php if($row['status']=="Đã đặt") echo "selected"; ?>>
                    Đã đặt
                </option>

                <option value="Đang thuê"
                    <?php if($row['status']=="Đang thuê") echo "selected"; ?>>
                    Đang thuê
                </option>

                <option value="Bảo trì"
                    <?php if($row['status']=="Bảo trì") echo "selected"; ?>>
                    Bảo trì
                </option>

            </select>

            <br>

            <div class="d-flex gap-2">
                <button type="submit" name="update" class="btn btn-primary">
                    Cập nhật
                </button>

                <a href="index.php" class="btn btn-secondary">
                    Trở về
                </a>
            </div>

        </form>

    </div>

</body>
</html>