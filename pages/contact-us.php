<?php
require_once 'config/db.php';

$loggedInUser = $_SESSION['user'] ?? null;
$prefillName = $loggedInUser['name'] ?? '';
$prefillEmail = $loggedInUser['email'] ?? '';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $msg = trim($_POST['message'] ?? '');

    if (!$name || !$email || !$msg) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } else {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $msg);

        if ($stmt->execute()) {
            $message = "Thank you for contacting us! We will get back to you soon.";
        } else {
            $error = "Failed to send your message. Please try again later.";
        }
    }
}
?>


<div class="contact-container">
    <h1>Contact Us</h1>
    <p class="subtitle">We're here to help you plan your perfect trip!</p>

    <div class="contact-wrapper">
        <form class="contact-form" method="POST" action="">
            <?php if ($message): ?>
                <div class="success-msg"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="danger-msg"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <label for="name">Your Name</label>
            <input type="text" id="name" name="name" placeholder="Enter your name" value="<?= htmlspecialchars($prefillName) ?>" required <?= $loggedInUser ? 'readonly' : '' ?> />

            <label for="email">Your Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" value="<?= htmlspecialchars($prefillEmail) ?>" required <?= $loggedInUser ? 'readonly' : '' ?> />

            <label for="message">Message</label>
            <textarea id="message" name="message" placeholder="How can we help you?" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>

            <button type="submit">Send Message</button>
        </form>



        <div class="contact-info">
            <h2>Get in Touch</h2>
            <p><strong>Phone:</strong> +92 300 1234567</p>
            <p><strong>Email:</strong> info@travelandtours.com</p>
            <p><strong>Address:</strong> Gulberg, Lahore, Pakistan</p>
            <div class="socials">
                <a href="#">Facebook</a>
                <a href="#">Instagram</a>
                <a href="#">WhatsApp</a>
            </div>
        </div>
    </div>
</div>