<?php
require_once 'config/db.php'; // Database connection
require_once 'includes/functions.php'; // For slugify()

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $originalSlug = slugify($title);
    $slug = $originalSlug;
    $counter = 1;

    while (true) {
        $checkStmt = $conn->prepare("SELECT id FROM destinations WHERE slug = ?");
        $checkStmt->bind_param("s", $slug);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows === 0) {
            break;
        }

        $slug = $originalSlug . '-' . $counter++;
        $checkStmt->close();
    }

    $description = $_POST['description'];
    $second_title = $_POST['second_title'];
    $second_description = $_POST['second_description'];
    $features = $_POST['features'];
    $duration = $_POST['duration'];
    $price = $_POST['price'];

    // File upload
    $banner_image = isset($_FILES['banner_image']) ? uploadFile($_FILES['banner_image']) : null;
    $background_image = isset($_FILES['background_image']) ? uploadFile($_FILES['background_image']) : null;


    $stmt = $conn->prepare("INSERT INTO destinations (slug, title, description, second_title, second_description, features, banner_image, background_image, duration, price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('sssssssssd', $slug, $title, $description, $second_title, $second_description, $features, $banner_image, $background_image, $duration, $price);

    if ($stmt->execute()) {
        $msg = "Destination created successfully.";
    } else {
        $msg = "Error: " . $stmt->error;
    }
}
?>

<link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/46.0.0/ckeditor5.css">


<h3>Create New Destination</h3>
<?php if ($msg): ?>
    <div class="alert alert-info"><?= $msg ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <div class="mb-3"><label>Banner Background Image</label><input type="file" name="background_image"
            class="form-control">
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label>Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="3" required></textarea>
        </div>
        <div class="col-md-6 mb-3">
            <label>Second Title</label>
            <input type="text" name="second_title" class="form-control">
        </div>
        <div class="col-md-6 mb-3">
            <label>Second Description</label>
            <textarea name="second_description" class="form-control"></textarea>
        </div>
    </div>
    <div class="mb-3"><label>Features</label><textarea name="features" class="form-control" id="editor"></textarea>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label>Duration (Days)</label>
            <input type="number" name="duration" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
            <label>Price</label>
            <input type="number" step="0.01" name="price" class="form-control" required>
        </div>
    </div>
    <button class="btn btn-primary">Create Destination</button>
    <a href="<?= $base ?>/admin/destinations/manage" class="btn btn-secondary">Back</a>
</form>

<script src="https://cdn.ckeditor.com/ckeditor5/46.0.0/ckeditor5.umd.js"></script>
<script>
    const {
        ClassicEditor,
        Essentials,
        Bold,
        Italic,
        Font,
        Paragraph
    } = CKEDITOR;

    ClassicEditor
        .create(document.querySelector('#editor'), {
            licenseKey: 'eyJhbGciOiJFUzI1NiJ9.eyJleHAiOjE3ODM2NDE1OTksImp0aSI6ImQxZWQwYmI2LWZkYWUtNDI2YS05N2Y4LTFhZWQ4YjZmNDRhNyIsImxpY2Vuc2VkSG9zdHMiOlsiMTI3LjAuMC4xIiwibG9jYWxob3N0IiwiMTkyLjE2OC4qLioiLCIxMC4qLiouKiIsIjE3Mi4qLiouKiIsIioudGVzdCIsIioubG9jYWxob3N0IiwiKi5sb2NhbCJdLCJ1c2FnZUVuZHBvaW50IjoiaHR0cHM6Ly9wcm94eS1ldmVudC5ja2VkaXRvci5jb20iLCJkaXN0cmlidXRpb25DaGFubmVsIjpbImNsb3VkIiwiZHJ1cGFsIl0sImxpY2Vuc2VUeXBlIjoiZGV2ZWxvcG1lbnQiLCJmZWF0dXJlcyI6WyJEUlVQIiwiRTJQIiwiRTJXIl0sInZjIjoiZTM5YmIzYmUifQ.nNJt798dkfUXyKtkkEoEqk3GHc4mYDHQe6S_Tj164ZUnXFxC-dWLja3OTN1apmsHlrO_itdvfWH9JXzCPnClSw',
            plugins: [Essentials, Bold, Italic, Font, Paragraph],
            toolbar: ['undo', 'redo', '|', 'bold', 'italic', '|', 'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor']
        })
        .then(editor => {
            window.editor = editor;
        })
        .catch(error => {
            console.error(error);
        });
</script>