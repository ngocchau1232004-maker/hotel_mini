<?php
include '../../includes/auth.php';
include '../../config/database.php';

/** @var mysqli $conn */

if (!isset($_GET['id'])) {
    die("Không tìm thấy thanh toán.");
}

$id = intval($_GET['id']);

$sql = "
SELECT

    p.*,

    i.invoice_id,
    i.room_total,
    i.service_total,
    i.total_amount,

    b.check_in_date,
    b.check_out_date,

    c.full_name,
    c.phone,
    c.email,
    c.id_card,
    c.address

FROM payments p

JOIN invoices i
ON p.invoice_id=i.invoice_id

JOIN bookings b
ON i.booking_id=b.booking_id

JOIN customers c
ON b.customer_id=c.customer_id

WHERE p.payment_id='$id'
";

$result=mysqli_query($conn,$sql);

if(mysqli_num_rows($result)==0){
    die("Không tìm thấy dữ liệu.");
}

$row=mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>

<html lang="vi">

<head>

<meta charset="UTF-8">

<title>Biên lai thanh toán</title>

<style>

body{

font-family:Arial,Helvetica,sans-serif;

margin:40px;

font-size:14px;

color:#000;

}

.header{

display:flex;

justify-content:space-between;

margin-bottom:30px;

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

.big{

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

<div style="text-align:right">

<h2>BIÊN LAI THANH TOÁN</h2>

<p>Mã thanh toán: #<?= $row['payment_id'] ?></p>

<p>Ngày in: <?= date("d/m/Y H:i") ?></p>

</div>

</div>

<hr>

<h3>Thông tin khách hàng</h3>

<p><strong>Họ tên:</strong> <?= htmlspecialchars($row['full_name']) ?></p>

<p><strong>Điện thoại:</strong> <?= $row['phone'] ?></p>

<p><strong>Email:</strong> <?= $row['email'] ?></p>

<p><strong>CCCD:</strong> <?= $row['id_card'] ?></p>

<p><strong>Địa chỉ:</strong> <?= $row['address'] ?></p>

<br>

<h3>Thông tin thanh toán</h3>

<table>

<tr>

<th width="40%">Mã hóa đơn</th>

<td>#<?= $row['invoice_id'] ?></td>

</tr>

<tr>

<th>Check In</th>

<td><?= $row['check_in_date'] ?></td>

</tr>

<tr>

<th>Check Out</th>

<td><?= $row['check_out_date'] ?></td>

</tr>

<tr>

<th>Phương thức thanh toán</th>

<td><?= $row['payment_method'] ?></td>

</tr>

<tr>

<th>Ngày thanh toán</th>

<td><?= date("d/m/Y H:i",strtotime($row['payment_date'])) ?></td>

</tr>

</table>

<br>

<h3>Chi tiết thanh toán</h3>

<table>

<tr>

<th>Nội dung</th>

<th width="220">Số tiền</th>

</tr>

<tr>

<td>Tiền phòng</td>

<td class="right">

<?= number_format($row['room_total']) ?> đ

</td>

</tr>

<tr>

<td>Tiền dịch vụ</td>

<td class="right">

<?= number_format($row['service_total']) ?> đ

</td>

</tr>

<tr>

<th>TỔNG THANH TOÁN</th>

<th class="right big">

<?= number_format($row['total_amount']) ?> đ

</th>

</tr>

<tr>

<td>Đã thanh toán</td>

<td class="right">

<?= number_format($row['amount']) ?> đ

</td>

</tr>

</table>

<br><br>

<table style="border:none">

<tr>

<td style="border:none;text-align:center">

<b>Khách hàng</b>

<br><br><br><br>

(Ký, ghi rõ họ tên)

</td>

<td style="border:none;text-align:center">

<b>Thu ngân</b>

<br><br><br><br>

(Ký, ghi rõ họ tên)

</td>

</tr>

</table>

<br>

<div style="text-align:center">

<button onclick="window.print()">

🖨 In biên lai

</button>

</div>

<script>

window.onload=function(){

window.print();

}

</script>

</body>

</html>