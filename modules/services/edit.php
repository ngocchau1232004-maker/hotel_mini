<?php
    include '../../includes/auth.php';
    include '../../config/database.php';

    /** @var mysqli $conn */
    $conn = $conn;

    $id = $_GET['id'];

    $result = mysqli_query($conn,"SELECT * FROM services WHERE service_id=$id");

    $data = mysqli_fetch_assoc($result);

    if(isset($_POST['update'])){

        $service_name = $_POST['service_name'];
        $price = $_POST['price'];
        $description = $_POST['description'];

        $sql = "UPDATE services
                SET
                    service_name='$service_name',
                    price='$price',
                    description='$description'
                WHERE service_id=$id";

        mysqli_query($conn,$sql);

        header("Location:index.php");
    }
?>

<!DOCTYPE html>
<html>
<head>

    <title>Sửa dịch vụ</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>

<body>

    <div class="container mt-4">
        <form method="POST">

            <h2>Sửa dịch vụ</h2>

            <label>Tên dịch vụ</label>
            <input type="text" name="service_name" class="form-control"
                value="<?= $data['service_name']; ?>">
            <br>

            <label>Giá</label>
            <div class="input-group">
                <input type="number" name="price" class="form-control"
                    step="1000" value="<?= $data['price']; ?>">
                <span class="input-group-text"> VNĐ </span>
            </div>
            <br>

            <label>Mô tả</label>
            <textarea name="description"
                class="form-control"><?= $data['description']; ?></textarea>
            <br>

            <button class="btn btn-success" name="update"> Cập nhật </button>
            <a href="index.php" class="btn btn-secondary"> Trở về </a>

        </form>
    </div>

</body>
</html>