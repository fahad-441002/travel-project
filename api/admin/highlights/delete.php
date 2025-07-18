<?php
require_once '../../../config/db.php';

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Delete highlight
    $stmt = $conn->prepare("DELETE FROM destination_highlights WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: /hassan/admin/destinations/highlights?status=deleted");
        exit;
    } else {
        die("Error deleting record: " . $conn->error);
    }
} else {
    die("Invalid request.");
}
