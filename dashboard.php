<?php
    include 'includes/auth.php';
    include 'config/database.php';

    $totalRooms = mysqli_fetch_assoc(
        mysqli_query($conn,"SELECT COUNT(*) total FROM rooms")
    );

    $totalCustomers = mysqli_fetch_assoc(
        mysqli_query($conn,"SELECT COUNT(*) total FROM customers")
    );

    $occupiedRooms = mysqli_fetch_assoc(
        mysqli_query($conn,"SELECT COUNT(*) total FROM rooms WHERE status='Đang thuê'")
    );

    $revenue = mysqli_fetch_assoc(
        mysqli_query($conn,"SELECT SUM(total_amount) total FROM invoices")
    );
  
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h2>
                        Xin chào,
                        <?php echo $_SESSION['full_name']; ?>
                    </h2>

                    <a href="logout.php" class="btn btn-danger">
                        Đăng xuất
                    </a>
                </div>

                <hr>
                <h4>Hệ thống quản lý khách sạn mini</h4>

                <div class="list-group mt-3">

                    <a href="modules/room_types/index.php"
                        class="list-group-item list-group-item-action">
                        Quản lý loại phòng
                    </a>

                    <a href="modules/rooms/index.php"
                        class="list-group-item list-group-item-action">
                        Quản lý phòng
                    </a>

                    <a href="modules/customers/index.php"
                        class="list-group-item list-group-item-action">
                        Quản lý khách hàng
                    </a>

                    <a href="modules/bookings/index.php"
                        class="list-group-item list-group-item-action">
                        Quản lý đặt phòng
                    </a>

                    <a href="modules/invoices/index.php"
                        class="list-group-item list-group-item-action">
                        Quản lý hóa đơn
                    </a>

                </div>

                

            </div>
        </div>
    </div>
    
</body>
</html>