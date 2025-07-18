<?php
require 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $destination_slug = $_POST['destination_slug'] ?? null;
    $travel_date = $_POST['travel_date'] ?? null;
    $persons = isset($_POST['persons']) ? (int)$_POST['persons'] : null;
    $status = $_POST['status'] ?? null;
    $reason = ($status === 'Cancelled') ? trim($_POST['reason']) : null;

    if (!$id || !$destination_slug || !$travel_date || !$persons || !$status) {
        die("Missing booking data");
    }

    $stmt = $conn->prepare("SELECT price FROM destinations WHERE slug = ?");
    $stmt->bind_param("s", $destination_slug);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("Invalid destination");
    }

    $dest = $result->fetch_assoc();
    $price = (float)$dest['price'];
    $total_price = $price * $persons;

    $stmt = $conn->prepare("UPDATE bookings 
        SET destination_slug=?, travel_date=?, persons=?, total_price=?, status=?, reason=? 
        WHERE id=?");
    $stmt->bind_param("ssidssi", $destination_slug, $travel_date, $persons, $total_price, $status, $reason, $id);
    $stmt->execute();

    // ✅ No output before this!
    echo "<script>window.location.href = '/hassan/admin/bookings/manage?updated=1';</script>";
    exit;
} else {
    // redirect if not POST
    echo "<script>window.location.href = '/hassan/admin/bookings/manage';</script>";
    exit;
}
