<?php
include '../../includes/auth.php';
include '../../config/database.php';
include '../../includes/header.php';

/** @var mysqli $conn */

if (!isset($_GET['id'])) {
    header("Location:index.php");
    exit();
}

$id = (int)$_GET['id'];

/*====================================
= Lấy thông tin hóa đơn
====================================*/

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
    ON i.booking_id = b.booking_id
JOIN customers c
    ON b.customer_id = c.customer_id
WHERE i.invoice_id = $id
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

/*====================================
= Kiểm tra thanh toán
====================================*/

$payment = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT payment_id
FROM payments
WHERE invoice_id = $id
LIMIT 1
"));

$isPaid = !empty($payment);

/*====================================
= Danh sách phòng
====================================*/

$rooms = mysqli_query($conn, "
SELECT
    r.room_number,
    rt.type_name,
    bd.price
FROM booking_details bd
JOIN rooms r
    ON bd.room_id = r.room_id
JOIN room_types rt
    ON r.room_type_id = rt.room_type_id
WHERE bd.booking_id = " . $invoice['booking_id'] . "
");

/*====================================
= Danh sách dịch vụ
====================================*/

$services = mysqli_query($conn, "
SELECT
    su.usage_id,
    s.service_name,
    s.price,
    su.quantity,
    su.usage_date
FROM service_usage su
JOIN services s
    ON su.service_id = s.service_id
WHERE su.booking_id = " . $invoice['booking_id'] . "
ORDER BY su.usage_date DESC
");
?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h3>Chi tiết hóa đơn</h3>

        <div class="d-flex gap-2">

            <a href="index.php" class="btn btn-secondary">
                Quay lại
            </a>

            <a href="print.php?id=<?= $id ?>"
                target="_blank"
                class="btn btn-primary">
                <i class="fa fa-print"></i>
                In hóa đơn
            </a>

            <?php if (!$isPaid) { ?>

                <a
                    href="../payments/create.php?id=<?= $invoice['invoice_id'] ?>"
                    class="btn btn-success">

                    <i class="fa fa-money-bill"></i>

                    Thanh toán

                </a>

            <?php } else { ?>

                <a
                    href="../payments/detail.php?id=<?= $payment['payment_id'] ?>"
                    class="btn btn-outline-success">

                    <i class="fa fa-check-circle"></i>

                    Đã thanh toán

                </a>

            <?php } ?>

        </div>

    </div>

    <!-- THÔNG TIN KHÁCH HÀNG -->
    <div class="card mb-3">

        <div class="card-header bg-primary text-white">

            Thông tin khách hàng

        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-6">

                    <p>
                        <strong>Họ tên:</strong>
                        <?= htmlspecialchars($invoice['full_name']) ?>
                    </p>

                    <p>
                        <strong>Điện thoại:</strong>
                        <?= $invoice['phone'] ?>
                    </p>

                    <p>
                        <strong>Email:</strong>
                        <?= $invoice['email'] ?>
                    </p>

                </div>

                <div class="col-md-6">

                    <p>
                        <strong>CCCD:</strong>
                        <?= $invoice['id_card'] ?>
                    </p>

                    <p>
                        <strong>Địa chỉ:</strong>
                        <?= $invoice['address'] ?>
                    </p>

                </div>

            </div>

        </div>

    </div>


    <!-- THÔNG TIN ĐẶT PHÒNG -->

    <div class="card mb-3">

        <div class="card-header bg-success text-white">

            Thông tin đặt phòng

        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-3">

                    <strong>Ngày đặt</strong><br>

                    <?= date("d/m/Y H:i", strtotime($invoice['booking_date'])) ?>

                </div>

                <div class="col-md-3">

                    <strong>Check In</strong><br>

                    <?= $invoice['check_in_date'] ?>

                </div>

                <div class="col-md-3">

                    <strong>Check Out</strong><br>

                    <?= $invoice['check_out_date'] ?>

                </div>

                <div class="col-md-3">

                    <strong>Thực tế</strong><br>

                    <?= $invoice['actual_check_out']
                        ? date("d/m/Y H:i", strtotime($invoice['actual_check_out']))
                        : "Chưa trả phòng"; ?>

                </div>

            </div>

        </div>

    </div>



    <!-- DANH SÁCH PHÒNG -->

    <div class="card mb-3">

        <div class="card-header">

            Danh sách phòng

        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <thead>

                    <tr>

                        <th>Phòng</th>

                        <th>Loại phòng</th>

                        <th class="text-end">

                            Giá

                        </th>

                    </tr>

                </thead>

                <tbody>

                    <?php while ($room = mysqli_fetch_assoc($rooms)) { ?>

                        <tr>

                            <td>

                                <?= $room['room_number'] ?>

                            </td>

                            <td>

                                <?= $room['type_name'] ?>

                            </td>

                            <td class="text-end">

                                <?= number_format($room['price']) ?> đ

                            </td>

                        </tr>

                    <?php } ?>

                </tbody>

            </table>

        </div>

    </div>


    <?php if (!$isPaid) { ?>

        <a
            href="../service_usage/create.php?booking_id=<?= $invoice['booking_id'] ?>"
            class="btn btn-success mb-3">

            <i class="fa fa-plus"></i>

            Thêm dịch vụ

        </a>

    <?php } ?>


    <!-- DANH SÁCH DỊCH VỤ -->

    <div class="card mb-3">

        <div class="card-header">

            Dịch vụ sử dụng

        </div>

        <div class="card-body">

            <table class="table table-striped">

                <thead>

                    <tr>

                        <th>Dịch vụ</th>

                        <th>Đơn giá</th>

                        <th>SL</th>

                        <th>Ngày sử dụng</th>

                        <th>Thành tiền</th>

                        <?php if (!$isPaid) { ?>

                            <th width="150">

                                Thao tác

                            </th>

                        <?php } ?>

                    </tr>

                </thead>

                <tbody>

                    <?php

                    while ($service = mysqli_fetch_assoc($services)) {

                        $total = $service['price'] * $service['quantity'];

                    ?>

                        <tr>

                            <td>

                                <?= htmlspecialchars($service['service_name']) ?>

                            </td>

                            <td>

                                <?= number_format($service['price']) ?> đ

                            </td>

                            <td>

                                <?= $service['quantity'] ?>

                            </td>

                            <td>

                                <?= date("d/m/Y H:i", strtotime($service['usage_date'])) ?>

                            </td>

                            <td>

                                <?= number_format($total) ?> đ

                            </td>

                            <?php if (!$isPaid) { ?>

                                <td>

                                    <a
                                        href="../service_usage/edit.php?id=<?= $service['usage_id'] ?>"
                                        class="btn btn-warning btn-sm">

                                        Sửa

                                    </a>

                                    <a
                                        href="../service_usage/delete.php?id=<?= $service['usage_id'] ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Xóa dịch vụ này?')">

                                        Xóa

                                    </a>

                                </td>

                            <?php } ?>

                        </tr>

                    <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

    <!-- TỔNG THANH TOÁN -->

    <div class="card">

        <div class="card-header bg-danger text-white">

            Tổng thanh toán

        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <tr>

                    <th width="250">

                        Tiền phòng

                    </th>

                    <td class="text-end">

                        <?= number_format($invoice['room_total']) ?> đ

                    </td>

                </tr>

                <tr>

                    <th>

                        Tiền dịch vụ

                    </th>

                    <td class="text-end">

                        <?= number_format($invoice['service_total']) ?> đ

                    </td>

                </tr>

                <tr class="table-warning">

                    <th>

                        Tổng cộng

                    </th>

                    <td class="text-end">

                        <h4 class="text-danger mb-0">

                            <?= number_format($invoice['total_amount']) ?> đ

                        </h4>

                    </td>

                </tr>

                <tr>

                    <th>

                        Trạng thái

                    </th>

                    <td class="text-end">

                        <?php if ($isPaid) { ?>

                            <span class="badge bg-success">

                                Đã thanh toán

                            </span>

                        <?php } else { ?>

                            <span class="badge bg-warning text-dark">

                                Chưa thanh toán

                            </span>

                        <?php } ?>

                    </td>

                </tr>

            </table>

        </div>

    </div>

</div>

<?php include '../../includes/footer.php'; ?>