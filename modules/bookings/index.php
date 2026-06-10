<?php
    include '../../includes/auth.php';
    include '../../config/database.php';

    /** @var mysqli $conn */
    $conn = $conn;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Đặt phòng</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet">
    
</head>

<body>

    <div class="container mt-4">
        <a href="../../dashboard.php"class="btn btn-outline-primary mb-3">
            <i class="bi bi-arrow-left"></i>
            Quay lại danh sách
        </a>

        <h2>Quản lý Đặt phòng</h2>
    </div>

</body>
</html>