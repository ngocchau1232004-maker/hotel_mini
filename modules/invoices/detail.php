<?php
include '../../includes/auth.php';
include '../../config/database.php';
include '../../includes/header.php';

/** @var mysqli $conn */
$conn = $conn;

if (!isset($_GET['id'])) {
    header("Location:index.php");
    exit();
}

$id = intval($_GET['id']);

$sql = "
SELECT
    i.*,
    b.booking_id,
    b.booking_date,
    b.check_in_date,
    b.check_out_date,
    b.actual_check_in,
    b.actual_check_out,
    c.full_name,
    c.phone,
    c.email,
    c.id_card,
    c.address
FROM invoices i

JOIN bookings b
ON i.booking_id=b.booking_id

JOIN customers c
ON b.customer_id=c.customer_id

WHERE i.invoice_id='$id'
";

$invoice = mysqli_fetch_assoc(mysqli_query($conn,$sql));

if(!$invoice){
    echo "<div class='alert alert-danger m-3'>Không tìm thấy hóa đơn.</div>";
    include '../../includes/footer.php';
    exit();
}

//Danh sách phòng

$rooms=mysqli_query($conn,"
SELECT
    r.room_number,
    rt.type_name,
    bd.price
FROM booking_details bd

JOIN rooms r
ON bd.room_id=r.room_id

JOIN room_types rt
ON r.room_type_id=rt.room_type_id

WHERE bd.booking_id='{$invoice['booking_id']}'
");

//Danh sách dịch vụ

$services=mysqli_query($conn,"
SELECT
    s.service_name,
    s.price,
    su.quantity,
    su.usage_date

FROM service_usage su

JOIN services s
ON su.service_id=s.service_id

WHERE su.booking_id='{$invoice['booking_id']}'
");
?>

<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-3">

<h3>Chi tiết hóa đơn</h3>

<div>

<a href="index.php" class="btn btn-secondary">
Quay lại
</a>

<a href="print.php?id=<?= $id ?>" target="_blank"
class="btn btn-primary">
In hóa đơn
</a>

</div>

</div>


<div class="card mb-3">

<div class="card-header bg-primary text-white">
Thông tin khách hàng
</div>

<div class="card-body">

<div class="row">

<div class="col-md-6">

<p><strong>Họ tên:</strong>
<?= $invoice['full_name'] ?>
</p>

<p><strong>Điện thoại:</strong>
<?= $invoice['phone'] ?>
</p>

<p><strong>Email:</strong>
<?= $invoice['email'] ?>
</p>

</div>

<div class="col-md-6">

<p><strong>CCCD:</strong>
<?= $invoice['id_card'] ?>
</p>

<p><strong>Địa chỉ:</strong>
<?= $invoice['address'] ?>
</p>

</div>

</div>

</div>

</div>



<div class="card mb-3">

<div class="card-header bg-success text-white">

Thông tin đặt phòng

</div>

<div class="card-body">

<div class="row">

<div class="col-md-4">

<p>

<strong>Ngày đặt:</strong><br>

<?= date("d/m/Y H:i",strtotime($invoice['booking_date'])) ?>

</p>

</div>

<div class="col-md-4">

<p>

<strong>Check In:</strong><br>

<?= $invoice['check_in_date'] ?>

</p>

</div>

<div class="col-md-4">

<p>

<strong>Check Out:</strong><br>

<?= $invoice['check_out_date'] ?>

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

<th class="text-end">

Giá

</th>

</tr>

</thead>

<tbody>

<?php

while($r=mysqli_fetch_assoc($rooms)){

?>

<tr>

<td>

<?= $r['room_number'] ?>

</td>

<td>

<?= $r['type_name'] ?>

</td>

<td class="text-end">

<?= number_format($r['price']) ?> đ

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

</div>





<div class="card mb-3">

<div class="card-header">

Dịch vụ sử dụng

</div>

<div class="card-body">

<table class="table table-striped">

<thead>

<tr>

<th>Dịch vụ</th>

<th width="120">

Đơn giá

</th>

<th width="100">

SL

</th>

<th width="180">

Ngày sử dụng

</th>

<th width="150">

Thành tiền

</th>

</tr>

</thead>

<tbody>

<?php

$totalService=0;

while($s=mysqli_fetch_assoc($services)){

$thanhtien=$s['price']*$s['quantity'];

$totalService+=$thanhtien;

?>

<tr>

<td>

<?= $s['service_name'] ?>

</td>

<td>

<?= number_format($s['price']) ?>

</td>

<td>

<?= $s['quantity'] ?>

</td>

<td>

<?= date("d/m/Y H:i",strtotime($s['usage_date'])) ?>

</td>

<td>

<?= number_format($thanhtien) ?>

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

</div>




<div class="card">

<div class="card-header bg-danger text-white">

Tổng thanh toán

</div>

<div class="card-body">

<table class="table">

<tr>

<th>Tiền phòng</th>

<td class="text-end">

<?= number_format($invoice['room_total']) ?> đ

</td>

</tr>

<tr>

<th>Tiền dịch vụ</th>

<td class="text-end">

<?= number_format($invoice['service_total']) ?> đ

</td>

</tr>

<tr class="table-warning">

<th>

TỔNG CỘNG

</th>

<td class="text-end">

<h4 class="text-danger">

<?= number_format($invoice['total_amount']) ?> đ

</h4>

</td>

</tr>

</table>

</div>

</div>

</div>

<?php
include '../../includes/footer.php';
?>