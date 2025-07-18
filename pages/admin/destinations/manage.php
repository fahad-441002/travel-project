<?php
require_once 'config/db.php';

$result = $conn->query('SELECT * FROM destinations ORDER BY id DESC');
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Manage Destinations</h2>
    <a href="<?= $base ?>/admin/destinations/create" class="btn btn-success">+ Create Destination</a>
</div>

<table class="table table-bordered table-hover bg-white shadow-sm">
    <thead class="table-light">
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Banner</th>
            <th>Created At</th>
            <th width="180">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><a href="<?= $base . '/destination/' . $row['slug'] ?>"><?= htmlspecialchars($row['title']) ?></a></td>
                <td><img src="<?= $base . $row['background_image'] ?>" alt="Banner" height="50"></td>
                <td><?= $row['created_at'] ?></td>
                <td>
                    <a href="<?= $base ?>/admin/destinations/edit?id=<?= $row['id'] ?>"
                        class="btn btn-sm btn-warning">Edit</a>

                    <!-- Delete Button triggers Modal -->
                    <button type="button" class="btn btn-sm btn-danger"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteModal"
                        data-id="<?= $row['id'] ?>"
                        data-title="<?= htmlspecialchars($row['title']) ?>">
                        Delete
                    </button>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="<?= $base ?>/admin/destinations/delete">
            <input type="hidden" name="id" id="delete-id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete <strong id="delete-title">this destination</strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap Modal Script -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var title = button.getAttribute('data-title');

            deleteModal.querySelector('#delete-id').value = id;
            deleteModal.querySelector('#delete-title').textContent = title;
        });
    });
</script>