<?php

    //* |===========================================================
    //* | HOTEL MINI API REGISTER
    //* | File: api/register.php
    //* | URL: http://localhost/hotel_mini/register.php
    //* |===========================================================


    require_once __DIR__ . '/auth.php';

    // Hỗ trợ GET và POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $data = getJson();

        $username = isset($data['username']) ? trim($data['username']) : "";
        $password = isset($data['password']) ? trim($data['password']) : "";

    } elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {

        $username = isset($_GET['username']) ? trim($_GET['username']) : "";
        $password = isset($_GET['password']) ? trim($_GET['password']) : "";

    } else {

        error("Method không được hỗ trợ.", 405);

    }

    // Kiểm tra dữ liệu
    if ($username == "" || $password == "") {
        error("Thiếu username hoặc password.");
    }

    // Kiểm tra đăng nhập
    $user = checkLogin($username, $password);

    if (!$user) {
        error("Sai tài khoản hoặc mật khẩu.", 401);
    }

    // Trả kết quả
    success([
        "user" => userResponse($user)
    ], "Đăng nhập thành công");

?>