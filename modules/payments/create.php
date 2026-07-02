<?php
include '../../includes/auth.php';
include '../../config/database.php';
include '../../includes/header.php';

/** @var mysqli $conn */
$conn = $conn;

if (!isset($_GET['id'])) {
    header("Location:../invoices/index.php");
    exit();
}

$invoice_id = intval($_GET['id']);

$sql = "
SELECT
    i.*,
    b.booking_id,
    c.full_name

FROM invoices i

JOIN bookings b
ON i.booking_id = b.booking_id

JOIN customers c
ON b.customer_id = c.customer_id

WHERE i.invoice_id = '$invoice_id'
";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    echo "<div class='alert alert-danger m-3'>
            Không tìm thấy hóa đơn.
          </div>";
    include '../../includes/footer.php';
    exit();
}

$invoice = mysqli_fetch_assoc($result);

/*
|--------------------------------------------------------------------------
| Kiểm tra đã thanh toán chưa
|--------------------------------------------------------------------------
*/

$check = mysqli_query($conn,"
SELECT *
FROM payments
WHERE invoice_id='$invoice_id'
");

if(mysqli_num_rows($check)>0){

    echo "<script>

        alert('Hóa đơn đã được thanh toán.');

        window.location='../payments/index.php';

    </script>";

    exit();
}

/*
|--------------------------------------------------------------------------
| Lưu thanh toán
|--------------------------------------------------------------------------
*/

if(isset($_POST['save'])){

    $payment_method = mysqli_real_escape_string(
        $conn,
        $_POST['payment_method']
    );

    $amount = intval($_POST['amount']);

    mysqli_query($conn,"
        INSERT INTO payments(
            invoice_id,
            payment_method,
            amount
        )
        VALUES(
            '$invoice_id',
            '$payment_method',
            '$amount'
        )
    ");

    echo "<script>

        alert('Thanh toán thành công.');

        window.location='../payments/index.php';

    </script>";

    exit();
}
?>

<div class="container-fluid">

<div class="row justify-content-center">

<div class="col-lg-6">

<div class="card">

<div class="card-header bg-success text-white">

<h4 class="mb-0">

Thanh toán hóa đơn

</h4>

</div>

<div class="card-body">

<form method="post">

<div class="mb-3">

<label class="form-label">

Khách hàng

</label>

<input
type="text"
class="form-control"
value="<?= htmlspecialchars($invoice['full_name']) ?>"
readonly>

</div>

<div class="mb-3">

<label class="form-label">

Mã hóa đơn

</label>

<input
type="text"
class="form-control"
value="#<?= $invoice['invoice_id'] ?>"
readonly>

</div>

<div class="mb-3">

<label class="form-label">

Tổng tiền

</label>

<input
type="text"
class="form-control"
value="<?= number_format($invoice['total_amount']) ?> VNĐ"
readonly>

<input
type="hidden"
name="amount"
value="<?= $invoice['total_amount'] ?>">

</div>

<div class="mb-3">

<label class="form-label">

Phương thức thanh toán

</label>

<select
name="payment_method"
class="form-select"
required>

<option value="Tiền mặt">

Tiền mặt

</option>

<option value="Chuyển khoản">

Chuyển khoản

</option>

<option value="Momo">

Momo

</option>

</select>

</div>

<div class="text-center">

<button
type="submit"
name="save"
class="btn btn-success">

<i class="fa fa-money-bill"></i>

Xác nhận thanh toán

</button>

<a
href="../invoices/detail.php?id=<?= $invoice_id ?>"
class="btn btn-secondary">

Quay lại

</a>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

<?php
include '../../includes/footer.php';
?>