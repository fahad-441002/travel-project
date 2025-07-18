<?php
require_once 'config/db.php';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['status'])) {
    $userId = intval($_POST['user_id']);
    $status = $_POST['status'];

    if (in_array($status, ['default', 'active', 'suspended'])) {
        $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $userId);
        $stmt->execute();
    }
}

// Fetch all users
$result = $conn->query("SELECT id, name, email, role, status, created_at FROM users ORDER BY id DESC");
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<h2>Manage Users</h2>

<table class="table table-bordered table-hover bg-white shadow-sm">
    <thead class="table-light">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Created</th>
            <!-- <th>Action</th> -->
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= $user['role'] ?></td>
                <td>
                    <form method="post" class="d-flex align-items-center">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <select name="status" class="form-select form-select-sm me-2">
                            <option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="suspended" <?= $user['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                        </select>
                        <button class="btn btn-sm btn-primary">Update</button>
                    </form>
                </td>
                <td><?= $user['created_at'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>