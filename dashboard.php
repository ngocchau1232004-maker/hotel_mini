<?php
    include 'includes/auth.php';
    include 'config/database.php';
    include 'includes/header.php';


    /** @var mysqli $conn */
    $conn = $conn;

    // Tổng phòng
    $totalRooms = mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT COUNT(*) total FROM rooms"
    ));

    // Phòng trống
    $emptyRooms = mysqli_fetch_assoc(
        mysqli_query($conn,"SELECT COUNT(*) total FROM rooms WHERE status='Trống'")
    );

    // Đang thuê
    $occupiedRooms = mysqli_fetch_assoc(
        mysqli_query($conn,"SELECT COUNT(*) total FROM rooms WHERE status='Đang thuê'")
    );

    // Khách hàng
    $totalCustomers = mysqli_fetch_assoc(
        mysqli_query($conn,"SELECT COUNT(*) total FROM customers")
    );

    // Booking
    $totalBookings = mysqli_fetch_assoc(
        mysqli_query($conn,"SELECT COUNT(*) total FROM bookings")
    );

    // Doanh thu
    $revenue = mysqli_fetch_assoc(
        mysqli_query($conn,"SELECT IFNULL(SUM(total_amount),0) total FROM invoices")
    );
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3">
            <div class="card stat-card bg-primary">
                <div class="card-body">
                    <h5>Tổng phòng</h5>
                    <h2> <?= $totalRooms['total'] ?> </h2>
                    <i class="bi bi-door-open fs-1"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card bg-success">
                <div class="card-body">
                    <h5>Phòng trống</h5>
                    <h2> <?= $emptyRooms['total'] ?> </h2>
                    <i class="bi bi-house-check fs-1"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card bg-warning">
                <div class="card-body">
                    <h5>Đang thuê</h5>
                    <h2> <?= $occupiedRooms['total'] ?> </h2>
                    <i class="bi bi-person-workspace fs-1"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card bg-danger">
                <div class="card-body">
                    <h5>Khách hàng</h5>
                    <h2> <?= $totalCustomers['total'] ?> </h2>
                    <i class="bi bi-people fs-1"></i>
                </div>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Tổng Booking
                </div>

                <div class="card-body">
                    <h1> <?= $totalBookings['total'] ?> </h1>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Tổng doanh thu
                </div>

                <div class="card-body">
                    <h1 class="text-danger">
                        <?= number_format($revenue['total']) ?> đ
                    </h1>
                </div>
            </div>
        </div>
    </div>

    <br>

    <div class="card">
        <div class="card-header">
            Biểu đồ doanh thu
        </div>

        <div class="card-body">
            <canvas id="myChart"></canvas>
        </div>
    </div>

    <script>
        const ctx=document.getElementById('myChart');
        new Chart(ctx,{

            type:'bar',

            data:{
                labels:['Doanh thu'],

                datasets:[{
                    label:'VNĐ',
                    data:[<?= $revenue['total']?>]
                }]
            }
        });
    </script>

<?php include 'includes/footer.php'; ?>