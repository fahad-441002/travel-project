<?php
require_once '../../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null; // for edit
    $destination_id = $_POST['destination_id'];
    $video_title = $_POST['video_title'];
    $video_description = $_POST['video_description'];
    $video_type = $_POST['video_type'];
    $video_url = '';

    // Handle YouTube link or MP4 upload
    if ($video_type === 'youtube') {
        $video_url = trim($_POST['video_url']);
    } elseif ($video_type === 'mp4') {
        if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['video_file']['name'], PATHINFO_EXTENSION);
            if (!in_array(strtolower($ext), ['mp4'])) {
                die('Only MP4 files are allowed.');
            }

            $uploadDir = '../../../uploads/videos/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $filename = uniqid('vid_', true) . '.' . $ext;
            $uploadPath = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['video_file']['tmp_name'], $uploadPath)) {
                $video_url = '/uploads/videos/' . $filename;
            } else {
                die('Video upload failed.');
            }
        } elseif (!$id) {
            // If adding and no file uploaded
            die('No video file uploaded.');
        }
    }

    if ($id) {
        // Edit: Update existing row
        $sql = "UPDATE destination_highlights SET destination_id=?, video_title=?, video_description=?, video_type=?";
        $params = [$destination_id, $video_title, $video_description, $video_type];
        $types = "isss";

        // If a new video file uploaded or youtube URL given, update video_url
        if ($video_url) {
            $sql .= ", video_url=?";
            $params[] = $video_url;
            $types .= "s";
        }

        $sql .= " WHERE id=?";
        $params[] = $id;
        $types .= "i";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
    } else {
        // Add: Insert new row
        $stmt = $conn->prepare("INSERT INTO destination_highlights 
            (destination_id, video_title, video_description, video_url, video_type) 
            VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $destination_id, $video_title, $video_description, $video_url, $video_type);
    }

    if ($stmt->execute()) {
        if ($id) {
            header("Location: /hassan/admin/destinations/highlights?status=updated");
        } else {
            header("Location: /hassan/admin/destinations/highlights?status=added");
        }
        exit;
    } else {
        die("Database error: " . $conn->error);
    }
} else {
    die("Invalid request.");
}
