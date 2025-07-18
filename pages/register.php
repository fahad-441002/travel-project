<?php
$base = '/hassan';

require_once 'includes/redirect_if_logged_in.php';

require 'config/db.php';

$errors = [];
$name = $email = '';
$redirectTo = $_GET['redirectTo'] ?? '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize inputs
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (empty($name)) {
        $errors['name'] = 'Name is required.';
    }

    if (empty($email)) {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    }

    if (empty($password)) {
        $errors['password'] = 'Password is required.';
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'Password must be at least 6 characters.';
    }

    if (empty($confirm_password)) {
        $errors['confirm_password'] = 'Please confirm your password.';
    } elseif ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    // Check if email already exists
    if (empty($errors)) {
        $checkStmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
        $checkStmt->bind_param('s', $email);
        $checkStmt->execute();
        $checkStmt->store_result();
        if ($checkStmt->num_rows > 0) {
            $errors['email'] = 'Email already exists. Try logging in.';
        }
        $checkStmt->close();
    }

    // If no errors, insert new user
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $role = 'user';
        $stmt = $conn->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $name, $email, $hashedPassword, $role);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            $redirectUrl = 'login';
            if (!empty($redirectTo)) {
                $redirectUrl .= '?redirectTo=' . urlencode($redirectTo);
            }
            header('Location: ' . $redirectUrl);
            exit;
        } else {
            $errors['general'] = 'Something went wrong: ' . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Register - Travel and Tour Management System</title>
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
        <h2>Create a New Account</h2>

        <?php if (!empty($errors['general'])): ?>
            <span class="error-msg"><?= $errors['general'] ?></span>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($name ?? '') ?>" required />
            <?php if (!empty($errors['name'])): ?>
                <div class="error-msg"><?= $errors['name'] ?></div>
            <?php endif; ?>

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

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required />
            <?php if (!empty($errors['confirm_password'])): ?>
                <div class="error-msg"><?= $errors['confirm_password'] ?></div>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary">Register</button>
        </form>

        <?php
        $loginUrl = $base . '/login';
        if (!empty($redirectTo)) {
            $loginUrl .= '?redirectTo=' . urlencode($redirectTo);
        }
        ?>
        <p>Already have an account? <a href="<?= $loginUrl ?>">Login here</a></p>

    </div>
</body>

</html>