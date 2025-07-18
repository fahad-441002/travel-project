<?php
require_once 'config/db.php';

$userId = $_SESSION['user']['id'];
$from = $_GET['from'] ?? date('Y-m-01');
$to   = $_GET['to'] ?? date('Y-m-d');

// Get bookings count for this user
$bookingsRes = $conn->query("SELECT COUNT(*) AS total FROM bookings WHERE user_id=$userId");
$bookings = $bookingsRes->fetch_assoc()['total'];

// Booking status count
$statusQuery = $conn->query("
    SELECT status, COUNT(*) AS total
    FROM bookings
    WHERE user_id=$userId AND created_at BETWEEN '$from 00:00:00' AND '$to 23:59:59'
    GROUP BY status
");
$stats = ['Pending' => 2, 'Confirmed' => 0, 'Cancelled' => 0];
while ($row = $statusQuery->fetch_assoc()) {
    $stats[$row['status']] = $row['total'];
}

// Monthly bookings
$monthlyRes = $conn->query("
    SELECT DATE_FORMAT(created_at,'%Y-%m') AS m, COUNT(*) AS c
    FROM bookings
    WHERE user_id=$userId AND created_at BETWEEN '$from 00:00:00' AND '$to 23:59:59'
    GROUP BY m ORDER BY m
");
$months = [];
$monthCounts = [];
while ($r = $monthlyRes->fetch_assoc()) {
    $months[] = $r['m'];
    $monthCounts[] = $r['c'];
}

// Recent bookings
$recent = $conn->query("
    SELECT * FROM bookings
    WHERE user_id=$userId
    ORDER BY created_at DESC
    LIMIT 5
");
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<h2>Welcome, <?= htmlspecialchars($_SESSION['user']['name']) ?></h2>

<form class="row justify-content-end g-3 mb-4" method="GET">
    <div class="col-auto">
        <input type="date" name="from" class="form-control" value="<?= $from ?>">
    </div>
    <div class="col-auto">
        <input type="date" name="to" class="form-control" value="<?= $to ?>">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary">Apply Filter</button>
    </div>
</form>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h5>Total Bookings</h5>
                <h3><?= $bookings ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Booking Status</div>
            <div class="card-body">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Monthly Bookings</div>
            <div class="card-body">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">Recent Bookings</div>
    <div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>Destination</th>
                    <th>Persons</th>
                    <th>Status</th>
                    <th>Travel Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $recent->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['destination_title']) ?></td>
                        <td><?= $row['persons'] ?></td>
                        <td><?= $row['status'] ?></td>
                        <td><?= $row['travel_date'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</div>

<script>
    const pie = new Chart(document.getElementById('statusChart'), {
        type: 'pie',
        data: {
            labels: ['Pending', 'Confirmed', 'Cancelled'],
            datasets: [{
                data: [<?= $stats['Pending'] ?>, <?= $stats['Confirmed'] ?>, <?= $stats['Cancelled'] ?>],
                backgroundColor: ['#ffc107', '#28a745', '#dc3545']
            }]
        }
    });

    const bar = new Chart(document.getElementById('monthlyChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($months) ?>,
            datasets: [{
                label: 'Bookings',
                data: <?= json_encode($monthCounts) ?>,
                backgroundColor: '#0d6efd'
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>