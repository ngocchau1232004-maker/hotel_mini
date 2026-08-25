<?php
include '../../includes/auth.php';
include '../../includes/admin_auth.php';
include '../../config/database.php';
include '../../includes/header.php';

$sql = "SELECT u.user_id, u.full_name, u.username, u.phone, u.email,
               u.created_at, u.role_id, r.role_name
        FROM users u
        INNER JOIN roles r ON u.role_id = r.role_id
        ORDER BY u.user_id DESC";
$result = mysqli_query($conn, $sql);
?>

<div class="container-fluid">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                <i class="bi bi-person-gear"></i>
                Quản lý tài khoản
            </h4>
            <a href="create.php" class="btn btn-light">
                <i class="bi bi-person-plus"></i>
                Thêm tài khoản
            </a>
        </div>

        <div class="card-body">
            <?php include '../../includes/alert.php'; ?>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Mã TK</th>
                            <th>Họ tên</th>
                            <th>Tài khoản</th>
                            <th>Số điện thoại</th>
                            <th>Email</th>
                            <th>Vai trò</th>
                            <th>Ngày tạo</th>
                            <th width="220">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= (int)$row['user_id']; ?></td>
                            <td><?= htmlspecialchars($row['full_name']); ?></td>
                            <td><?= htmlspecialchars($row['username']); ?></td>
                            <td><?= htmlspecialchars($row['phone'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($row['email'] ?? ''); ?></td>
                            <td>
                                <?php if ((int)$row['role_id'] === 1): ?>
                                    <span class="badge bg-danger">Admin</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Nhân viên</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['created_at']); ?></td>
                            <td>
                                <a href="edit.php?id=<?= (int)$row['user_id']; ?>"
                                   class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i> Sửa
                                </a>
                                <a href="reset_password.php?id=<?= (int)$row['user_id']; ?>"
                                   class="btn btn-info btn-sm text-white">
                                    <i class="bi bi-key"></i> Đổi MK
                                </a>
                                <?php if ((int)$row['user_id'] !== (int)$_SESSION['user_id']): ?>
                                    <a href="delete.php?id=<?= (int)$row['user_id']; ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Bạn có chắc muốn xóa tài khoản này?')">
                                        <i class="bi bi-trash"></i> Xóa
                                    </a>
                                <?php else: ?>
                                    <span class="badge bg-light text-dark border">Tài khoản hiện tại</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__.'/../../includes/footer.php'; ?>