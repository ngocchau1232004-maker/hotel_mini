<?php
    include '../../includes/auth.php';
    include '../../config/database.php';

    /** @var mysqli $conn */
    $conn = $conn;

    if(isset($_POST['save'])){

        $service_name = $_POST['service_name'];
        $price = $_POST['price'];
        $description = $_POST['description'];

        $sql = "INSERT INTO services(service_name,price,description)
                VALUES('$service_name','$price','$description')";

        mysqli_query($conn,$sql);

        header("Location:index.php");
    }
?>

<!DOCTYPE html>
<html>
<head>

    <title>Thêm dịch vụ</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>

<body>

    <div class="container mt-4">

        <h2>Thêm dịch vụ</h2>

        <form method="POST">
            <label>Tên dịch vụ</label>
            <input type="text" name="service_name" class="form-control">
            <br>

            <label>Giá</label>
            <div class="input-group">
                <input type="number" name="price" class="form-control"
                        min="0" step="1000" value="10000">
                <span class="input-group-text">VNĐ</span>
            </div>
            <br>

            <label>Mô tả</label>
            <textarea name="description" class="form-control"></textarea>
            <br>

            <div class="d-flex gap-2">
                <button class="btn btn-success" name="save">Thêm</button>
                <a href="index.php" class="btn btn-secondary"> Trở về </a>
            </div>

        </form>
    </div>

</body>
</html>