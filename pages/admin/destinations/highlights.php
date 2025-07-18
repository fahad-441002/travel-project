<?php
require_once 'config/db.php';

$highlights = $conn->query("SELECT destination_highlights.*, destinations.title AS destination_title 
    FROM destination_highlights 
    JOIN destinations ON destination_highlights.destination_id = destinations.id 
    ORDER BY destination_highlights.id DESC");
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Manage Highlights</h2>
    <a href="highlightForm" class="btn btn-primary">➕ Add Highlight</a>
</div>

<table class="table table-bordered table-hover bg-white shadow-sm">
    <thead class="table-light">
        <tr>
            <th>ID</th>
            <th>Destination</th>
            <th>Title</th>
            <th>Type</th>
            <th>Preview</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($highlights->num_rows > 0): ?>
            <?php while ($row = $highlights->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['destination_title']) ?></td>
                    <td><?= htmlspecialchars($row['video_title']) ?></td>
                    <td><?= $row['video_type'] ?></td>
                    <td>
                        <?php if ($row['video_type'] === 'youtube'): ?>
                            <iframe width="160" height="90" src="<?= $row['video_url'] ?>" frameborder="0" allowfullscreen></iframe>
                        <?php else: ?>
                            <video width="160" height="90" controls>
                                <source src="<?= $row['video_url'] ?>" type="video/mp4">
                            </video>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= $base . "/admin/destinations/highlightForm?id=" . $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $row['id'] ?>">
                            Delete
                        </button>
                    </td>
                </tr>

                <!-- Delete Confirmation Modal -->
                <div class="modal fade" id="deleteModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel">Delete Highlight</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to delete this highlight?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <a href="<?= $base ?>/api/admin/highlights/delete.php?id=<?= $row['id'] ?>" class="btn btn-danger">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="text-center">No highlights found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>