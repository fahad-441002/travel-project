<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    die("Invalid ID.");
}

// Fetch current destination data
$stmt = $conn->prepare("SELECT * FROM destinations WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$destination = $result->fetch_assoc();

if (!$destination) {
    die("Destination not found.");
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $slug = slugify($title);
    $description = $_POST['description'];
    $second_title = $_POST['second_title'];
    $second_description = $_POST['second_description'];
    $features = $_POST['features'];
    $duration = $_POST['duration'];
    $price = $_POST['price'];

    // Optional file uploads
    $banner_image = $destination['banner_image'];
    if (!empty($_FILES['banner_image']['name'])) {
        $banner_image = uploadFile($_FILES['banner_image']);
    }

    $background_image = $destination['background_image'];
    if (!empty($_FILES['background_image']['name'])) {
        $background_image = uploadFile($_FILES['background_image']);
    }

    $stmt = $conn->prepare("UPDATE destinations SET slug=?, title=?, description=?, second_title=?, second_description=?, features=?, banner_image=?, background_image=?, duration=?, price=? WHERE id=?");
    $stmt->bind_param("ssssssssidi", $slug, $title, $description, $second_title, $second_description, $features, $banner_image, $background_image, $duration, $price, $id);

    if ($stmt->execute()) {
        $msg = "Destination updated successfully.";
        // Refresh data
        $destination = array_merge($destination, $_POST, ['banner_image' => $banner_image, 'background_image' => $background_image]);
    } else {
        $msg = "Error: " . $stmt->error;
    }
}
?>
<link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/46.0.0/ckeditor5.css">


<h3>Edit Destination</h3>
<?php if ($msg): ?>
    <div class="alert alert-info"><?= $msg ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label>Banner Background Image (leave blank to keep current)</label>
        <input type="file" name="background_image" class="form-control">
        <?php if ($destination['background_image']): ?>
            <img src="<?= $base . $destination['background_image'] ?>" alt="Current" height="60" class="mt-2">
        <?php endif; ?>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label>Title</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($destination['title']) ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($destination['description']) ?></textarea>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label>Second Title</label>
            <input type="text" name="second_title" class="form-control" value="<?= htmlspecialchars($destination['second_title']) ?>">
        </div>
        <div class="col-md-6 mb-3">
            <label>Second Description</label>
            <textarea name="second_description" class="form-control"><?= htmlspecialchars($destination['second_description']) ?></textarea>
        </div>
    </div>

    <div class="mb-3"><label>Features</label>
        <textarea name="features" class="form-control" id="editor"><?= htmlspecialchars($destination['features']) ?></textarea>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label>Duration (Days)</label>
            <input type="number" name="duration" class="form-control" value="<?= $destination['duration'] ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label>Price</label>
            <input type="number" step="0.01" name="price" class="form-control" value="<?= $destination['price'] ?>" required>
        </div>
    </div>

    <button class="btn btn-primary">Update Destination</button>
    <a href="<?= $base ?>/admin/destinations/manage" class="btn btn-secondary">Back</a>
</form>

<!-- CKEditor -->
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
        .catch(error => {
            console.error(error);
        });
</script>