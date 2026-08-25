<?php
include '../../includes/auth.php';
include '../../includes/admin_auth.php';
include '../../config/database.php';

$id = (int)($_GET['id'] ?? 0);

if ($id === (int)$_SESSION['user_id']) {
    header("Location: index.php?error=self_delete");
    exit();
}

// Không cho xóa Admin cuối cùng.
$stmt = mysqli_prepare($conn, "SELECT role_id FROM users WHERE user_id=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$user) {
    header("Location: index.php?error=not_found");
    exit();
}

if ((int)$user['role_id'] === 1) {
    $admin_count = mysqli_fetch_assoc(mysqli_query(
        $conn, "SELECT COUNT(*) AS total FROM users WHERE role_id=1"
    ));
    if ((int)$admin_count['total'] <= 1) {
        header("Location: index.php?error=last_admin");
        exit();
    }
}

$stmt = mysqli_prepare($conn, "DELETE FROM users WHERE user_id=?");
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    header("Location: index.php?success=deleted");
} else {
    header("Location: index.php?error=delete_failed");
}
exit();
?>