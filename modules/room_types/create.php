<?php
    include '../../config/database.php';

    /** @var mysqli $conn */
    $conn = $conn;

    if(isset($_POST['save'])){

        $type_name = $_POST['type_name'];
        $price = $_POST['price'];
        $max_people = $_POST['max_people'];
        $description = $_POST['description'];

        $sql = "INSERT INTO room_types(
                type_name,
                price,
                max_people,
                description
                )
                VALUES(
                '$type_name',
                '$price',
                '$max_people',
                '$description'
                )";

        mysqli_query($conn,$sql);

        header("Location:index.php");
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
        <h2>Thêm loại phòng</h2>

        <form method="POST">
            <label>Tên loại phòng</label>
            <input type="text" name="type_name" class="form-control">
            <br>

            
            <label>Giá</label>
            <div class="input-group">
                <input type="number" name="price" class="form-control" min="0"step="10000"value="100000">
                <span class="input-group-text">VNĐ</span>
                
            </div><br>

            <label>Số người</label>
            <input type="number" name="max_people" class="form-control">
            <br>

            <label>Mô tả</label>
            <textarea name="description" class="form-control"></textarea>
            <br>

            <button class="btn btn-success" name="save"> Lưu </button>

        </form>

    </div>

</body>
</html>