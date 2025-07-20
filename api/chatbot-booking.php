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
            $body = "<h2>Thank you, $name!</h2>
                     <p>Your custom booking request for <strong>$custom_destination</strong> has been received.</p>
                     <p>We’ll get back to you shortly.</p>";
            sendMail($email, "Custom Booking Request Received", $body, '', $name);
        }

        // Email to admin
        $adminBody = "<h3>New Custom Booking</h3>
                      <p><strong>Name:</strong> $name<br>
                         <strong>Email:</strong> $email<br>
                         <strong>Phone:</strong> $phone<br>
                         <strong>Custom Destination:</strong> $custom_destination<br>
                         <strong>Date:</strong> $date<br>
                         <strong>People:</strong> $people<br>
                         <strong>Message:</strong> $agentMsg</p>";
        sendMail('admin@yourdomain.com', "New Custom Booking", $adminBody);

        echo json_encode(['success' => true, 'message' => 'Custom booking saved successfully']);
        exit;
    }

    // === Normal destination booking ===
    $slug  = $conn->real_escape_string($data['destination'] ?? '');
    $title = $conn->real_escape_string($data['title'] ?? '');

    $stmt = $conn->prepare("INSERT INTO bookings 
        (user_id, guest_id, destination_slug, destination_title, phone, travel_date, persons, amount, total_price, status, source, agent_message, channel) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', ?, ?, ?)");

    $stmt->bind_param("iissssiddsss", $user_id, $guest_id, $slug, $title, $phone, $date, $people, $price, $total, $source, $agentMsg, $channel);
    $stmt->execute();
    $stmt->close();

    // Email to user
    if (!empty($email)) {
        $body = "<h2>Thank you, $name!</h2>
                 <p>Your booking for <strong>$title</strong> on <strong>$date</strong> has been received.</p>
                 <p><strong>Persons:</strong> $people<br>
                    <strong>Total Price:</strong> $$total<br>
                    <strong>Status:</strong> Pending</p>
                 <p>We will contact you soon.</p>";
        sendMail($email, "Booking Confirmation - $title", $body, '', $name);
    }

    // Email to admin
    $adminBody = "<h3>New Booking</h3>
                  <p><strong>Name:</strong> $name<br>
                     <strong>Email:</strong> $email<br>
                     <strong>Phone:</strong> $phone<br>
                     <strong>Destination:</strong> $title<br>
                     <strong>Date:</strong> $date<br>
                     <strong>People:</strong> $people<br>
                     <strong>Channel:</strong> $channel</p>";
    sendMail('admin@yourdomain.com', "New Booking", $adminBody);

    echo json_encode(['success' => true, 'message' => 'Booking saved successfully']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server Error: ' . $e->getMessage()]);
}
