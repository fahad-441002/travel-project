<?php
$base = '/hassan';

require_once 'includes/redirect_if_logged_in.php';

require 'config/db.php';

$errors = [];
$email = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Input validation
    if (empty($email)) {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    }

    if (empty($password)) {
        $errors['password'] = 'Password is required.';
    }

    // If no validation errors, check login
    if (empty($errors)) {
        $stmt = $conn->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;

            // Handle redirectTo if present
            $redirectTo = $_GET['redirectTo'] ?? null;
            if ($redirectTo && strpos($redirectTo, '..') === false) {
                header('Location: ' . $base . '?' . $redirectTo);
                exit;
            }

            // Default dashboards
            if ($user['role'] === 'admin') {
                header('Location: admin/dashboard');
            } else {
                header('Location: user/dashboard');
            }
            exit;
        } else {
            $errors['general'] = 'Invalid email or password.';
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - Travel and Tour Management System</title>
    <link rel="stylesheet" href="assets/css/style.css" />
    <style>
        .error-msg {
            display: block;
            color: red;
            font-size: 14px;
            margin-top: 5px;
        }

        .auth-form input {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="auth-container">
        <h2>Login to Your Account</h2>

        <?php if (!empty($errors['general'])): ?>
            <span class="error-msg"><?= $errors['general'] ?>test</span>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required />
            <?php if (!empty($errors['email'])): ?>
                <div class="error-msg"><?= $errors['email'] ?></div>
            <?php endif; ?>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required />
            <?php if (!empty($errors['password'])): ?>
                <div class="error-msg"><?= $errors['password'] ?></div>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary">Login</button>
        </form>

        <?php
        $redirectTo = $_GET['redirectTo'] ?? '';
        $registerUrl = $base . '/register';
        if ($redirectTo) {
            $registerUrl .= '?redirectTo=' . urlencode($redirectTo);
        }
        ?>
        <p>Don't have an account? <a href="<?= $registerUrl ?>">Register here</a></p>

    </div>
</body>

</html>