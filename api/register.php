<?php
    
    //* |======================================================================
    //* | HOTEL MINI API REGISTER
    //* | File: api/register.php
    //* | URL: http://localhost/hotel_mini/register.php
    //* |====================================================================== 

    require_once __DIR__ . '/auth.php';

    // Hỗ trợ GET và POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $data = getJson();

        $full_name = isset($data['full_name']) ? str($data['full_name']) : "";
        $username = isset($data['username']) ? str($data['username']) : "";
        $password = isset($data['password']) ? trim($data['password']) : "";
        $confirm_password = isset($data['confirm_password']) ? trim($data['confirm_password']) : "";

    } elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {

        $full_name = isset($_GET['full_name']) ? str($_GET['full_name']) : "";
        $username = isset($_GET['username']) ? str($_GET['username']) : "";
        $password = isset($_GET['password']) ? trim($_GET['password']) : "";
        $confirm_password = isset($_GET['confirm_password']) ? trim($_GET['confirm_password']) : "";

    } else {

        error("Method không được hỗ trợ.",405);

    }

    // Validate
    if (
        $full_name == "" ||
        $username == "" ||
        $password == "" ||
        $confirm_password == ""
    ) {
        error("Vui lòng nhập đầy đủ thông tin.");
    }

    if (strlen($username) < 4) {
        error("Tên đăng nhập phải từ 4 ký tự.");
    }

    if (strlen($password) < 6) {
        error("Mật khẩu phải từ 6 ký tự.");
    }

    if ($password != $confirm_password) {
        error("Mật khẩu xác nhận không khớp.");
    }

    // Kiểm tra username
    $sql = "SELECT user_id
            FROM users
            WHERE username='$username'
            LIMIT 1";

    $query = mysqli_query($conn,$sql);

    if(!$query){
        error(mysqli_error($conn),500);
    }

    if(mysqli_num_rows($query)>0){
        error("Tên đăng nhập đã tồn tại.");
    }

    // Thêm User
    $password = md5($password);

    $sql = "INSERT INTO users(
                full_name,
                username,
                password,
                role_id
            )
            VALUES(
                '$full_name',
                '$username',
                '$password',
                2
            )";

    if(!mysqli_query($conn,$sql)){
        error(mysqli_error($conn),500);
    }

    $id = mysqli_insert_id($conn);

    // Lấy User vừa tạo
    $sql = "SELECT
                user_id,
                full_name,
                username,
                role_id
            FROM users
            WHERE user_id='$id'
            LIMIT 1";

    $query = mysqli_query($conn,$sql);

    $user = mysqli_fetch_assoc($query);

    success(
        [
            "user"=>$user
        ],
        "Đăng ký thành công."
    );

?>