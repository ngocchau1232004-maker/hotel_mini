<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hotel Mini Management</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" 
        rel="stylesheet">
    <link rel="stylesheet" href="/hotel_mini/assets/css/header.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
</head>
<body>
    <?php include __DIR__.'/sidebar.php'; ?>

    <div class="content">
        <div class="topbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">

                <button id="toggleSidebar" class="btn btn-outline-secondary me-3">
                    <i class="bi bi-list"></i>
                </button>

                <div>
                    <h4 class="mb-0">
                        🏨 Hệ thống quản lý khách sạn Mini
                    </h4>
                    <small>
                        Xin chào,
                        <b><?= $_SESSION['full_name']; ?></b>
                    </small>
                </div>
            </div>

            <a href="/hotel_mini/logout.php" class="btn btn-danger">
                <i class="bi bi-box-arrow-right"></i>
                Đăng xuất
            </a>
        </div>

        