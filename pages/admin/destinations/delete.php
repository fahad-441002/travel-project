<?php
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int) $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM destinations WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirect back to manage
        echo "<script>window.location.href = 'manage';</script>";
        exit;
    } else {
        echo "Delete failed: " . $stmt->error;
    }
} else {
    echo "Invalid request.";
}
