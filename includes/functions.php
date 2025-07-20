<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../PHPMailer/src/Exception.php';

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
