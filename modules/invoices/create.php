<?php
include '../../includes/auth.php';
include '../../config/database.php';
include '../../includes/header.php';

/** @var mysqli $conn */
$conn = $conn;

if (!isset($_GET['booking_id'])) {
    header("Location:../bookings/index.php");
    exit();
}

$booking_id = intval($_GET['booking_id']);

/*
|--------------------------------------------------------------------------
| Kiểm tra booking
|--------------------------------------------------------------------------
*/

$sql = "
SELECT
    b.*,
    c.full_name,
    c.phone,
    c.email,
    c.id_card,
    c.address

FROM bookings b

JOIN customers c
ON b.customer_id = c.customer_id

WHERE b.booking_id='$booking_id'
";

$result = mysqli_query($conn,$sql);

if(mysqli_num_rows($result)==0){

    echo "<div class='alert alert-danger'>
            Không tìm thấy booking.
          </div>";

    include '../../includes/footer.php';
    exit();
}

$booking = mysqli_fetch_assoc($result);

/*
|--------------------------------------------------------------------------
| Kiểm tra hóa đơn đã tồn tại chưa
|--------------------------------------------------------------------------
*/

$check = mysqli_query($conn,"
SELECT *
FROM invoices
WHERE booking_id='$booking_id'
");

if(mysqli_num_rows($check)>0){

    $invoice=mysqli_fetch_assoc($check);

    header("Location:detail.php?id=".$invoice['invoice_id']);
    exit();

}

/*
|--------------------------------------------------------------------------
| Tính số ngày thuê
|--------------------------------------------------------------------------
*/

$checkin  = strtotime($booking['check_in_date']);
$checkout = strtotime($booking['check_out_date']);

$days = ($checkout-$checkin)/86400;

if($days<=0){
    $days=1;
}

/*
|--------------------------------------------------------------------------
| Tiền phòng
|--------------------------------------------------------------------------
*/

$roomQuery = mysqli_query($conn,"
SELECT
    bd.price,
    r.room_number,
    rt.type_name

FROM booking_details bd

JOIN rooms r
ON bd.room_id=r.room_id

JOIN room_types rt
ON r.room_type_id=rt.room_type_id

WHERE bd.booking_id='$booking_id'
");

$room_total=0;

$rooms=[];

while($row=mysqli_fetch_assoc($roomQuery)){

    $row['subtotal']=$row['price']*$days;

    $room_total += $row['subtotal'];

    $rooms[]=$row;

}

/*
|--------------------------------------------------------------------------
| Tiền dịch vụ
|--------------------------------------------------------------------------
*/

$serviceQuery=mysqli_query($conn,"
SELECT

    s.service_name,
    s.price,
    su.quantity

FROM service_usage su

JOIN services s
ON su.service_id=s.service_id

WHERE su.booking_id='$booking_id'
");

$service_total=0;

$services=[];

while($row=mysqli_fetch_assoc($serviceQuery)){

    $row['subtotal']=$row['price']*$row['quantity'];

    $service_total += $row['subtotal'];

    $services[]=$row;

}

$total_amount=$room_total+$service_total;
?>

<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-3">

<h3>Tạo hóa đơn</h3>

<a href="../bookings/index.php"
class="btn btn-secondary">

Quay lại

</a>

</div>

<div class="card mb-3">

<div class="card-header bg-primary text-white">

Thông tin khách hàng

</div>

<div class="card-body">

<div class="row">

<div class="col-md-6">

<p><strong>Khách hàng:</strong>

<?= $booking['full_name'] ?>

</p>

<p><strong>SĐT:</strong>

<?= $booking['phone'] ?>

</p>

<p><strong>Check In:</strong>

<?= $booking['check_in_date'] ?>

</p>

<p><strong>Check Out:</strong>

<?= $booking['check_out_date'] ?>

</p>

</div>

<div class="col-md-6">

<p><strong>Số ngày thuê:</strong>

<?= $days ?> ngày

</p>

<p><strong>Email:</strong>

<?= $booking['email'] ?>

</p>

<p><strong>CCCD:</strong>

<?= $booking['id_card'] ?>

</p>

</div>

</div>

</div>

</div>

<div class="card mb-3">

<div class="card-header">

Danh sách phòng

</div>

<div class="card-body">

<table class="table table-bordered">

<thead>

<tr>

<th>Phòng</th>

<th>Loại</th>

<th>Đơn giá</th>

<th>Số ngày</th>

<th>Thành tiền</th>

</tr>

</thead>

<tbody>

<?php foreach($rooms as $r){ ?>

<tr>

<td><?= $r['room_number'] ?></td>

<td><?= $r['type_name'] ?></td>

<td><?= number_format($r['price']) ?> đ</td>

<td><?= $days ?></td>

<td><?= number_format($r['subtotal']) ?> đ</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

<div class="card mb-3">

    <div class="card-header">
        Dịch vụ sử dụng
    </div>

    <div class="card-body">

        <table class="table table-bordered">

            <thead>

            <tr>

                <th>Dịch vụ</th>

                <th>Đơn giá</th>

                <th>Số lượng</th>

                <th>Thành tiền</th>

            </tr>

            </thead>

            <tbody>

            <?php

            if(count($services)>0){

                foreach($services as $s){

            ?>

            <tr>

                <td><?= $s['service_name'] ?></td>

                <td><?= number_format($s['price']) ?> đ</td>

                <td><?= $s['quantity'] ?></td>

                <td><?= number_format($s['subtotal']) ?> đ</td>

            </tr>

            <?php

                }

            }else{

            ?>

            <tr>

                <td colspan="4" class="text-center text-muted">

                    Không sử dụng dịch vụ

                </td>

            </tr>

            <?php } ?>

            </tbody>

        </table>

    </div>

</div>



<div class="card">

    <div class="card-header bg-success text-white">

        Tổng thanh toán

    </div>

    <div class="card-body">

        <table class="table">

            <tr>

                <th>Tiền phòng</th>

                <td class="text-end">

                    <?= number_format($room_total) ?> đ

                </td>

            </tr>

            <tr>

                <th>Tiền dịch vụ</th>

                <td class="text-end">

                    <?= number_format($service_total) ?> đ

                </td>

            </tr>

            <tr class="table-warning">

                <th>TỔNG CỘNG</th>

                <td class="text-end">

                    <h3 class="text-danger">

                        <?= number_format($total_amount) ?> đ

                    </h3>

                </td>

            </tr>

        </table>

        <form method="post">

            <button
                type="submit"
                name="save"
                class="btn btn-success">

                <i class="fa fa-save"></i>

                Tạo hóa đơn

            </button>

        </form>

    </div>

</div>

</div>

<?php

if(isset($_POST['save'])){

    mysqli_begin_transaction($conn);

    try{

        mysqli_query($conn,"
        INSERT INTO invoices(
            booking_id,
            room_total,
            service_total,
            total_amount
        )
        VALUES(
            '$booking_id',
            '$room_total',
            '$service_total',
            '$total_amount'
        )
        ");

        $invoice_id=mysqli_insert_id($conn);

        mysqli_query($conn,"
        UPDATE bookings

        SET

            status='Đã trả phòng',

            actual_check_out=NOW(),

            total_amount='$total_amount'

        WHERE booking_id='$booking_id'
        ");

        mysqli_query($conn,"
        UPDATE rooms

        SET status='Trống'

        WHERE room_id IN(

            SELECT room_id

            FROM booking_details

            WHERE booking_id='$booking_id'

        )
        ");

        mysqli_commit($conn);

        echo "<script>

            alert('Tạo hóa đơn thành công');

            window.location='detail.php?id=".$invoice_id."';

        </script>";

    }catch(Exception $e){

        mysqli_rollback($conn);

        echo "<div class='alert alert-danger'>

            Lỗi tạo hóa đơn.

        </div>";

    }

}

include '../../includes/footer.php';

?>