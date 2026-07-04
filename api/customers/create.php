<?php

//* |==========================================================
//* | HOTEL MINI API
//* | File: api/Customers/Create.php
//* | POST: http://localhost/hotel_mini/api/customers/create.php
//* ==========================================================


require_once '../auth.php';

/*
|--------------------------------------------------------------------------
| GET hoặc POST
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $data = getJson();

    $full_name = isset($data['full_name']) ? str($data['full_name']) : "";
    $gender    = isset($data['gender']) ? str($data['gender']) : "";
    $phone     = isset($data['phone']) ? str($data['phone']) : "";
    $email     = isset($data['email']) ? str($data['email']) : "";
    $id_card   = isset($data['id_card']) ? str($data['id_card']) : "";
    $address   = isset($data['address']) ? str($data['address']) : "";

} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $full_name = isset($_GET['full_name']) ? str($_GET['full_name']) : "";
    $gender    = isset($_GET['gender']) ? str($_GET['gender']) : "";
    $phone     = isset($_GET['phone']) ? str($_GET['phone']) : "";
    $email     = isset($_GET['email']) ? str($_GET['email']) : "";
    $id_card   = isset($_GET['id_card']) ? str($_GET['id_card']) : "";
    $address   = isset($_GET['address']) ? str($_GET['address']) : "";

} else {

    error("Method không được hỗ trợ.",405);

}

/*
|--------------------------------------------------------------------------
| Validate
|--------------------------------------------------------------------------
*/

if($full_name==""){
    error("Vui lòng nhập họ tên.");
}

if($gender==""){
    $gender="Nam";
}

if($phone==""){
    error("Vui lòng nhập số điện thoại.");
}

if(!preg_match('/^[0-9]{10,11}$/',$phone)){
    error("Số điện thoại không hợp lệ.");
}

if($email!="" && !filter_var($email,FILTER_VALIDATE_EMAIL)){
    error("Email không hợp lệ.");
}

if($id_card==""){
    error("Vui lòng nhập CCCD.");
}

if(!preg_match('/^[0-9]{9,12}$/',$id_card)){
    error("CCCD không hợp lệ.");
}

/*
|--------------------------------------------------------------------------
| Kiểm tra trùng SĐT
|--------------------------------------------------------------------------
*/

$sql="SELECT customer_id
      FROM customers
      WHERE phone='$phone'
      LIMIT 1";

$query=mysqli_query($conn,$sql);

if(mysqli_num_rows($query)>0){
    error("Số điện thoại đã tồn tại.");
}

/*
|--------------------------------------------------------------------------
| Kiểm tra trùng CCCD
|--------------------------------------------------------------------------
*/

$sql="SELECT customer_id
      FROM customers
      WHERE id_card='$id_card'
      LIMIT 1";

$query=mysqli_query($conn,$sql);

if(mysqli_num_rows($query)>0){
    error("CCCD đã tồn tại.");
}

/*
|--------------------------------------------------------------------------
| Insert
|--------------------------------------------------------------------------
*/

$sql="INSERT INTO customers(

        full_name,
        gender,
        phone,
        email,
        id_card,
        address

    )

    VALUES(

        '$full_name',
        '$gender',
        '$phone',
        '$email',
        '$id_card',
        '$address'

    )";

if(!mysqli_query($conn,$sql)){
    error(mysqli_error($conn),500);
}

$id=mysqli_insert_id($conn);

/*
|--------------------------------------------------------------------------
| Lấy khách vừa tạo
|--------------------------------------------------------------------------
*/

$sql="SELECT *
      FROM customers
      WHERE customer_id='$id'
      LIMIT 1";

$query=mysqli_query($conn,$sql);

$row=mysqli_fetch_assoc($query);

/*
|--------------------------------------------------------------------------
| Response
|--------------------------------------------------------------------------
*/

success([

    "customer"=>$row

],"Thêm khách hàng thành công.");

?>