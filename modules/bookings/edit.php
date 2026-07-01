<?php
    include '../../includes/auth.php';
    include '../../config/database.php';

    $id = $_GET['id'];

    if(isset($_POST['update'])){

        $customer_id = $_POST['customer_id'];
        $room_id      = $_POST['room_id'];
        $check_in     = $_POST['check_in_date'];
        $check_out    = $_POST['check_out_date'];
        $status       = $_POST['status'];
        $note         = $_POST['note'];

        // Kiểm tra phòng đã được đặt trong khoảng thời gian này chưa
        $check_sql = "SELECT b.booking_id FROM bookings b JOIN booking_details bd
            ON b.booking_id = bd.booking_id
            WHERE bd.room_id = '$room_id'
            AND b.booking_id <> '$id'
            AND b.status IN ('Đã đặt','Đang thuê')
            AND (b.check_in_date < '$check_out' AND b.check_out_date > '$check_in')";

        $check_result = mysqli_query($conn, $check_sql);

        if(mysqli_num_rows($check_result) > 0){
            echo "<script>
                    alert('Phòng này đã được đặt trong khoảng thời gian đã chọn!');
                    window.history.back();
                </script>";
            exit();
        }

        // Lấy giá phòng
        $sqlPrice = "SELECT rt.price FROM rooms r JOIN room_types rt
                        ON r.room_type_id = rt.room_type_id
                        WHERE r.room_id='$room_id' ";

        $priceResult = mysqli_query($conn,$sqlPrice);
        $priceRow = mysqli_fetch_assoc($priceResult);

        $price = $priceRow['price'];

        $days = ceil(
            (strtotime($check_out)-strtotime($check_in))/86400
        );

        if($days <= 0) {
            $days = 1;
        }

        $total = $price * $days;

        // Lấy phòng cũ
        $oldRoom = mysqli_query($conn,"SELECT room_id 
                                        FROM booking_details 
                                        WHERE booking_id='$id'");
        $old = mysqli_fetch_assoc($oldRoom);

        // Trả phòng cũ về trạng thái Trống
        mysqli_query($conn,"UPDATE rooms
                        SET status='Trống'
                        WHERE room_id='".$old['room_id']."' ");

        // Cập nhật booking
        mysqli_query($conn,"UPDATE bookings
                            SET customer_id='$customer_id',
                                check_in_date='$check_in',
                                check_out_date='$check_out',
                                status='$status',
                                total_amount='$total',
                                note='$note'
                            WHERE booking_id='$id' ");

        // Xóa booking detail cũ
        mysqli_query($conn,"DELETE FROM booking_details WHERE booking_id='$id' ");
        
        // Thêm booking detail mới
        mysqli_query($conn,"INSERT INTO booking_details(booking_id, room_id, price)
                            VALUES('$id', '$room_id', '$price')");

        // Cập nhật trạng thái phòng mới
        if($status == 'Đã xác nhận'){
            $roomStatus = "Đã đặt";
        }elseif($status == 'Đang thuê'){
            $roomStatus = "Đang thuê";
        }else{
            $roomStatus = "Trống";
        }

        mysqli_query($conn," UPDATE rooms
            SET status='$roomStatus'
            WHERE room_id='$room_id'
        ");

        header("Location:index.php");
        exit();
    }

    // Lấy thông tin booking
    $sql = "SELECT b.*, bd.room_id
            FROM bookings b
            JOIN booking_details bd
            ON b.booking_id=bd.booking_id
            WHERE b.booking_id='$id'";

    $result = mysqli_query($conn,$sql);
    $row = mysqli_fetch_assoc($result);

    $customers = mysqli_query($conn,"SELECT * FROM customers ORDER BY full_name");

    $rooms = mysqli_query($conn,"SELECT r.room_id, r.room_number, rt.type_name
                                FROM rooms r JOIN room_types rt 
                                ON r.room_type_id = rt.room_type_id
                                WHERE r.status = 'Trống' 
                                OR r.room_id = '".$row['room_id']."' ");

    //Không cho sửa booking đã trả phòng
    if($row['status'] == 'Đã trả phòng'){
        echo "<script>
            alert('Không thể sửa đơn đã trả phòng!');
            location='index.php';
        </script>";
        exit();
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sửa đặt phòng</title>

    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet">

</head>

<body>

    <div class="container mt-4">

        <h2>Sửa đặt phòng</h2>

        <form method="POST">

            <div class="mb-3">
                <label>Khách hàng</label>

                <select name="customer_id" class="form-control">

                    <?php while($c=mysqli_fetch_assoc($customers)){ ?>

                        <option value="<?= $c['customer_id']; ?>"
                            <?= $row['customer_id']==$c['customer_id']?'selected':''; ?>>

                            <?= $c['full_name']; ?>
                        </option>

                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Phòng</label>

                <select name="room_id" class="form-control">
                    <?php while($r=mysqli_fetch_assoc($rooms)) { ?>

                        <option value="<?= $r['room_id']; ?>"
                            <?= $row['room_id']==$r['room_id']?'selected':''; ?>>

                            <?= $r['room_number']; ?>
                            -
                            <?= $r['type_name']; ?>
                        </option>

                    <?php } ?>
                </select>

            </div>

            <div class="mb-3">
                <label>Ngày nhận phòng</label>

                <input type="date" name="check_in_date" class="form-control"
                        value="<?= $row['check_in_date']; ?>">
            </div>

            <div class="mb-3">
                <label>Ngày trả phòng</label>

                <input type="date" name="check_out_date" class="form-control"
                        value="<?= $row['check_out_date']; ?>">
            </div>

            <div class="mb-3">

                <label>Trạng thái</label>
                <select name="status" class="form-control">
                
                <?php
                    $list=[
                        'Đã đặt',
                        'Đang thuê',
                        'Đã trả phòng',
                        'Đã hủy'
                    ];

                    foreach($list as $st) {
                ?>

                    <option <?= $row['status']==$st?'selected':''; ?> >
                        <?= $st ?>
                    </option>

                <?php } ?>

                </select>

            </div>

            <div class="mb-3">
                <label>Ghi chú</label>
                <textarea name="note" class="form-control"><?= $row['note']; ?></textarea>
            </div>

            <button class="btn btn-primary" name="update">
                Cập nhật
            </button>

            <a href="index.php" class="btn btn-secondary">
                Quay lại
            </a>

        </form>

    </div>

</body>
</html>