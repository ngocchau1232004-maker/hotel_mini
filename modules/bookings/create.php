<?php
    include '../../includes/auth.php';
    include '../../config/database.php';

    if(isset($_POST['save'])){
        mysqli_begin_transaction($conn);

        try{
            $type = $_POST['customer_type'];

            if($type=="new"){

                $full_name = trim($_POST['full_name']);
                $gender    = $_POST['gender'];
                $phone     = trim($_POST['phone']);
                $email     = trim($_POST['email']);
                $id_card   = trim($_POST['id_card']);
                $address   = trim($_POST['address']);

                if($full_name==""){
                    echo "<script>
                        alert('Vui lòng nhập họ tên khách hàng!');
                        window.history.back();
                    </script>";
                    exit();
                }

                // Kiểm tra CCCD
                if($id_card!=""){
                    $check = mysqli_query($conn,"SELECT customer_id
                                FROM customers WHERE id_card='$id_card' ");

                    if(mysqli_num_rows($check)>0){
                        echo "<script>
                                alert('CCCD đã tồn tại!');
                                window.history.back();
                            </script>";
                        exit();
                    }
                }

                $result = mysqli_query($conn,"INSERT 
                    INTO customers(full_name,gender,phone,email,id_card,address)
                    VALUES('$full_name','$gender','$phone','$email','$id_card','$address') ");
                
                if(!$result){
                    throw new Exception("Lỗi thêm khách hàng");
                }
                $customer_id = mysqli_insert_id($conn);

            }else{
                if(empty($_POST['customer_id'])){
                    echo "<script>
                            alert('Vui lòng chọn khách hàng!');
                            window.history.back();
                        </script>";
                    exit();
                }
                $customer_id = $_POST['customer_id'];
            }

            $room_id = $_POST['room_id'];
            $check_in = $_POST['check_in_date'];
            $check_out = $_POST['check_out_date'];

            // Kiểm tra ngày nhận và ngày trả
            if(strtotime($check_out) <= strtotime($check_in)){
                echo "<script>
                    alert('Ngày trả phòng phải sau ngày nhận phòng!');
                    window.history.back();
                </script>";
                exit();
            }

            // Kiểm tra phòng đã được đặt trong khoảng thời gian này chưa
            $check_sql = "SELECT b.booking_id FROM bookings b JOIN booking_details bd
                ON b.booking_id = bd.booking_id WHERE bd.room_id = '$room_id'
                AND b.status IN ('Chờ xác nhận','Đã xác nhận','Đang thuê')
                AND (b.check_in_date < '$check_out' AND b.check_out_date > '$check_in')";

            $check_result = mysqli_query($conn, $check_sql);

            if(mysqli_num_rows($check_result) > 0){
                echo "<script>
                        alert('Phòng này đã được đặt trong khoảng thời gian đã chọn!');
                        window.history.back();
                    </script>";
                exit();
            }

            // Lấy giá phòng
            $price_sql = "SELECT rt.price FROM rooms r JOIN room_types rt
                            ON r.room_type_id = rt.room_type_id
                            WHERE r.room_id = '$room_id' ";

            $price_result = mysqli_query($conn, $price_sql);
            $price_row = mysqli_fetch_assoc($price_result);

            if(!$price_row){
                mysqli_rollback($conn);
                echo "<script>
                        alert('Không tìm thấy thông tin phòng!');
                        window.history.back();
                    </script>";
                exit();
            }
            $price = $price_row['price'];

            // Tính số ngày
            $days = ceil(
                (strtotime($check_out) - strtotime($check_in))
                / 86400
            );

            if($days <= 0){
                $days = 1;
            }

            $total = $price * $days;

            // Thêm booking
            $sql = "INSERT INTO bookings(customer_id,check_in_date,check_out_date,total_amount,status)
                    VALUES('$customer_id','$check_in','$check_out','$total','Chờ xác nhận')";

            if(!mysqli_query($conn,$sql)){
                throw new Exception("Lỗi tạo booking");
            }
            $booking_id = mysqli_insert_id($conn);

            // Thêm chi tiết phòng(booking_details)
            if(!mysqli_query($conn,"INSERT INTO booking_details(booking_id,room_id,price)
                VALUES('$booking_id','$room_id','$price')")
            ){
                throw new Exception("Lỗi tạo chi tiết phòng");
            }

            // Đổi trạng thái phòng
            if(!mysqli_query($conn,"UPDATE rooms SET status='Đã đặt' WHERE room_id='$room_id' ")){
                throw new Exception("Lỗi cập nhật trạng thái phòng");
            }
            mysqli_commit($conn);
            
            header("Location:index.php");
            exit();

        }catch(Exception $e){
            mysqli_rollback($conn);

            echo "<script>
                    alert('Có lỗi xảy ra khi đặt phòng!');
                    window.history.back();
                </script>";
            exit();
        }
    }

    // Khách hàng
    $customers = mysqli_query($conn,"
        SELECT *
        FROM customers
        ORDER BY full_name
    ");

    // Phòng trống
    $rooms = mysqli_query($conn,"
        SELECT
            r.room_id,
            r.room_number,
            rt.type_name,
            rt.price
        FROM rooms r
        JOIN room_types rt
            ON r.room_type_id = rt.room_type_id
        WHERE r.status='Trống'
    ");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Thêm đặt phòng</title>

    <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body>

    <div class="container mt-4">

        <h2>Thêm đặt phòng</h2>

        <form method="POST">

            <div class="mb-3">
                <label>
                    <input type="radio" name="customer_type" value="old" checked
                        onclick="toggleCustomer()">
                        Khách cũ
                </label>

                &nbsp;&nbsp;

                <label>
                    <input type="radio" name="customer_type" value="new"
                        onclick="toggleCustomer()">
                    Khách mới
                </label>
            </div>

            <div id="oldCustomer">
                <label>Chọn khách hàng</label>

                <select name="customer_id" class="form-control">
                    <option value="">-- Chọn khách hàng --</option>

                    <?php while($c=mysqli_fetch_assoc($customers)){ ?>
                        <option value="<?= $c['customer_id']; ?>">
                            <?= $c['full_name']; ?>
                            -
                            <?= $c['phone']; ?>
                        </option>
                    <?php } ?>

                </select>
            </div>

            <div id="newCustomer" style="display:none;">

                <div class="mb-3">
                    <label>Họ tên</label>
                    <input type="text" name="full_name" id="full_name" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Giới tính</label>
                    <select name="gender" class="form-control">
                        <option>Nam</option>
                        <option>Nữ</option>
                        <option>Khác</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Số điện thoại</label>
                    <input type="text" name="phone" id="phone" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" id="email" class="form-control">
                </div>

                <div class="mb-3">
                    <label>CCCD</label>
                    <input type="text" name="id_card" id="id_card" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Địa chỉ</label>
                    <textarea name="address" id="address" class="form-control"></textarea>
                </div>

            </div>

            <div class="mb-3">
                <label>Phòng</label>
                <select name="room_id" class="form-control" required>

                    <option value="">
                        -- Chọn phòng --
                    </option>

                    <?php while($r = mysqli_fetch_assoc($rooms)){ ?>

                        <option value="<?= $r['room_id']; ?>">
                            Phòng <?= $r['room_number']; ?>
                            -
                            <?= $r['type_name']; ?>
                            -
                            <?= number_format($r['price']); ?> VNĐ
                        </option>

                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Ngày nhận phòng</label>
                <input type="date" name="check_in_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Ngày trả phòng</label>
                <input type="date" name="check_out_date" class="form-control" required>
            </div>

            <button type="submit" name="save" class="btn btn-primary">
                Lưu
            </button>

            <a href="index.php" class="btn btn-secondary">
                Quay lại
            </a>

        </form>

    </div>

    <script>
        function toggleCustomer(){
            let type=document.querySelector(
                'input[name="customer_type"]:checked'
            ).value;

            if(type=="old"){
                document.getElementById("oldCustomer").style.display="block";
                document.getElementById("newCustomer").style.display="none";

                document.getElementById("full_name").required=false;
                document.getElementById("phone").required=false;
                document.getElementById("id_card").required=false;

            }else{
                document.getElementById("oldCustomer").style.display="none";
                document.getElementById("newCustomer").style.display="block";

                document.getElementById("full_name").required=true;
                document.getElementById("phone").required=true;
                document.getElementById("id_card").required=true;
            }
        }
        toggleCustomer();
    </script>

</body>
</html>