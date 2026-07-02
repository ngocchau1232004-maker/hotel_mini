<?php
    include '../../includes/auth.php';
    include '../../config/database.php';
    include '../../includes/header.php';

    /** @var mysqli $conn */
    $conn = $conn;

    if(isset($_POST['save'])){

        $type_name = $_POST['type_name'];
        $price = $_POST['price'];
        $max_people = $_POST['max_people'];
        $description = $_POST['description'];

        $sql = "INSERT INTO room_types(type_name, price, max_people, description)
                    VALUES('$type_name','$price','$max_people','$description')";

        mysqli_query($conn,$sql);

        header("Location:index.php");
    }
?>


    <div class="container mt-4">
        <h2>Thêm loại phòng</h2>

        <form method="POST">
            <label>Tên loại phòng</label>
            <input type="text" name="type_name" class="form-control">
            <br>

            
            <label>Giá</label>
            <div class="input-group">
                <input type="number" name="price" class="form-control" min="0"step="10000"value="100000">
                <span class="input-group-text">VNĐ</span>
                
            </div><br>

            <label>Số người</label>
            <input type="number" name="max_people" class="form-control">
            <br>

            <label>Mô tả</label>
            <textarea name="description" class="form-control"></textarea>
            <br>

    
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success" name="save">
                    Thêm
                </button>

                <a href="index.php" class="btn btn-secondary">
                    Trở về
                </a>
            </div>
                
           
        </form>

    </div>

<?php include __DIR__.'/../../includes/footer.php'; ?>