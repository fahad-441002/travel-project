<?php
require_once 'config/db.php';

ini_set('display_errors', 0); // don't show errors to users
ini_set('log_errors', 1);     // log all errors
ini_set('error_log', __DIR__ . 'logs/error.log'); // log file path
error_reporting(E_ALL);       // report all errors

$request = strtok($_GET['page'] ?? 'home', '?');
$path = "pages/$request.php";

// ✅ Security check
if (strpos($request, '..') !== false) {
    http_response_code(403);
    exit("Access denied.");
}

// ✅ Handle dynamic destination slug
if (preg_match('#^destination/([^/]+)$#', $request, $matches)) {
    $slug = $matches[1];

    // Query your destinations table
    $stmt = $conn->prepare("SELECT * FROM destinations WHERE slug = ?");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        http_response_code(404);
        exit("Destination not found.");
    }

    $destination = $result->fetch_assoc();

    // Pass destination data to the view
    $pageTitle = $destination['title'];
    $contentFile = "pages/destination/index.php"; // Your display page

    require_once 'layouts/main.php';
    exit;
}

// ✅ File exists check
if (!file_exists($path)) {
    http_response_code(404);
    exit("Page not found.");
}

// ✅ Determine layout and auth
$layout = 'layouts/main.php';

if (preg_match('/^admin\//', $request)) {
    require_once 'includes/auth_admin.php';
    $layout = 'layouts/admin.php';
} elseif (preg_match('/^user\//', $request)) {
    require_once 'includes/auth_user.php';
    $layout = 'layouts/admin.php';
} elseif (in_array($request, ['login', 'register'])) {
    require_once $path;
    exit;
}

$contentFile = $path;
$pageTitle = ucfirst(basename($request));

require_once $layout;
