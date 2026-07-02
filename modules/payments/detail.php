<?php
    include '../../includes/auth.php';
    include '../../config/database.php';
    include '../../includes/header.php';

    /** @var mysqli $conn */
    $conn = $conn;

    if(!isset($_GET['id'])){
        header("Location:index.php");
        exit();
    }

    $id = intval($_GET['id']);

    $sql = "SELECT p.*,
                i.invoice_id,
                i.room_total,
                i.service_total,
                i.total_amount,
                b.booking_id,
                b.check_in_date,
                b.check_out_date,
                c.full_name,
                c.phone,
                c.email,
                c.id_card,
                c.address
            FROM payments p
            JOIN invoices i ON p.invoice_id=i.invoice_id
            JOIN bookings b ON i.booking_id=b.booking_id
            JOIN customers c ON b.customer_id=c.customer_id
            WHERE p.payment_id='$id'    ";

    $result = mysqli_query($conn,$sql);

    if(mysqli_num_rows($result)==0){
        echo "<div class='alert alert-danger'>Không tìm thấy thanh toán.</div>";
        include '../../includes/footer.php';
        exit();
    }

    $row = mysqli_fetch_assoc($result);
?>


<div class="container-fluid">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">

        <h3>Chi tiết thanh toán</h3>

        <div class="d-flex gap-2">
            <a href="index.php" class="btn btn-secondary">
                Quay lại
            </a>

            <a href="print.php?id=<?= $row['payment_id'] ?>"
                target="_blank" class="btn btn-success">
                <i class="fa fa-print"></i>
                In biên lai
            </a>

            <a href="../invoices/detail.php?id=<?= $row['invoice_id'] ?>"
                class="btn btn-primary">
                Xem hóa đơn
            </a>
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
                    <p><strong>Họ tên:</strong> <?= htmlspecialchars($row['full_name']) ?></p>
                    <p><strong>Điện thoại:</strong> <?= $row['phone'] ?></p>
                    <p><strong>Email:</strong> <?= $row['email'] ?></p>
                </div>

                <div class="col-md-6">
                    <p><strong>CCCD:</strong> <?= $row['id_card'] ?></p>
                    <p><strong>Địa chỉ:</strong> <?= $row['address'] ?></p>
                </div>
            </div>
        </div>

    </div>

    <!-- THÔNG TIN THANH TOÁN -->
    <div class="card mb-3">

        <div class="card-header bg-success text-white">
            Thông tin thanh toán
        </div>

        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th width="250">Mã thanh toán</th>
                    <td>#<?= $row['payment_id'] ?></td>
                </tr>

                <tr>
                    <th>Mã hóa đơn</th>
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
        </div>

    </div>

    <!-- CHI TIẾT SỐ TIỀN -->
    <div class="card">

        <div class="card-header bg-warning">
            Chi tiết số tiền
        </div>

        <div class="card-body">
            <table class="table table-striped">
                <tr>
                    <th>Tiền phòng</th>
                    <td class="text-end"><?= number_format($row['room_total']) ?> đ</td>
                </tr>

                <tr>
                    <th>Tiền dịch vụ</th>
                    <td class="text-end"><?= number_format($row['service_total']) ?> đ</td>
                </tr>

                <tr class="table-danger">
                    <th>Tổng thanh toán</th>
                    <td class="text-end">
                        <h4 class="text-danger">
                            <?= number_format($row['total_amount']) ?> đ
                        </h4>
                    </td>
                </tr>
            </table>
        </div>

    </div>

</div>

<?php include __DIR__ .'/../../includes/footer.php'; ?>