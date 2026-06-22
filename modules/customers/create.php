<?php
    include '../../includes/auth.php';
    include '../../config/database.php';

    /** @var mysqli $conn */
    $conn = $conn;

    if(isset($_POST['save'])){

        $full_name = $_POST['full_name'];
        $gender = $_POST['gender'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $id_card = $_POST['id_card'];
        $address = $_POST['address'];

        $sql = "INSERT INTO customers(full_name, gender, phone, email, id_card, address)
                    VALUES('$full_name','$gender','$phone','$email','$id_card','$address')";

        mysqli_query($conn, $sql);

        header("Location:index.php");
        exit();
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Thêm khách hàng</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body>

    <div class="container mt-4">

        <h2>Thêm khách hàng</h2>

        <form method="POST">

            <label>Họ và tên</label>
            <input type="text"
                name="full_name"
                class="form-control"
                required>
            <br>

            <label>Giới tính</label>
            <select name="gender" class="form-control">
                <option value="Nam">Nam</option>
                <option value="Nữ">Nữ</option>
                <option value="Khác">Khác</option>
            </select>
            <br>

            <label>Số điện thoại</label>
            <input type="text"
                name="phone"
                class="form-control">
            <br>

            <label>Email</label>
            <input type="email"
                name="email"
                class="form-control">
            <br>

            <label>CCCD/CMND</label>
            <input type="text"
                name="id_card"
                class="form-control">
            <br>

            <label>Địa chỉ</label>
            <textarea name="address"
                    class="form-control"
                    rows="3"></textarea>
            <br>

            <div class="d-flex gap-2">
                <button type="submit"
                        name="save"
                        class="btn btn-success">
                    Thêm
                </button>

                <a href="index.php"
                class="btn btn-secondary">
                    Trở về
                </a>
            </div>

        </form>

    </div>

</body>
</html>