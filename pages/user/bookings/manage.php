<?php
require_once 'config/db.php';

$userId = $_SESSION['user']['id'];
$userRole = $_SESSION['user']['role'];

// Check role
if ($userRole !== 'user') {
    die("Unauthorized access.");
}

$stmt = $conn->prepare("SELECT bookings.*, users.name AS user_name, destinations.title AS destination_title 
                        FROM bookings 
                        JOIN users ON bookings.user_id = users.id 
                        JOIN destinations ON bookings.destination_slug = destinations.slug 
                        WHERE bookings.user_id = ? 
                        ORDER BY bookings.id DESC");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>My Bookings</h2>
</div>

<?php if ($result->num_rows > 0): ?>
    <table class="table table-bordered table-hover bg-white shadow-sm">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Destination</th>
                <th>Persons</th>
                <th>Total Price</th>
                <th>Travel Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['destination_title']) ?></td>
                    <td><?= $row['persons'] ?></td>
                    <td>$<?= number_format($row['total_price'], 2) ?></td>
                    <td><?= $row['travel_date'] ?></td>
                    <td>
                        <span class="badge bg-<?= $row['status'] === 'Confirmed' ? 'success' : ($row['status'] === 'Cancelled' ? 'danger' : 'warning') ?>">
                            <?= $row['status'] ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-info"
                            data-bs-toggle="modal"
                            data-bs-target="#viewModal"
                            data-id="<?= $row['id'] ?>"
                            data-destination="<?= htmlspecialchars($row['destination_title']) ?>"
                            data-persons="<?= $row['persons'] ?>"
                            data-price="<?= $row['total_price'] ?>"
                            data-date="<?= $row['travel_date'] ?>"
                            data-status="<?= $row['status'] ?>"
                            data-reason="<?= $row['reason'] ?>"
                            data-message="<?= $row['message'] ?>">
                            View
                        </button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="alert alert-info">No bookings found for your account.</div>
<?php endif; ?>


<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-white">
            <div class="modal-header">
                <h5 class="modal-title">Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Destination:</strong> <span id="v-destination"></span></p>
                <p><strong>Persons:</strong> <span id="v-persons"></span></p>
                <p><strong>Total Price:</strong> $<span id="v-price"></span></p>
                <p><strong>Travel Date:</strong> <span id="v-date"></span></p>
                <p><strong>Status:</strong> <span id="v-status"></span></p>
                <p><strong>Message:</strong> <span id="v-message"></span></p>
                <p id="v-reason-box" style="display:none;">
                    <strong>Cancel Reason:</strong> <span id="v-reason"></span>
                </p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- JS -->
<script>
    const viewModal = document.getElementById('viewModal');

    viewModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;

        document.getElementById('v-destination').textContent = button.dataset.destination;
        document.getElementById('v-persons').textContent = button.dataset.persons;
        document.getElementById('v-price').textContent = parseFloat(button.dataset.price).toFixed(2);
        document.getElementById('v-date').textContent = button.dataset.date;
        document.getElementById('v-status').textContent = button.dataset.status;
        document.getElementById('v-message').textContent = button.dataset.message;

        if (button.dataset.status === 'Cancelled') {
            document.getElementById('v-reason-box').style.display = 'block';
            document.getElementById('v-reason').textContent = button.dataset.reason || 'N/A';
        } else {
            document.getElementById('v-reason-box').style.display = 'none';
            document.getElementById('v-reason').textContent = '';
        }
    });
</script>