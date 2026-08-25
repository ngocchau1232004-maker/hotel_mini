<?php
// Chỉ cho phép tài khoản Admin truy cập khu vực quản lý tài khoản.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: /hotel_mini/login.php");
    exit();
}

if (!isset($_SESSION['role_id'])) {
    require_once __DIR__ . '/../config/database.php';

    $user_id = (int) $_SESSION['user_id'];
    $stmt = mysqli_prepare($conn, "SELECT role_id FROM users WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    $_SESSION['role_id'] = (int)($user['role_id'] ?? 0);
}

if ((int)$_SESSION['role_id'] !== 1) {
    http_response_code(403);
    echo '<!DOCTYPE html><html lang="vi"><head><meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Không có quyền truy cập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head><body class="bg-light"><div class="container mt-5">
    <div class="alert alert-danger"><h4>Không có quyền truy cập</h4>
    <p>Chức năng này chỉ dành cho tài khoản Admin.</p>
    <a href="/hotel_mini/dashboard.php" class="btn btn-primary">Về trang chủ</a>
    </div></div></body></html>';
    exit();
}
?>