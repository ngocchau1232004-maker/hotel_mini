<?php
include '../../includes/auth.php';
include '../../config/database.php';

if(isset($_POST['save'])){

    $customer_id = $_POST['customer_id'];
    $room_id = $_POST['room_id'];
    $check_in = $_POST['check_in_date'];
    $check_out = $_POST['check_out_date'];

    // Lấy giá phòng
    $price_sql = "
        SELECT rt.price
        FROM rooms r
        JOIN room_types rt
            ON r.room_type_id = rt.room_type_id
        WHERE r.room_id = '$room_id'
    ";

    $price_result = mysqli_query($conn, $price_sql);
    $price_row = mysqli_fetch_assoc($price_result);

    $price = $price_row['price'];

    // Tính số ngày
    $days = ceil(
        (strtotime($check_out) - strtotime($check_in))
        / 86400
    );

    if($days <= 0){
        $days = 1;
    }

    $total = $price * $days;

    // Thêm booking
    $sql = "
        INSERT INTO bookings(
            customer_id,
            check_in_date,
            check_out_date,
            total_amount,
            status
        )
        VALUES(
            '$customer_id',
            '$check_in',
            '$check_out',
            '$total',
            'Đã xác nhận'
        )
    ";

    mysqli_query($conn, $sql);

    $booking_id = mysqli_insert_id($conn);

    // Thêm chi tiết phòng
    mysqli_query($conn,"
        INSERT INTO booking_details(
            booking_id,
            room_id,
            price
        )
        VALUES(
            '$booking_id',
            '$room_id',
            '$price'
        )
    ");

    // Đổi trạng thái phòng
    mysqli_query($conn,"
        UPDATE rooms
        SET status='Đã đặt'
        WHERE room_id='$room_id'
    ");

    header("Location:index.php");
    exit();
}

// Khách hàng
$customers = mysqli_query($conn,"
    SELECT *
    FROM customers
    ORDER BY full_name
");

// Phòng trống
$rooms = mysqli_query($conn,"
    SELECT
        r.room_id,
        r.room_number,
        rt.type_name,
        rt.price
    FROM rooms r
    JOIN room_types rt
        ON r.room_type_id = rt.room_type_id
    WHERE r.status='Trống'
");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Thêm đặt phòng</title>

    <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body>

<div class="container mt-4">

    <h2>Thêm đặt phòng</h2>

    <form method="POST">

        <div class="mb-3">
            <label>Khách hàng</label>

            <select name="customer_id"
                    class="form-control"
                    required>

                <option value="">
                    -- Chọn khách hàng --
                </option>

                <?php while($c = mysqli_fetch_assoc($customers)){ ?>

                    <option value="<?= $c['customer_id']; ?>">
                        <?= $c['full_name']; ?>
                    </option>

                <?php } ?>

            </select>
        </div>

        <div class="mb-3">
            <label>Phòng</label>

            <select name="room_id"
                    class="form-control"
                    required>

                <option value="">
                    -- Chọn phòng --
                </option>

                <?php while($r = mysqli_fetch_assoc($rooms)){ ?>

                    <option value="<?= $r['room_id']; ?>">

                        Phòng <?= $r['room_number']; ?>
                        -
                        <?= $r['type_name']; ?>
                        -
                        <?= number_format($r['price']); ?> VNĐ

                    </option>

                <?php } ?>

            </select>
        </div>

        <div class="mb-3">
            <label>Ngày nhận phòng</label>

            <input type="date"
                   name="check_in_date"
                   class="form-control"
                   required>
        </div>

        <div class="mb-3">
            <label>Ngày trả phòng</label>

            <input type="date"
                   name="check_out_date"
                   class="form-control"
                   required>
        </div>

        <button type="submit"
                name="save"
                class="btn btn-primary">

            Lưu

        </button>

        <a href="index.php"
           class="btn btn-secondary">

            Quay lại

        </a>

    </form>

</div>

</body>
</html>