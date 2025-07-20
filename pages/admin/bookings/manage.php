<?php
require_once 'config/db.php';

// Updated query to fetch user or guest name, email, and phone
$result = $conn->query("SELECT bookings.*, 
                               COALESCE(users.name, guest_users.name) AS user_name,
                               COALESCE(users.email, guest_users.email) AS user_email,
                               COALESCE(guest_users.phone, '') AS user_phone,
                               bookings.destination_title,
                               destinations.title AS real_destination_title 
                        FROM bookings 
                        LEFT JOIN users ON bookings.user_id = users.id 
                        LEFT JOIN guest_users ON bookings.guest_id = guest_users.id
                        LEFT JOIN destinations ON bookings.destination_slug = destinations.slug 
                        ORDER BY bookings.id DESC");

// Fetch all destinations
$allDestinations = $conn->query("SELECT slug, title, price FROM destinations");
$destMap = [];
while ($d = $allDestinations->fetch_assoc()) {
    $destMap[$d['slug']] = $d;
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Manage Bookings</h2>
</div>
<?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success">Booking updated successfully!</div>
<?php endif; ?>

<table class="table table-bordered table-hover bg-white shadow-sm">
    <thead class="table-light">
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Destination</th>
            <th>Persons</th>
            <th>Total Price</th>
            <th>Travel Date</th>
            <th>Status</th>
            <th>Source</th>
            <th>Channel</th>
            <th width="160">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['user_name']) ?></td>
                <td><?= htmlspecialchars($row['destination_title'] ?: $row['real_destination_title']) ?></td>
                <td><?= $row['persons'] ?></td>
                <td>$<?= number_format($row['total_price'], 2) ?></td>
                <td><?= $row['travel_date'] ?></td>
                <td>
                    <span class="badge bg-<?= $row['status'] === 'Confirmed' ? 'success' : ($row['status'] === 'Cancelled' ? 'danger' : 'warning') ?>">
                        <?= $row['status'] ?>
                    </span>
                </td>
                <td><?= htmlspecialchars($row['source']) ?></td>
                <td><?= htmlspecialchars($row['channel']) ?></td>
                <td>
                    <button class="btn btn-sm btn-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#viewModal"
                        data-id="<?= $row['id'] ?>"
                        data-user="<?= htmlspecialchars($row['user_name']) ?>"
                        data-email="<?= htmlspecialchars($row['user_email']) ?>"
                        data-phone="<?= htmlspecialchars($row['user_phone']) ?>"
                        data-destination="<?= htmlspecialchars($row['destination_title'] ?: $row['real_destination_title']) ?>"
                        data-persons="<?= $row['persons'] ?>"
                        data-price="<?= $row['total_price'] ?>"
                        data-date="<?= $row['travel_date'] ?>"
                        data-status="<?= $row['status'] ?>"
                        data-message="<?= htmlspecialchars($row['agent_message']) ?>"
                        data-reason="<?= htmlspecialchars($row['reason']) ?>">
                        View/Edit
                    </button>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="<?= $base ?>/admin/bookings/update">
            <input type="hidden" name="id" id="booking-id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Booking Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>User:</strong> <span id="booking-user"></span></p>
                    <p><strong>Email:</strong> <span id="booking-email"></span></p>
                    <p><strong>Phone:</strong> <span id="booking-phone"></span></p>

                    <div class="mb-2">
                        <label>Destination</label>
                        <select name="destination_slug" id="booking-destination" class="form-select" required>
                            <?php foreach ($destMap as $slug => $d): ?>
                                <option value="<?= $slug ?>"><?= htmlspecialchars($d['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <p><strong>Message:</strong> <span id="booking-message"></span></p>

                    <div class="mb-2">
                        <label>Travel Date</label>
                        <input type="date" name="travel_date" id="booking-date" class="form-control" required>
                    </div>

                    <div class="mb-2">
                        <label>Persons</label>
                        <input type="number" name="persons" id="booking-persons" class="form-control" min="1" required>
                    </div>

                    <div class="mb-2">
                        <label>Status</label>
                        <select name="status" id="booking-status" class="form-select">
                            <option>Pending</option>
                            <option>Confirmed</option>
                            <option>Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-2" id="cancel-reason-container" style="display: none;">
                        <label>Cancellation Reason</label>
                        <textarea name="reason" id="booking-reason" class="form-control" rows="2" placeholder="Enter cancellation reason"></textarea>
                    </div>

                    <div>
                        <strong>Total Price:</strong> $<span id="booking-price">0.00</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Update Booking</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- JS -->
<script>
    const destinationsData = <?= json_encode($destMap) ?>;
    const viewModal = document.getElementById('viewModal');
    const destSelect = document.getElementById('booking-destination');
    const personsInput = document.getElementById('booking-persons');
    const priceDisplay = document.getElementById('booking-price');
    const reasonContainer = document.getElementById('cancel-reason-container');
    const reasonInput = document.getElementById('booking-reason');
    const statusSelect = document.getElementById('booking-status');

    function updateTotal() {
        const slug = destSelect.value;
        const persons = parseInt(personsInput.value) || 0;
        const price = parseFloat(destinationsData[slug]?.price || 0);
        const total = price * persons;
        priceDisplay.textContent = total.toFixed(2);
    }

    function toggleReasonBox() {
        if (statusSelect.value === 'Cancelled') {
            reasonContainer.style.display = 'block';
        } else {
            reasonContainer.style.display = 'none';
            reasonInput.value = '';
        }
    }

    viewModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const slug = Object.keys(destinationsData).find(key =>
            destinationsData[key].title === button.dataset.destination);

        const status = button.dataset.status;
        const isReadOnly = (status === 'Cancelled');

        document.getElementById('booking-id').value = button.dataset.id;
        document.getElementById('booking-user').textContent = button.dataset.user;
        document.getElementById('booking-email').textContent = button.dataset.email || 'N/A';
        document.getElementById('booking-phone').textContent = button.dataset.phone || 'N/A';
        document.getElementById('booking-message').textContent = button.dataset.message;
        document.getElementById('booking-date').value = button.dataset.date;
        document.getElementById('booking-persons').value = button.dataset.persons;
        reasonInput.value = (status === 'Cancelled') ? (button.dataset.reason || '') : '';
        destSelect.value = slug;
        updateTotal();

        statusSelect.innerHTML = '';
        if (status === 'Confirmed') {
            statusSelect.innerHTML += '<option selected>Confirmed</option>';
            statusSelect.innerHTML += '<option>Cancelled</option>';
        } else if (status === 'Pending') {
            statusSelect.innerHTML += '<option>Pending</option>';
            statusSelect.innerHTML += '<option>Confirmed</option>';
            statusSelect.innerHTML += '<option>Cancelled</option>';
            statusSelect.value = 'Pending';
        } else if (status === 'Cancelled') {
            statusSelect.innerHTML += '<option selected>Cancelled</option>';
        }

        reasonContainer.style.display = (status === 'Cancelled') ? 'block' : 'none';

        destSelect.disabled = isReadOnly;
        document.getElementById('booking-date').readOnly = isReadOnly;
        personsInput.readOnly = isReadOnly;
        statusSelect.disabled = isReadOnly;
        reasonInput.readOnly = isReadOnly;

        const submitBtn = viewModal.querySelector('button[type="submit"]');
        submitBtn.style.display = isReadOnly ? 'none' : 'inline-block';
    });

    // Disable submit button and show "Saving..." on form submit
    viewModal.querySelector('form').addEventListener('submit', function(e) {
        const submitBtn = viewModal.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Saving...';
    });

    statusSelect.addEventListener('change', toggleReasonBox);
    destSelect.addEventListener('change', updateTotal);
    personsInput.addEventListener('input', updateTotal);
</script>