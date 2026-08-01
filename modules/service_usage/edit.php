<?php
include '../../includes/auth.php';
include '../../config/database.php';
include '../../includes/header.php';
require_once __DIR__ . '/update_invoice.php';

/** @var mysqli $conn */

if (!isset($_GET['id'])) {
    header("Location:../invoices/index.php");
    exit();
}

$id = (int)$_GET['id'];

/*=========================================
= Lấy thông tin dịch vụ
=========================================*/

$sql = "
SELECT
    su.*,
    s.service_name,
    s.price,
    b.status,
    i.invoice_id
FROM service_usage su
JOIN services s
    ON su.service_id = s.service_id
JOIN bookings b
    ON su.booking_id = b.booking_id
LEFT JOIN invoices i
    ON su.booking_id = i.booking_id
WHERE su.usage_id = $id
LIMIT 1
";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {

    echo "<div class='alert alert-danger m-3'>
            Không tìm thấy dịch vụ.
          </div>";

    include '../../includes/footer.php';
    exit();
}

$data = mysqli_fetch_assoc($result);

$booking_id = (int)$data['booking_id'];
$invoice_id = (int)$data['invoice_id'];

/*=========================================
= Chỉ cho phép sửa khi đang thuê
=========================================*/

if ($data['status'] != 'Đang thuê') {

    echo "<script>
        alert('Khách đã trả phòng. Không thể sửa dịch vụ.');
        window.location='../bookings/index.php';
    </script>";

    exit();
}

/*=========================================
= Nếu hóa đơn đã thanh toán
=========================================*/

if ($invoice_id > 0) {

    $paid = mysqli_query($conn, "
        SELECT payment_id
        FROM payments
        WHERE invoice_id = $invoice_id
        LIMIT 1
    ");

    if (mysqli_num_rows($paid) > 0) {

        echo "<script>
            alert('Hóa đơn đã thanh toán. Không được sửa.');
            window.location='../payments/detail.php?id=".mysqli_fetch_assoc($paid)['payment_id']."';
        </script>";

        exit();
    }
}

/*=========================================
= Cập nhật
=========================================*/

if (isset($_POST['update'])) {

    $quantity = max(1, (int)$_POST['quantity']);

    mysqli_begin_transaction($conn);

    try {

        if (!mysqli_query($conn, "
            UPDATE service_usage
            SET quantity = $quantity
            WHERE usage_id = $id
        ")) {

            throw new Exception(mysqli_error($conn));
        }

        if ($invoice_id > 0) {

            updateInvoice($conn, $booking_id);

        }

        mysqli_commit($conn);

        if ($invoice_id > 0) {

            header("Location:../invoices/detail.php?id=".$invoice_id);

        } else {

            header("Location:../bookings/detail.php?id=".$booking_id);

        }

        exit();

    } catch (Exception $e) {

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

<div class="container mt-4">

    <div class="card shadow">

        <div class="card-header bg-warning">

            <h4 class="mb-0">

                Sửa dịch vụ

            </h4>

        </div>

        <div class="card-body">

            <form method="post">

                <div class="mb-3">

                    <label class="form-label">

                        Dịch vụ

                    </label>

                    <input
                        type="text"
                        class="form-control"
                        value="<?= htmlspecialchars($data['service_name']) ?>"
                        readonly>

                </div>

                <div class="mb-3">

                    <label class="form-label">

                        Đơn giá

                    </label>

                    <input
                        type="text"
                        class="form-control"
                        value="<?= number_format($data['price']) ?> đ"
                        readonly>

                </div>

                <div class="mb-3">

                    <label class="form-label">

                        Số lượng

                    </label>

                    <input
                        type="number"
                        name="quantity"
                        class="form-control"
                        value="<?= $data['quantity'] ?>"
                        min="1"
                        required>

                </div>

                <div class="d-flex gap-2">

                    <button
                        type="submit"
                        name="update"
                        class="btn btn-success">

                        <i class="fa fa-save"></i>

                        Cập nhật

                    </button>

                    <?php if($invoice_id > 0){ ?>

                        <a
                            href="../invoices/detail.php?id=<?= $invoice_id ?>"
                            class="btn btn-secondary">

                            Quay lại

                        </a>

                    <?php }else{ ?>

                        <a
                            href="../bookings/detail.php?id=<?= $booking_id ?>"
                            class="btn btn-secondary">

                            Quay lại

                        </a>

                    <?php } ?>

                </div>

            </form>

        </div>

    </div>

</div>

<?php include '../../includes/footer.php'; ?>