<?php

    $messages = [

        // Thành công
        "add"         => ["success", "Thêm mới thành công."],
        "update"      => ["success", "Cập nhật thành công."],
        "delete"      => ["success", "Xóa thành công."],
        "checkin"     => ["success", "Nhận phòng thành công."],
        "checkout"    => ["success", "Trả phòng thành công."],

        // Lỗi
        
        "error"       => ["danger", "Có lỗi xảy ra."],
        "notfound"    => ["danger", "Không tìm thấy dữ liệu."],
        "date"        => ["danger", "Ngày trả phòng phải sau ngày nhận phòng."],
        "room_exists" => ["danger", "Phòng đã được đặt trong khoảng thời gian này."],
        "checkout_error" => ["danger", "Không thể sửa đơn đã trả phòng hoặc đã hủy."],
        "delete_error" => ["danger", "Không thể xóa phòng đang thuê hoặc đã check-out."],
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