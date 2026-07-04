<?php
/**
 * ==========================================================
 * HOTEL MINI API AUTH
 * File: api/auth.php
 * ==========================================================
 */

require_once __DIR__ . '/config.php';

/*
|--------------------------------------------------------------------------
| Lấy tất cả Header (nếu cần dùng sau này)
|--------------------------------------------------------------------------
*/
function getRequestHeaders()
{
    if (function_exists('getallheaders')) {
        return getallheaders();
    }

    $headers = [];

    foreach ($_SERVER as $name => $value) {
        if (substr($name, 0, 5) == 'HTTP_') {

            $key = str_replace(
                ' ',
                '-',
                ucwords(
                    strtolower(
                        str_replace('_', ' ', substr($name, 5))
                    )
                )
            );

            $headers[$key] = $value;
        }
    }

    return $headers;
}

/*
|--------------------------------------------------------------------------
| KHÔNG KIỂM TRA API KEY
|--------------------------------------------------------------------------
|
| Đã bỏ để dễ test bằng Postman.
| Sau này nếu cần bảo mật thì thêm lại.
|
*/

/*
|--------------------------------------------------------------------------
| Lấy User theo Username
|--------------------------------------------------------------------------
*/
function getUser($username)
{
    global $conn;

    $username = str($username);

    $sql = "
        SELECT *
        FROM users
        WHERE username='$username'
        LIMIT 1
    ";

    $query = mysqli_query($conn, $sql);

    if (!$query) {
        return null;
    }

    if (mysqli_num_rows($query) == 0) {
        return null;
    }

    return mysqli_fetch_assoc($query);
}

/*
|--------------------------------------------------------------------------
| Kiểm tra đăng nhập
|--------------------------------------------------------------------------
*/
function checkLogin($username, $password)
{
    $user = getUser($username);

    if (!$user) {
        return false;
    }

    // password_hash()
    if (
        !empty($user['password']) &&
        password_verify($password, $user['password'])
    ) {
        return $user;
    }

    // md5()
    if (
        !empty($user['password']) &&
        $user['password'] === md5($password)
    ) {
        return $user;
    }

    // Plain text
    if (
        !empty($user['password']) &&
        $user['password'] === $password
    ) {
        return $user;
    }

    return false;
}

/*
|--------------------------------------------------------------------------
| Lấy Bearer Token (để dành)
|--------------------------------------------------------------------------
*/
function bearerToken()
{
    $headers = getRequestHeaders();

    if (!isset($headers['Authorization'])) {
        return "";
    }

    if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
        return $matches[1];
    }

    return "";
}

/*
|--------------------------------------------------------------------------
| Trả thông tin User
|--------------------------------------------------------------------------
*/
function userResponse($user)
{
    return [
        "user_id"   => (int)$user['user_id'],
        "full_name" => $user['full_name'],
        "username"  => $user['username'],
        "role_id"   => (int)$user['role_id']
    ];
}