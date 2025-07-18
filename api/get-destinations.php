<?php
require_once '../config/db.php';
header('Content-Type: application/json');

$stmt = $conn->prepare("SELECT slug, title, price, duration FROM destinations");
$stmt->execute();
$res = $stmt->get_result();

$destinations = [];
while ($row = $res->fetch_assoc()) {
    $destinations[$row['slug']] = [
        'name' => $row['title'],
        'price' => (float)$row['price'],
        'duration' => $row['duration'] . ' Days Tour'
    ];
}

echo json_encode($destinations);
exit;
