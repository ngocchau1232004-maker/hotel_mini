<?php
include '../../includes/auth.php';
include '../../includes/admin_auth.php';
include '../../config/database.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    header("Location: index.php");
    exit();
}

$error = '';

if (isset($_POST['update'])) {
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $role_id = (int)$_POST['role_id'];

    if ($full_name === '' || $username === '') {
        $error = 'Họ tên và tên tài khoản không được để trống.';
    } elseif ($id === (int)$_SESSION['user_id'] && $role_id !== 1) {
        $error = 'Bạn không thể tự hạ quyền Admin của chính mình.';
    } else {
        $check = mysqli_prepare($conn,
            "SELECT user_id FROM users WHERE username = ? AND user_id <> ?");
        mysqli_stmt_bind_param($check, "si", $username, $id);
        mysqli_stmt_execute($check);
        $exists = mysqli_stmt_get_result($check);

        if (mysqli_num_rows($exists) > 0) {
            $error = 'Tên tài khoản đã tồn tại.';
        } else {
            $stmt = mysqli_prepare($conn,
                "UPDATE users SET full_name=?, username=?, phone=?, email=?, role_id=?
                 WHERE user_id=?");
            mysqli_stmt_bind_param($stmt, "ssssii",
                $full_name, $username, $phone, $email, $role_id, $id);

            if (mysqli_stmt_execute($stmt)) {
                if ($id === (int)$_SESSION['user_id']) {
                    $_SESSION['full_name'] = $full_name;
                    $_SESSION['role_id'] = $role_id;
                }
                header("Location: index.php?success=updated");
                exit();
            }
            $error = 'Không thể cập nhật tài khoản.';
        }
    }
}

$roles = mysqli_query($conn, "SELECT role_id, role_name FROM roles ORDER BY role_id");
include '../../includes/header.php';
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-warning">
            <h4 class="mb-0"><i class="bi bi-pencil-square"></i> Sửa tài khoản</h4>
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
                               value="<?= htmlspecialchars($user['full_name']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tên tài khoản</label>
                        <input type="text" name="username" class="form-control"
                               value="<?= htmlspecialchars($user['username']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control"
                               value="<?= htmlspecialchars($user['phone'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control"
                               value="<?= htmlspecialchars($user['email'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Vai trò</label>
                        <select name="role_id" class="form-select" required>
                            <?php while ($role = mysqli_fetch_assoc($roles)): ?>
                                <option value="<?= (int)$role['role_id']; ?>"
                                    <?= (int)$user['role_id'] === (int)$role['role_id'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($role['role_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <button type="submit" name="update" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Cập nhật
                </button>
                <a href="index.php" class="btn btn-secondary">Trở về</a>
            </form>
        </div>
    </div>
</div>


<?php include __DIR__.'/../../includes/footer.php'; ?>