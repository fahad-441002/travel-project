<?php
require 'config/db.php';
require 'helpers/mail_helper.php';

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

    // Get price
    $stmt = $conn->prepare("SELECT title, price FROM destinations WHERE slug = ?");
    $stmt->bind_param("s", $destination_slug);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("Invalid destination");
    }

    $dest = $result->fetch_assoc();
    $destination_title = $dest['title'];
    $price = (float)$dest['price'];
    $total_price = $price * $persons;

    // Update booking
    $stmt = $conn->prepare("UPDATE bookings 
        SET destination_slug=?, travel_date=?, persons=?, total_price=?, status=?, reason=? 
        WHERE id=?");
    $stmt->bind_param("ssidssi", $destination_slug, $travel_date, $persons, $total_price, $status, $reason, $id);
    $stmt->execute();

    // Get booking info
    $stmt = $conn->prepare("SELECT bookings.*, users.name AS user_name, users.email AS user_email, guest_users.name AS guest_name, guest_users.email AS guest_email 
                            FROM bookings 
                            LEFT JOIN users ON bookings.user_id = users.id 
                            LEFT JOIN guest_users ON bookings.guest_id = guest_users.id 
                            WHERE bookings.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $booking_result = $stmt->get_result();

    if ($booking_result->num_rows > 0) {
        $booking = $booking_result->fetch_assoc();
        $recipient_name = $booking['user_name'] ?? $booking['guest_name'] ?? '';
        $recipient_email = $booking['user_email'] ?? $booking['guest_email'] ?? '';

        if ($recipient_email) {
            $subject = ($status === 'Cancelled') ? "Your Booking was Cancelled" : "Your Booking is Confirmed";

            ob_start();
?>
            <div style="font-family: Arial, sans-serif; padding: 20px; max-width: 600px; margin: auto; border: 1px solid #ddd; border-radius: 10px;">
                <h2 style="color: <?= $status === 'Cancelled' ? '#e74c3c' : '#2ecc71' ?>;">
                    <?= $status === 'Cancelled' ? 'Booking Cancelled' : 'Booking Confirmed' ?>
                </h2>
                <p>Dear <?= htmlspecialchars($recipient_name) ?>,</p>
                <p>Your booking has been <strong><?= $status ?></strong> for the following destination:</p>
                <table cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td><strong>Destination:</strong></td>
                        <td><?= htmlspecialchars($destination_title) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Travel Date:</strong></td>
                        <td><?= htmlspecialchars($travel_date) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Persons:</strong></td>
                        <td><?= $persons ?></td>
                    </tr>
                    <tr>
                        <td><strong>Total Price:</strong></td>
                        <td>$<?= number_format($total_price, 2) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td><?= htmlspecialchars($status) ?></td>
                    </tr>
                    <?php if ($status === 'Cancelled' && $reason): ?>
                        <tr>
                            <td><strong>Reason:</strong></td>
                            <td><?= nl2br(htmlspecialchars($reason)) ?></td>
                        </tr>
                    <?php endif; ?>
                </table>
                <p style="margin-top: 20px;">If you have any questions, feel free to reply to this email.</p>
                <p>Thank you,<br><strong>Travel Team</strong></p>
            </div>
<?php
            $emailBody = ob_get_clean();
            sendMail($recipient_email, $subject, $emailBody);
        }
    }

    echo "<script>window.location.href = '/hassan/admin/bookings/manage?updated=1';</script>";
    exit;
} else {
    echo "<script>window.location.href = '/hassan/admin/bookings/manage';</script>";
    exit;
}
