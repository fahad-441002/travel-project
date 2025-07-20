<?php
require_once '../config/db.php'; // Provides $conn (MySQLi)
require_once '../helpers/mail_helper.php';

header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data || empty($data['name']) || empty($data['date']) || empty($data['people']) || empty($data['contactMethod'])) {
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
        exit;
    }

    // Validate contact method
    if ($data['contactMethod'] === 'email') {
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'error' => 'Invalid or missing email']);
            exit;
        }
    } elseif ($data['contactMethod'] === 'phone') {
        if (empty($data['phone']) || !preg_match('/^\d{10,15}$/', $data['phone'])) {
            echo json_encode(['success' => false, 'error' => 'Invalid or missing phone']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid contact method']);
        exit;
    }

    // === Clean Inputs ===
    $name     = $conn->real_escape_string(trim($data['name']));
    $email    = isset($data['email']) ? $conn->real_escape_string(trim($data['email'])) : '';
    $phone    = isset($data['phone']) ? preg_replace('/\D/', '', $data['phone']) : '';
    $date     = $conn->real_escape_string($data['date']);
    $people   = (int)$data['people'];
    $price    = floatval($data['price'] ?? 0);
    $total    = $price * $people;
    $manual   = $data['manual'] ?? false;
    $agentMsg = isset($data['agentMessage']) ? $conn->real_escape_string($data['agentMessage']) : null;
    $source   = 'chatbot';
    $channel  = ($data['contactMethod'] === 'email' || $data['contactMethod'] === 'phone') ? 'book_now' : 'talk_to_agent';

    $user_id = null;
    $guest_id = null;

    // === Check if user already exists ===
    if (!empty($email)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($user_id);
        $stmt->fetch();
        $stmt->close();
    }

    // === Insert into guest_users if not a registered user ===
    if (!$user_id) {
        $stmt = $conn->prepare("INSERT INTO guest_users (name, email, phone) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $phone);
        $stmt->execute();
        $guest_id = $stmt->insert_id;
        $stmt->close();
    }

    // === Custom (manual) booking ===
    if ($manual) {
        $custom_destination = $conn->real_escape_string($data['customDestination'] ?? 'Custom Destination');

        $stmt = $conn->prepare("INSERT INTO custom_bookings 
            (guest_id, name, email, phone, custom_destination, travel_date, people, message) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("isssssis", $guest_id, $name, $email, $phone, $custom_destination, $date, $people, $agentMsg);
        $stmt->execute();
        $stmt->close();

        // Email to user
        if (!empty($email)) {
            $body = '
    <div style="font-family: Arial, sans-serif; padding: 20px; max-width: 600px; margin: auto; border: 1px solid #ddd; border-radius: 10px;">
        <h2 style="color: #f1c40f;">Custom Booking Received</h2>
        <p>Dear ' . htmlspecialchars($name) . ',</p>
        <p>Your custom booking request has been received for:</p>
        <table cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse;">
            <tr>
                <td><strong>Custom Destination:</strong></td>
                <td>' . htmlspecialchars($custom_destination) . '</td>
            </tr>
            <tr>
                <td><strong>Travel Date:</strong></td>
                <td>' . htmlspecialchars($date) . '</td>
            </tr>
            <tr>
                <td><strong>Persons:</strong></td>
                <td>' . (int)$people . '</td>
            </tr>
        </table>
        <p style="margin-top: 20px;">We’ll get back to you shortly regarding your request.</p>
        <p>Thank you,<br><strong>Travel Team</strong></p>
    </div>';

            sendMail($email, "Custom Booking Request Received", $body, '', $name);
        }

        // Email to admin
        $adminBody = '
<div style="font-family: Arial, sans-serif; padding: 20px; max-width: 600px; margin: auto; border: 1px solid #ddd; border-radius: 10px;">
    <h2 style="color: #3498db;">New Custom Booking Received</h2>
    <p><strong>Name:</strong> ' . htmlspecialchars($name) . '</p>
    <table cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td><strong>Email:</strong></td>
            <td>' . htmlspecialchars($email) . '</td>
        </tr>
        <tr>
            <td><strong>Phone:</strong></td>
            <td>' . htmlspecialchars($phone) . '</td>
        </tr>
        <tr>
            <td><strong>Custom Destination:</strong></td>
            <td>' . htmlspecialchars($custom_destination) . '</td>
        </tr>
        <tr>
            <td><strong>Date:</strong></td>
            <td>' . htmlspecialchars($date) . '</td>
        </tr>
        <tr>
            <td><strong>People:</strong></td>
            <td>' . (int)$people . '</td>
        </tr>
        <tr>
            <td><strong>Message:</strong></td>
            <td>' . nl2br(htmlspecialchars($agentMsg)) . '</td>
        </tr>
    </table>
    <p>Check the admin panel to view full details.</p>
</div>';

        sendMail('mfd84739@gmail.com', "📝 New Custom Booking - $name", $adminBody);

        echo json_encode(['success' => true, 'message' => 'Custom booking saved successfully']);
        exit;
    }

    // === destination booking ===
    $slug  = $conn->real_escape_string($data['destination'] ?? '');

    // Look up the destination by slug
    $stmt = $conn->prepare("SELECT title FROM destinations WHERE slug = ?");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $stmt->bind_result($real_title);
    $stmt->fetch();
    $stmt->close();

    // Fallback title if not found
    $title = $real_title ?: 'Unknown Destination';


    $stmt = $conn->prepare("INSERT INTO bookings 
        (user_id, guest_id, destination_slug, destination_title, phone, travel_date, persons, amount, total_price, status, source, agent_message, channel) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', ?, ?, ?)");

    $stmt->bind_param("iissssiddsss", $user_id, $guest_id, $slug, $title, $phone, $date, $people, $price, $total, $source, $agentMsg, $channel);
    $stmt->execute();
    $stmt->close();

    // Email to user
    if (!empty($email)) {
        $body = '
    <div style="font-family: Arial, sans-serif; padding: 20px; max-width: 600px; margin: auto; border: 1px solid #ddd; border-radius: 10px;">
        <h2 style="color: #f1c40f;">Booking Pending</h2>
        <p>Dear ' . htmlspecialchars($name) . ',</p>
        <p>Your booking has been <strong>Pending</strong> for the following destination:</p>
        <table cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse;">
            <tr>
                <td><strong>Destination:</strong></td>
                <td>' . htmlspecialchars($title) . '</td>
            </tr>
            <tr>
                <td><strong>Travel Date:</strong></td>
                <td>' . htmlspecialchars($date) . '</td>
            </tr>
            <tr>
                <td><strong>Persons:</strong></td>
                <td>' . (int)$people . '</td>
            </tr>
            <tr>
                <td><strong>Total Price:</strong></td>
                <td>$' . number_format($total, 2) . '</td>
            </tr>
            <tr>
                <td><strong>Status:</strong></td>
                <td>Pending</td>
            </tr>
        </table>
        <p style="margin-top: 20px;">We will contact you soon to confirm your booking.</p>
        <p>Thank you,<br><strong>Travel Team</strong></p>
    </div>';

        sendMail($email, "Booking Confirmation - $title", $body, '', $name);
    }

    // Email to admin
    $adminBody = '
<div style="font-family: Arial, sans-serif; padding: 20px; max-width: 600px; margin: auto; border: 1px solid #ddd; border-radius: 10px;">
    <h2 style="color: #3498db;">New Booking Received</h2>
    <p><strong>Name:</strong> ' . htmlspecialchars($name) . '</p>
    <table cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td><strong>Email:</strong></td>
            <td>' . htmlspecialchars($email) . '</td>
        </tr>
        <tr>
            <td><strong>Phone:</strong></td>
            <td>' . htmlspecialchars($phone) . '</td>
        </tr>
        <tr>
            <td><strong>Destination:</strong></td>
            <td>' . htmlspecialchars($title) . '</td>
        </tr>
        <tr>
            <td><strong>Date:</strong></td>
            <td>' . htmlspecialchars($date) . '</td>
        </tr>
        <tr>
            <td><strong>People:</strong></td>
            <td>' . (int)$people . '</td>
        </tr>
        <tr>
            <td><strong>Total Price:</strong></td>
            <td>$' . number_format($total, 2) . '</td>
        </tr>
        <tr>
            <td><strong>Channel:</strong></td>
            <td>' . htmlspecialchars($channel) . '</td>
        </tr>
    </table>
    <p>Check the dashboard for full details.</p>
</div>';

    sendMail('mfd84739@gmail.com', "🧾 New Booking - $name", $adminBody);

    echo json_encode(['success' => true, 'message' => 'Booking saved successfully']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server Error: ' . $e->getMessage()]);
}
