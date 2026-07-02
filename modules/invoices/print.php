<?php
include '../../includes/auth.php';
include '../../config/database.php';

/** @var mysqli $conn */

if (!isset($_GET['id'])) {
    die("Không tìm thấy hóa đơn.");
}

$id = intval($_GET['id']);

$sql = "
SELECT
    i.*,
    b.booking_id,
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

$result=mysqli_query($conn,$sql);

if(mysqli_num_rows($result)==0){
    die("Không tìm thấy hóa đơn.");
}

$invoice=mysqli_fetch_assoc($result);

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

$services=mysqli_query($conn,"
SELECT
s.service_name,
s.price,
su.quantity

FROM service_usage su

JOIN services s
ON su.service_id=s.service_id

WHERE su.booking_id='{$invoice['booking_id']}'
");
?>

<!DOCTYPE html>
<html lang="vi">

<head>

<meta charset="UTF-8">

<title>Hóa đơn</title>

<style>

body{

font-family:Arial,sans-serif;

margin:40px;

font-size:14px;

color:#000;

}

h1,h2,h3{

margin:0;

}

table{

width:100%;

border-collapse:collapse;

margin-top:10px;

}

table th,
table td{

border:1px solid #000;

padding:8px;

}

.right{

text-align:right;

}

.center{

text-align:center;

}

.header{

display:flex;

justify-content:space-between;

margin-bottom:30px;

}

.total{

font-size:18px;

font-weight:bold;

color:red;

}

@media print{

button{

display:none;

}

}

</style>

</head>

<body>

<div class="header">

<div>

<h2>KHÁCH SẠN MINI</h2>

<p>Địa chỉ: Cần Thơ</p>

<p>Điện thoại: 0909 999 999</p>

</div>

<div>

<h2>HÓA ĐƠN THANH TOÁN</h2>

<p>Mã hóa đơn: #<?= $invoice['invoice_id'] ?></p>

<p>Ngày: <?= date("d/m/Y H:i") ?></p>

</div>

</div>

<hr>

<h3>Thông tin khách hàng</h3>

<p><b>Họ tên:</b> <?= $invoice['full_name'] ?></p>

<p><b>Điện thoại:</b> <?= $invoice['phone'] ?></p>

<p><b>Email:</b> <?= $invoice['email'] ?></p>

<p><b>CCCD:</b> <?= $invoice['id_card'] ?></p>

<p><b>Địa chỉ:</b> <?= $invoice['address'] ?></p>

<p><b>Check In:</b> <?= $invoice['check_in_date'] ?></p>

<p><b>Check Out:</b> <?= $invoice['check_out_date'] ?></p>

<h3>Danh sách phòng</h3>

<table>

<tr>

<th>Phòng</th>

<th>Loại phòng</th>

<th>Giá</th>

</tr>

<?php while($r=mysqli_fetch_assoc($rooms)){ ?>

<tr>

<td><?= $r['room_number'] ?></td>

<td><?= $r['type_name'] ?></td>

<td class="right"><?= number_format($r['price']) ?> đ</td>

</tr>

<?php } ?>

</table>

<h3>Dịch vụ</h3>

<table>

<tr>

<th>Dịch vụ</th>

<th>Đơn giá</th>

<th>SL</th>

<th>Thành tiền</th>

</tr>

<?php

$totalService=0;

while($s=mysqli_fetch_assoc($services)){

$tt=$s['price']*$s['quantity'];

$totalService += $tt;

?>

<tr>

<td><?= $s['service_name'] ?></td>

<td class="right"><?= number_format($s['price']) ?></td>

<td class="center"><?= $s['quantity'] ?></td>

<td class="right"><?= number_format($tt) ?> đ</td>

</tr>

<?php } ?>

</table>

<br>

<table>

<tr>

<th width="70%">Tiền phòng</th>

<td class="right">

<?= number_format($invoice['room_total']) ?> đ

</td>

</tr>

<tr>

<th>Tiền dịch vụ</th>

<td class="right">

<?= number_format($invoice['service_total']) ?> đ

</td>

</tr>

<tr>

<th>TỔNG THANH TOÁN</th>

<td class="right total">

<?= number_format($invoice['total_amount']) ?> đ

</td>

</tr>

</table>

<br><br>

<table style="border:none">

<tr style="border:none">

<td style="border:none;text-align:center">

<b>Khách hàng</b>

<br><br><br><br>

(Ký tên)

</td>

<td style="border:none;text-align:center">

<b>Thu ngân</b>

<br><br><br><br>

(Ký tên)

</td>

</tr>

</table>

<br>

<div style="text-align:center">

<button onclick="window.print()">

🖨 In hóa đơn

</button>

</div>

<script>

window.onload=function(){

window.print();

}

</script>

</body>

</html>