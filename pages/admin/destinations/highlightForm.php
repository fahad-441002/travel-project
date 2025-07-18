<?php
require_once 'config/db.php';

$id = $_GET['id'] ?? null;
$data = null;

// Get destinations
$destinations = $conn->query("SELECT id, title FROM destinations");

// Load existing highlight if editing
if ($id) {
    $stmt = $conn->prepare("SELECT * FROM destination_highlights WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><?= $id ? 'Edit' : 'Add' ?> Highlight</h2>
    <a href="highlights" class="btn btn-secondary">← Back to List</a>
</div>

<form action="<?= $base . '/api/admin/highlights/save.php' ?>" method="POST" enctype="multipart/form-data">
    <?php if ($id): ?>
        <input type="hidden" name="id" value="<?= $id ?>">
    <?php endif; ?>

    <div class="row mb-3">
        <div class="col">
            <label>Destination</label>
            <select name="destination_id" class="form-select" required>
                <option value="">Select Destination</option>
                <?php while ($d = $destinations->fetch_assoc()): ?>
                    <option value="<?= $d['id'] ?>" <?= ($data && $data['destination_id'] == $d['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($d['title']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="col">
            <label>Video Type</label>
            <select name="video_type" id="video_type" class="form-select" required onchange="toggleVideoInput()">
                <option value="youtube" <?= ($data && $data['video_type'] === 'youtube') ? 'selected' : '' ?>>YouTube</option>
                <option value="mp4" <?= ($data && $data['video_type'] === 'mp4') ? 'selected' : '' ?>>MP4</option>
            </select>
        </div>
    </div>

    <div class="mb-3">
        <label>Video Title</label>
        <input type="text" name="video_title" class="form-control" value="<?= $data['video_title'] ?? '' ?>" required>
    </div>

    <div class="mb-3">
        <label>Video Description</label>
        <textarea name="video_description" class="form-control"><?= $data['video_description'] ?? '' ?></textarea>
    </div>

    <div class="mb-3" id="youtube_input" style="display: <?= (!$data || $data['video_type'] === 'youtube') ? 'block' : 'none' ?>">
        <label>YouTube URL</label>
        <input type="url" name="video_url" class="form-control" value="<?= $data['video_url'] ?? '' ?>">
    </div>

    <div class="mb-3" id="mp4_input" style="display: <?= ($data && $data['video_type'] === 'mp4') ? 'block' : 'none' ?>">
        <label>Upload MP4</label>
        <input type="file" name="video_file" class="form-control">
        <?php if ($data && $data['video_type'] === 'mp4'): ?>
            <small>Current file: <?= basename($data['video_url']) ?></small>
        <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-primary"><?= $id ? 'Update' : 'Add' ?> Highlight</button>
</form>

<script>
    function toggleVideoInput() {
        const type = document.getElementById('video_type').value;
        document.getElementById('youtube_input').style.display = type === 'youtube' ? 'block' : 'none';
        document.getElementById('mp4_input').style.display = type === 'mp4' ? 'block' : 'none';
    }
</script>