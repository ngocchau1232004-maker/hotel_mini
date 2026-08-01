<?php
include '../../includes/auth.php';
include '../../config/database.php';
include '../../includes/header.php';
require_once __DIR__ . '/update_invoice.php';

/** @var mysqli $conn */

if (!isset($_GET['booking_id'])) {
    header("Location:../bookings/index.php");
    exit();
}

$booking_id = (int)$_GET['booking_id'];

/*=========================================
= Lấy thông tin booking
=========================================*/

$sql = "
SELECT
    b.*,
    c.full_name
FROM bookings b
JOIN customers c
    ON b.customer_id = c.customer_id
WHERE b.booking_id = $booking_id
LIMIT 1
";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {

    echo "<div class='alert alert-danger m-3'>
            Không tìm thấy booking.
          </div>";

    include '../../includes/footer.php';
    exit();
}

$booking = mysqli_fetch_assoc($result);

/*=========================================
= Chỉ cho phép khi đang thuê
=========================================*/

if ($booking['status'] != 'Đang thuê') {

    echo "<script>

        alert('Chỉ được thêm dịch vụ khi khách đang thuê phòng.');

        window.location='../bookings/index.php';

    </script>";

    exit();
}

/*=========================================
= Kiểm tra hóa đơn đã thanh toán chưa
=========================================*/

$invoice = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT
    i.invoice_id,
    p.payment_id
FROM invoices i
LEFT JOIN payments p
    ON i.invoice_id = p.invoice_id
WHERE i.booking_id = $booking_id
LIMIT 1
"));

if ($invoice && !empty($invoice['payment_id'])) {

    echo "<script>

        alert('Hóa đơn đã thanh toán. Không thể thêm dịch vụ.');

        window.location='../payments/detail.php?id={$invoice['payment_id']}';

    </script>";

    exit();
}

/*=========================================
= Lưu dịch vụ
=========================================*/

if (isset($_POST['save'])) {

    $service_id = (int)$_POST['service_id'];
    $quantity   = max(1, (int)$_POST['quantity']);

    mysqli_begin_transaction($conn);

    try {

        /*---------------------------------------
        Kiểm tra dịch vụ đã tồn tại chưa
        ---------------------------------------*/

        $old = mysqli_query($conn, "
        SELECT usage_id
        FROM service_usage
        WHERE booking_id = $booking_id
        AND service_id = $service_id
        LIMIT 1
        ");

        if (!$old) {
            throw new Exception(mysqli_error($conn));
        }

        if (mysqli_num_rows($old) > 0) {

            if (!mysqli_query($conn, "
                UPDATE service_usage
                SET quantity = quantity + $quantity
                WHERE booking_id = $booking_id
                AND service_id = $service_id
            ")) {

                throw new Exception(mysqli_error($conn));

            }

        } else {

            if (!mysqli_query($conn, "
                INSERT INTO service_usage(
                    booking_id,
                    service_id,
                    quantity
                )
                VALUES(
                    $booking_id,
                    $service_id,
                    $quantity
                )
            ")) {

                throw new Exception(mysqli_error($conn));

            }

        }

        /*---------------------------------------
        Nếu đã có hóa đơn thì cập nhật
        ---------------------------------------*/

        updateInvoice($conn, $booking_id);

        mysqli_commit($conn);

        if ($invoice) {

            header("Location:../invoices/detail.php?id=".$invoice['invoice_id']);

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

/*=========================================
= Danh sách dịch vụ
=========================================*/

$services = mysqli_query($conn, "
SELECT *
FROM services
ORDER BY service_name
");
?>

<div class="container mt-4">

    <div class="card shadow">

        <div class="card-header bg-success text-white">
            <h4 class="mb-0">
                Thêm dịch vụ
            </h4>
        </div>

        <div class="card-body">

            <div class="mb-3">

                <strong>Khách hàng:</strong>

                <?= htmlspecialchars($booking['full_name']) ?>

            </div>

            <form method="post">

                <div class="mb-3">

                    <label class="form-label">

                        Dịch vụ

                    </label>

                    <select
                        name="service_id"
                        class="form-select"
                        required>

                        <option value="">

                            -- Chọn dịch vụ --

                        </option>

                        <?php while($s=mysqli_fetch_assoc($services)){ ?>

                            <option value="<?= $s['service_id'] ?>">

                                <?= htmlspecialchars($s['service_name']) ?>

                                (<?= number_format($s['price']) ?> đ)

                            </option>

                        <?php } ?>

                    </select>

                </div>

                <div class="mb-3">

                    <label class="form-label">

                        Số lượng

                    </label>

                    <input
                        type="number"
                        name="quantity"
                        class="form-control"
                        value="1"
                        min="1"
                        required>

                </div>

                <div class="d-flex gap-2">

                    <button
                        type="submit"
                        name="save"
                        class="btn btn-success">

                        <i class="fa fa-save"></i>

                        Lưu

                    </button>

                    <?php if($invoice){ ?>

                        <a
                            href="../invoices/detail.php?id=<?= $invoice['invoice_id'] ?>"
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

