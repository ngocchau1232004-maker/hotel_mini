<?php
include '../../includes/auth.php';
include '../../includes/admin_auth.php';
include '../../config/database.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = mysqli_prepare($conn, "SELECT user_id, full_name, username FROM users WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$user) {
    header("Location: index.php");
    exit();
}

$error = '';

if (isset($_POST['reset'])) {
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (strlen($password) < 6) {
        $error = 'Mật khẩu phải có ít nhất 6 ký tự.';
    } elseif ($password !== $confirm) {
        $error = 'Mật khẩu xác nhận không khớp.';
    } else {
        $password_md5 = md5($password);
        $stmt = mysqli_prepare($conn, "UPDATE users SET password=? WHERE user_id=?");
        mysqli_stmt_bind_param($stmt, "si", $password_md5, $id);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php?success=password");
            exit();
        }
        $error = 'Không thể đổi mật khẩu.';
    }
}

include '../../includes/header.php';
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0"><i class="bi bi-key"></i> Đổi mật khẩu</h4>
        </div>
        <div class="card-body">
            <p>Đổi mật khẩu cho tài khoản <strong><?= htmlspecialchars($user['username']); ?></strong>
               (<?= htmlspecialchars($user['full_name']); ?>).</p>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Mật khẩu mới</label>
                    <input type="password" name="password" class="form-control" minlength="6" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Xác nhận mật khẩu</label>
                    <input type="password" name="confirm_password" class="form-control" minlength="6" required>
                </div>

                <button type="submit" name="reset" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Cập nhật mật khẩu
                </button>
                <a href="index.php" class="btn btn-secondary">Trở về</a>
            </form>
        </div>
    </div>
</div>


<?php include __DIR__.'/../../includes/footer.php'; ?>