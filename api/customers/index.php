<?php

require_once '../auth.php';

/*
|--------------------------------------------------------------------------
| GET hoặc POST
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $keyword = isset($_GET['keyword']) ? str($_GET['keyword']) : "";
    $gender  = isset($_GET['gender']) ? str($_GET['gender']) : "";

} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $data = getJson();

    $keyword = isset($data['keyword']) ? str($data['keyword']) : "";
    $gender  = isset($data['gender']) ? str($data['gender']) : "";

} else {

    error("Method không được hỗ trợ.",405);

}

/*
|--------------------------------------------------------------------------
| SQL
|--------------------------------------------------------------------------
*/

$sql = "SELECT *
        FROM customers
        WHERE 1=1 ";

if($keyword!=""){

    $sql .= " AND (
                full_name LIKE '%$keyword%'
                OR phone LIKE '%$keyword%'
                OR id_card LIKE '%$keyword%'
            )";

}

if($gender!=""){

    $sql .= " AND gender='$gender' ";

}

$sql .= " ORDER BY customer_id DESC";

$query = mysqli_query($conn,$sql);

if(!$query){

    error(mysqli_error($conn),500);

}

/*
|--------------------------------------------------------------------------
| Data
|--------------------------------------------------------------------------
*/

$list=[];

while($row=mysqli_fetch_assoc($query)){

    $list[]=[

        "customer_id" => (int)$row['customer_id'],
        "full_name"   => $row['full_name'],
        "gender"      => $row['gender'],
        "phone"       => $row['phone'],
        "email"       => $row['email'],
        "id_card"     => $row['id_card'],
        "address"     => $row['address'],
        "created_at"  => $row['created_at']

    ];

}

success([
    "total"=>count($list),
    "customers"=>$list
],"Danh sách khách hàng");

?>