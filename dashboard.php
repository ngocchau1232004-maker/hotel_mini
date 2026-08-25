<?php

include 'includes/auth.php';
include 'config/database.php';
include 'includes/header.php';

/** @var mysqli $conn */

// ==============================
// THỐNG KÊ
// ==============================

// Tổng phòng
$totalRooms = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total FROM rooms"
    )
);

// Phòng trống
$emptyRooms = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total
         FROM rooms
         WHERE status = 'Trống'"
    )
);

// Đang thuê
$occupiedRooms = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total
         FROM rooms
         WHERE status = 'Đang thuê'"
    )
);

// Khách hàng
$totalCustomers = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total
         FROM customers"
    )
);

// Booking
$totalBookings = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total
         FROM bookings"
    )
);

// Tổng doanh thu
$revenue = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT IFNULL(SUM(total_amount), 0) AS total
         FROM invoices"
    )
);


// ==============================
// DOANH THU THEO THÁNG
// ==============================

$monthlyRevenueQuery = mysqli_query(
    $conn,
    "SELECT
        MONTH(invoice_date) AS month,
        SUM(total_amount) AS total
     FROM invoices
     WHERE YEAR(invoice_date) = YEAR(CURDATE())
     GROUP BY MONTH(invoice_date)
     ORDER BY MONTH(invoice_date)"
);

// Mảng doanh thu theo tháng
$monthlyRevenue = [];

while ($row = mysqli_fetch_assoc($monthlyRevenueQuery)) {
    $monthlyRevenue[(int)$row['month']] = (float)$row['total'];
}

?>

<div class="container-fluid">

    <!-- ============================== -->
    <!-- THỐNG KÊ -->
    <!-- ============================== -->

    <div class="row">

        <!-- Tổng phòng -->
        <div class="col-md-3">
            <div class="card stat-card bg-primary text-white">
                <div class="card-body">

                    <h5>Tổng phòng</h5>

                    <h2>
                        <?= $totalRooms['total'] ?>
                    </h2>

                    <i class="bi bi-door-open fs-1"></i>

                </div>
            </div>
        </div>


        <!-- Phòng trống -->
        <div class="col-md-3">
            <div class="card stat-card bg-success text-white">
                <div class="card-body">

                    <h5>Phòng trống</h5>

                    <h2>
                        <?= $emptyRooms['total'] ?>
                    </h2>

                    <i class="bi bi-house-check fs-1"></i>

                </div>
            </div>
        </div>


        <!-- Đang thuê -->
        <div class="col-md-3">
            <div class="card stat-card bg-warning text-dark">
                <div class="card-body">

                    <h5>Đang thuê</h5>

                    <h2>
                        <?= $occupiedRooms['total'] ?>
                    </h2>

                    <i class="bi bi-person-workspace fs-1"></i>

                </div>
            </div>
        </div>


        <!-- Khách hàng -->
        <div class="col-md-3">
            <div class="card stat-card bg-danger text-white">
                <div class="card-body">

                    <h5>Khách hàng</h5>

                    <h2>
                        <?= $totalCustomers['total'] ?>
                    </h2>

                    <i class="bi bi-people fs-1"></i>

                </div>
            </div>
        </div>

    </div>


    <br>


    <!-- ============================== -->
    <!-- BOOKING + DOANH THU -->
    <!-- ============================== -->

    <div class="row">

        <!-- Tổng Booking -->
        <div class="col-md-6">

            <div class="card">

                <div class="card-header">
                    Tổng Booking
                </div>

                <div class="card-body">

                    <h1>
                        <?= $totalBookings['total'] ?>
                    </h1>

                </div>

            </div>

        </div>


        <!-- Tổng doanh thu -->
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


    <!-- ============================== -->
    <!-- BIỂU ĐỒ DOANH THU -->
    <!-- ============================== -->

    <div class="card">
        <div class="card-header">
            <strong>
                Biểu đồ doanh thu năm <?= date('Y') ?>
            </strong>
        </div>

        <div class="card-body">
            <div class="chart-container">
                <canvas id="myChart"></canvas>
            </div>
        </div>
    </div>
    
    <style>
        .chart-container {
            position: relative;
            width: 100%;
            height: 300px !important;
            max-height: 300px !important;
        }

        #myChart {
            width: 100% !important;
            height: 300px !important;
            max-height: 300px !important;
        }
    </style>

</div>


<script>
    const ctx = document.getElementById('myChart');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [
                'Tháng 1',
                'Tháng 2',
                'Tháng 3',
                'Tháng 4',
                'Tháng 5',
                'Tháng 6',
                'Tháng 7',
                'Tháng 8',
                'Tháng 9',
                'Tháng 10',
                'Tháng 11',
                'Tháng 12'
            ],

            datasets: [{
                label: 'Doanh thu (VNĐ)',

                data: [
                    <?= $monthlyRevenue[1] ?? 0 ?>,
                    <?= $monthlyRevenue[2] ?? 0 ?>,
                    <?= $monthlyRevenue[3] ?? 0 ?>,
                    <?= $monthlyRevenue[4] ?? 0 ?>,
                    <?= $monthlyRevenue[5] ?? 0 ?>,
                    <?= $monthlyRevenue[6] ?? 0 ?>,
                    <?= $monthlyRevenue[7] ?? 0 ?>,
                    <?= $monthlyRevenue[8] ?? 0 ?>,
                    <?= $monthlyRevenue[9] ?? 0 ?>,
                    <?= $monthlyRevenue[10] ?? 0 ?>,
                    <?= $monthlyRevenue[11] ?? 0 ?>,
                    <?= $monthlyRevenue[12] ?? 0 ?>
                ]
            }]

        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('vi-VN')
                                .format(value) + ' đ';
                        }
                    }
                }
            },

            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return new Intl.NumberFormat('vi-VN')
                                .format(context.raw) + ' đ';
                        }
                    }
                }
            }
        }
    });

</script>


<?php include 'includes/footer.php'; ?>