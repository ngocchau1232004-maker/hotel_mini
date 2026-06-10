<?php
    include '../../config/database.php';

    /** @var mysqli $conn */ 
    $conn = $conn; 

    $id = $_GET['id'];
    $result = mysqli_query($conn,"SELECT * FROM room_types WHERE room_type_id=$id");
    $data = mysqli_fetch_assoc($result);

    if(isset($_POST['update'])){

        $type_name = $_POST['type_name'];
        $price = $_POST['price'];
        $max_people = $_POST['max_people'];
        $description = $_POST['description'];

        $sql = "UPDATE room_types 
                SET type_name='$type_name',price='$price', max_people='$max_people', description='$description'
                WHERE room_type_id=$id";

        mysqli_query($conn,$sql);

        header("Location:index.php");
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sửa loại phòng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>

<body>

    <div class="container mt-4">
        <form method="POST">
            <h2>Sửaloại phòng</h2>

            <label>Tên loại phòng</label>
            <input type="text" name="type_name" class="form-control" value="<?= $data['type_name']; ?>">
            <br>

            <label>Giá</label>
            <div class="input-group">
                <input type="number" name="price" class="form-control"  step="10000" value="<?= $data['price']; ?>">
                <span class="input-group-text">VNĐ</span>
            </div><br>

            <label>Số người</label>
            <input type="number" name="max_people"class="form-control" min="0" value="<?= $data['max_people']; ?>">
            <br>

            <label>Mô tả</label>
            <textarea name="description" class="form-control"><?= $data['description']; ?></textarea>
            <br>

            <button name="update" class="btn btn-success">Cập nhật</button>

        </form>
    </div>

   
</body>
</html>