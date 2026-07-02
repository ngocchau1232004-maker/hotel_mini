<?php
    include '../../includes/auth.php';
    include '../../config/database.php';
    include '../../includes/header.php';

    /** @var mysqli $conn */
    $conn = $conn;

    $id = $_GET['id'];
    $sql = "SELECT * FROM customers WHERE customer_id = '$id'";

    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if(isset($_POST['update'])){

        $full_name = $_POST['full_name'];
        $gender = $_POST['gender'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $id_card = $_POST['id_card'];
        $address = $_POST['address'];

        $sql = "UPDATE customers SET
                    full_name = '$full_name',
                    gender = '$gender',
                    phone = '$phone',
                    email = '$email',
                    id_card = '$id_card',
                    address = '$address'
                WHERE customer_id = '$id'";

        mysqli_query($conn, $sql);

        header("Location:index.php");
        exit();
    }
?>



    <div class="container mt-4">

        <h2>Sửa khách hàng</h2>

        <form method="POST">

            <label>Họ và tên</label>
            <input type="text"
                name="full_name"
                class="form-control"
                value="<?php echo $row['full_name']; ?>"
                required>
            <br>

            <label>Giới tính</label>
            <select name="gender" class="form-control">
                <option value="Nam"
                    <?php if($row['gender']=="Nam") echo "selected"; ?>>
                    Nam
                </option>

                <option value="Nữ"
                    <?php if($row['gender']=="Nữ") echo "selected"; ?>>
                    Nữ
                </option>

                <option value="Khác"
                    <?php if($row['gender']=="Khác") echo "selected"; ?>>
                    Khác
                </option>
            </select>
            <br>

            <label>Số điện thoại</label>
            <input type="text"
                name="phone"
                class="form-control"
                value="<?php echo $row['phone']; ?>">
            <br>

            <label>Email</label>
            <input type="email"
                name="email"
                class="form-control"
                value="<?php echo $row['email']; ?>">
            <br>

            <label>CCCD/CMND</label>
            <input type="text"
                name="id_card"
                class="form-control"
                value="<?php echo $row['id_card']; ?>">
            <br>

            <label>Địa chỉ</label>
            <textarea name="address"
                    class="form-control"
                    rows="3"><?php echo $row['address']; ?></textarea>
            <br>

            <div class="d-flex gap-2">
                <button type="submit"
                        name="update"
                        class="btn btn-primary">
                    Cập nhật
                </button>

                <a href="index.php"
                class="btn btn-secondary">
                    Trở về
                </a>
            </div>

        </form>

    </div>

<?php include __DIR__.'/../../includes/footer.php'; ?>