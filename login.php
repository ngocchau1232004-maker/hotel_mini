<?php
    session_start();
    include 'config/database.php';

    if(isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = md5($_POST['password']);

        $sql = "SELECT * FROM users
                WHERE username='$username'
                AND password='$password'";

        $result = mysqli_query($conn, $sql);

        if(mysqli_num_rows($result) > 0) {

            $user = mysqli_fetch_assoc($result);

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];

            header('Location: dashboard.php');

        }else{
            $error = "Sai tài khoản hoặc mật khẩu";
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Đăng nhập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<script>
    function togglePassword() {
        const password = document.getElementById("password");
        const eyeIcon = document.getElementById("eyeIcon");

        if (password.type === "password") {
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
                        <h4>Đăng nhập</h4>
                    </div>

                    <div class="card-body">
                        <?php
                            if(isset($error)){
                                echo "<div class='alert alert-danger'>$error</div>";
                            }
                        ?>

                        <?php
                        if(isset($_GET['timeout'])){
                            echo "<div class='alert alert-warning'>
                                    Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.
                                </div>";
                        }
                        ?>

                        <form method="POST">

                            <div class="mb-3">
                                <label>Tài khoản</label>
                                <input type="text" name="username" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label>Mật khẩu</label>

                                <div class="input-group">
                                    <input type="password" id="password" name="password" class="form-control">
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
                                        <i id="eyeIcon" class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>    
                            
                            <button type="submit" name="login" class="btn btn-primary w-100">
                                Đăng nhập
                            </button>
                        
                            <div class="text-center mt-3">
                                <a href="register.php"> Đăng ký tài khoản </a>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>      

</body>
</html>