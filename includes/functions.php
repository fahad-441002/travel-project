<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/PHPMailer/src/Exception.php';

function slugify($text)
{
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text)));
}

function uploadFile($file, $uploadDir = '/assets/images/')
{
    if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $fileName = strtolower(str_replace(' ', '_', $file['name']));
    $fileName = preg_replace('/\s/', '_', $fileName);

    // ABSOLUTE path for saving on disk
    $absolutePath = $_SERVER['DOCUMENT_ROOT'] . '/hassan' . $uploadDir . time() . '_' . $fileName;

    // RELATIVE path to save in DB
    $dbPath = $uploadDir . time() . '_' . $fileName;

    if (move_uploaded_file($file['tmp_name'], $absolutePath)) {
        return $dbPath; // return path relative to project
    }

    return null;
}

function sendBookingEmails($user, $destination, $booking)
{
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mfd84739@gmail.com'; // your Gmail
        $mail->Password = 'sadrlfuxmoitgruk'; // your Gmail app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email content
        $userEmail = $user['email'];
        $userName = $user['name'];
        $subjectUser = "Booking Confirmation - {$destination['title']}";
        $messageUser = "
            <h2>Hello {$userName},</h2>
            <p>Thank you for booking <strong>{$destination['title']}</strong> with us.</p>
            <p>Status: <strong>Pending</strong></p>
            <p><strong>Travel Date:</strong> {$booking['travel_date']}</p>
            <p><strong>Persons:</strong> {$booking['persons']}</p>
            <p><strong>Total Price:</strong> $" . number_format($booking['total_price'], 2) . "</p>
            <p>We'll contact you soon to confirm your booking.</p>
        ";

        // Send to user
        $mail->setFrom('mfd84739@gmail.com', 'ExploreWorld');
        $mail->addAddress($userEmail, $userName);
        $mail->isHTML(true);
        $mail->Subject = $subjectUser;
        $mail->Body    = $messageUser;
        $mail->send();

        // Send to admin
        $mail->clearAddresses();
        $mail->addAddress('hassanaltaf468348@gmail.com', 'Admin'); // Admin email
        $mail->Subject = "🧾 New Booking from {$userName}";
        $mail->Body    = "
            <h3>New Booking Details:</h3>
            <ul>
                <li><strong>User:</strong> {$userName}</li>
                <li><strong>Email:</strong> {$userEmail}</li>
                <li><strong>Phone:</strong> {$booking['phone']}</li>
                <li><strong>Destination:</strong> {$destination['title']}</li>
                <li><strong>Date:</strong> {$booking['travel_date']}</li>
                <li><strong>Persons:</strong> {$booking['persons']}</li>
                <li><strong>Total:</strong> $" . number_format($booking['total_price'], 2) . "</li>
                <li><strong>Message:</strong> " . htmlspecialchars($booking['message']) . "</li>
            </ul>
        ";
        $mail->send();
    } catch (Exception $e) {
        error_log("Email Error: {$mail->ErrorInfo}");
    }
}
