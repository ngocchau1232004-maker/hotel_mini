<?php
include '../../includes/auth.php';
include '../../config/database.php';
include '../../includes/header.php';

/** @var mysqli $conn */

if (!isset($_GET['id'])) {
    header("Location:../invoices/index.php");
    exit();
}

$invoice_id = (int)$_GET['id'];

/*=========================================
= Lấy thông tin hóa đơn
=========================================*/

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
WHERE i.invoice_id = $invoice_id
LIMIT 1
";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {

    echo "<div class='alert alert-danger m-3'>
            Không tìm thấy hóa đơn.
          </div>";

    include '../../includes/footer.php';
    exit();
}

$invoice = mysqli_fetch_assoc($result);

/*=========================================
= Kiểm tra đã thanh toán
=========================================*/

$paid = mysqli_query($conn,"
SELECT payment_id
FROM payments
WHERE invoice_id = $invoice_id
LIMIT 1
");

if(mysqli_num_rows($paid)>0){

    $payment = mysqli_fetch_assoc($paid);

    header("Location:detail.php?id=".$payment['payment_id']);
    exit();
}

/*=========================================
= Thanh toán
=========================================*/

if(isset($_POST['save'])){

    $payment_method = mysqli_real_escape_string(
        $conn,
        $_POST['payment_method']
    );

    $amount = (int)$_POST['amount'];

    mysqli_begin_transaction($conn);

    try{

        if(!mysqli_query($conn,"
            INSERT INTO payments(
                invoice_id,
                payment_method,
                amount
            )
            VALUES(
                $invoice_id,
                '$payment_method',
                $amount
            )
        ")){
            throw new Exception(mysqli_error($conn));
        }

        $payment_id = mysqli_insert_id($conn);

        if(!mysqli_query($conn,"
            UPDATE bookings
            SET
                status='Đã trả phòng',
                actual_check_out = NOW()
            WHERE booking_id=".$invoice['booking_id']."
        ")){
            throw new Exception(mysqli_error($conn));
        }

        if(!mysqli_query($conn,"
            UPDATE rooms r
            JOIN booking_details bd
                ON r.room_id = bd.room_id
            SET
                r.status='Trống'
            WHERE bd.booking_id=".$invoice['booking_id']."
        ")){
            throw new Exception(mysqli_error($conn));
        }

        mysqli_commit($conn);

        header("Location:detail.php?id=".$payment_id);
        exit();

    }catch(Exception $e){

        mysqli_rollback($conn);

        echo "<div class='container mt-3'>
                <div class='alert alert-danger'>
                    ".$e->getMessage()."
                </div>
              </div>";

        include '../../includes/footer.php';
        exit();
    }
}
?>

<div class="container-fluid">

    <div class="row justify-content-center">

        <div class="col-lg-6">

            <div class="card shadow">

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

                                Tổng thanh toán

                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="<?= number_format($invoice['total_amount']) ?> đ"
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

                        <div class="d-flex gap-2 justify-content-center">

                            <button
                                type="submit"
                                name="save"
                                class="btn btn-success">

                                <i class="fa fa-money-bill"></i>

                                Xác nhận thanh toán

                            </button>

                            <a
                                href="../invoices/detail.php?id=<?= $invoice['invoice_id'] ?>"
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

<?php include '../../includes/footer.php'; ?>