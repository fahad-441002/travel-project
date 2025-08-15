<?php
require_once 'config/db.php';

// Fetch custom bookings
$sql = "SELECT cb.*, u.name AS user_name, gu.name AS guest_name 
        FROM custom_bookings cb 
        LEFT JOIN users u ON cb.user_id = u.id 
        LEFT JOIN guest_users gu ON cb.guest_id = gu.id 
        ORDER BY cb.id DESC";

$result = $conn->query($sql);
?>

<div class="">
    <h2 class="mb-4">All Custom Bookings</h2>

    <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive bg-white shadow-sm rounded">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Custom Destination</th>
                        <th>Travel Date</th>
                        <th>People</th>
                        <th>User Type</th>
                        <th>Message</th>
                        <th>Booked At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['name'] ?></td>
                            <td><?= $row['email'] ?></td>
                            <td><?= $row['phone'] ?></td>
                            <td><?= $row['custom_destination'] ?></td>
                            <td><?= $row['travel_date'] ?></td>
                            <td><?= $row['people'] ?></td>
                            <td>
                                <?= $row['user_id'] ? "User: " . $row['user_name'] : "Guest: " . $row['guest_name'] ?>
                            </td>
                            <td><?= nl2br($row['message']) ?></td>
                            <td><?= $row['created_at'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No custom bookings found.</div>
    <?php endif; ?>
</div>