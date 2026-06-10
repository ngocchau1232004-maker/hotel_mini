<?php
    include 'config/database.php';

    if(isset($_POST['register'])) {
        $full_name = $_POST['full_name'];
        $username = $_POST['username'];

        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if($password != $confirm_password){
            $error = "Mật khẩu xác nhận không khớp!";
        }else{

            $password = md5($password);

            $sql = "INSERT INTO users(full_name,username,password,role_id)
                    VALUES('$full_name','$username','$password',2)";

            if(mysqli_query($conn, $sql)){
                $success = "Đăng ký thành công!";
            }
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Đăng ký</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<script>
    function togglePassword(inputId, iconId) {
        const password = document.getElementById(inputId);
        const eyeIcon = document.getElementById(iconId);

        if(password.type === "password") {
            password.type = "text";
            eyeIcon.classList.remove("bi-eye");
            eyeIcon.classList.add("bi-eye-slash");

        } else {
            password.type = "password";
            eyeIcon.classList.remove("bi-eye-slash");
            eyeIcon.classList.add("bi-eye");
        }
    }
</script>

<body class="bg-light">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
    
                <div class="card shadow">
                    <div class="card-header text-center">
                        <h3>Đăng ký</h3>
                    </div>

                    <div class="card-body">
                        <?php
                            if(isset($error)){
                                echo "<div class='alert alert-danger'>$error</div>";
                            }

                            if(isset($success)){
                                echo "<div class='alert alert-success'>$success</div>";
                            }
                        ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label>Họ tên</label>
                                <input type="text" name="full_name" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label>Tài khoản</label>
                                <input type="text" name="username" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label>Mật khẩu</label>
                                <div class="input-group">
                                    <input type="password" id="password" name="password" class="form-control">
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password','eyeIcon')">
                                        <i id="eyeIcon" class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label>Xác nhận mật khẩu</label>
                                <div class="input-group">
                                    <input type="password" id="confirm_password" name="confirm_password" class="form-control">

                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('confirm_password','eyeIcon2')">
                                        <i id="eyeIcon2" class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" name="register" class="btn btn-success w-100"> Đăng ký </button>
                            <div class="text-center mt-3">
                                <a href="login.php"> Đã có tài khoản? Đăng nhập </a>
                            </div>
                        
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>