<?php
include '../../includes/auth.php';
include '../../includes/admin_auth.php';
include '../../config/database.php';

$error = '';

$roles = mysqli_query($conn, "SELECT role_id, role_name FROM roles ORDER BY role_id");

if (isset($_POST['create'])) {
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $role_id = (int)$_POST['role_id'];

    if ($full_name === '' || $username === '' || $password === '') {
        $error = 'Vui lòng nhập đầy đủ họ tên, tài khoản và mật khẩu.';
    } elseif (strlen($password) < 6) {
        $error = 'Mật khẩu phải có ít nhất 6 ký tự.';
    } else {
        $check = mysqli_prepare($conn, "SELECT user_id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($check, "s", $username);
        mysqli_stmt_execute($check);
        $exists = mysqli_stmt_get_result($check);

        if (mysqli_num_rows($exists) > 0) {
            $error = 'Tên tài khoản đã tồn tại.';
        } else {
            // Giữ đồng bộ với hệ thống hiện tại đang sử dụng MD5.
            $password_md5 = md5($password);
            $stmt = mysqli_prepare($conn,
                "INSERT INTO users(full_name, username, password, phone, email, role_id)
                 VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sssssi",
                $full_name, $username, $password_md5, $phone, $email, $role_id);

            if (mysqli_stmt_execute($stmt)) {
                header("Location: index.php?success=created");
                exit();
            }
            $error = 'Không thể tạo tài khoản.';
        }
    }
}

include '../../includes/header.php';
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="bi bi-person-plus"></i> Thêm tài khoản</h4>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Họ và tên</label>
                        <input type="text" name="full_name" class="form-control"
                               value="<?= htmlspecialchars($_POST['full_name'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tên tài khoản</label>
                        <input type="text" name="username" class="form-control"
                               value="<?= htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mật khẩu</label>
                        <input type="password" name="password" class="form-control" minlength="6" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Vai trò</label>
                        <select name="role_id" class="form-select" required>
                            <?php while ($role = mysqli_fetch_assoc($roles)): ?>
                                <option value="<?= (int)$role['role_id']; ?>">
                                    <?= htmlspecialchars($role['role_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control"
                               value="<?= htmlspecialchars($_POST['phone'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control"
                               value="<?= htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                </div>

                <button type="submit" name="create" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Tạo tài khoản
                </button>
                <a href="index.php" class="btn btn-secondary">Trở về</a>
            </form>
        </div>
    </div>
</div>


<?php include __DIR__.'/../../includes/footer.php'; ?>