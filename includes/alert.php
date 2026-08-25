<?php

    $messages = [

        // Thành công
        "add"         => ["success", "Thêm mới thành công."],
        "update"      => ["success", "Cập nhật thành công."],
        "delete"      => ["success", "Xóa thành công."],
        "created"     => ["success", "Tạo tài khoản thành công."],
        "updated"     => ["success", "Cập nhật tài khoản thành công."],
        "password"    => ["success", "Đổi mật khẩu thành công."],
        "deleted"     => ["success", "Xóa tài khoản thành công."],
        "checkin"     => ["success", "Nhận phòng thành công."],
        "checkout"    => ["success", "Trả phòng thành công."],

        // Lỗi
        
        "error"       => ["danger", "Có lỗi xảy ra."],
        "notfound"    => ["danger", "Không tìm thấy dữ liệu."],
        "date"        => ["danger", "Ngày trả phòng phải sau ngày nhận phòng."],
        "room_exists" => ["danger", "Phòng đã được đặt trong khoảng thời gian này."],
        "checkout_error" => ["danger", "Không thể sửa đơn đã trả phòng hoặc đã hủy."],
        "delete_error" => ["danger", "Không thể xóa phòng đang thuê hoặc đã check-out."],
        "self_delete" => ["danger", "Bạn không thể xóa tài khoản đang đăng nhập."],
        "last_admin" => ["danger", "Không thể xóa Admin cuối cùng của hệ thống."],
        "delete_failed" => ["danger", "Không thể xóa tài khoản."],
        "not_found" => ["danger", "Không tìm thấy tài khoản."],
    ];

    $key = "";

    if(isset($_GET['success'])){
        $key = $_GET['success'];
    }

    if(isset($_GET['error'])){
        $key = $_GET['error'];
    }

    if($key != "" && isset($messages[$key])){

        $type = $messages[$key][0];
        $text = $messages[$key][1];
    ?>

    <div class="alert alert-<?= $type ?> alert-dismissible fade show">

        <?= $text ?>

        <button class="btn-close" data-bs-dismiss="alert"></button>

    </div>

<?php } ?>