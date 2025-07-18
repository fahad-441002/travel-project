<?php
require_once 'config/db.php';

$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-d');

function countTable($conn, $table)
{
    return $conn->query("SELECT COUNT(*) as total FROM `$table`")
        ->fetch_assoc()['total'];
}

$users      = countTable($conn, 'users');
$bookings   = countTable($conn, 'bookings');
$destCount  = countTable($conn, 'destinations');
$hlCount    = countTable($conn, 'destination_highlights');
$msgCount   = countTable($conn, 'contact_messages');

// Booking status counts
$statusRes = $conn->query("
  SELECT status, COUNT(*) total 
  FROM bookings
  WHERE created_at BETWEEN '$from 00:00:00' AND '$to 23:59:59'
  GROUP BY status
");
$stats = ['Pending' => 0, 'Confirmed' => 0, 'Cancelled' => 0];
while ($r = $statusRes->fetch_assoc()) {
    $stats[$r['status']] = (int)$r['total'];
}

// Monthly bookings
$mon = $conn->query("
  SELECT DATE_FORMAT(created_at,'%Y-%m') m, COUNT(*) c
  FROM bookings 
  WHERE created_at BETWEEN '$from 00:00:00' AND '$to 23:59:59'
  GROUP BY m ORDER BY m
");
$months = [];
$monthCount = [];
while ($r = $mon->fetch_assoc()) {
    $months[] = $r['m'];
    $monthCount[] = (int)$r['c'];
}

// Recent bookings
$recent = $conn->query("
  SELECT b.*, u.name 
  FROM bookings b JOIN users u ON b.user_id=u.id
  ORDER BY b.id DESC LIMIT 5
");
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<h2>Welcome, <?= htmlspecialchars($_SESSION['user']['name'] ?? 'Admin') ?></h2>

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
    <div class="col-lg">
        <div class="card text-center">
            <div class="card-body">
                <h5>Users</h5>
                <h3><?= $users ?></h3>
            </div>
        </div>
    </div>
    <div class="col-lg">
        <div class="card text-center">
            <div class="card-body">
                <h5>Bookings</h5>
                <h3><?= $bookings ?></h3>
            </div>
        </div>
    </div>
    <div class="col-lg">
        <div class="card text-center">
            <div class="card-body">
                <h5>Destinations</h5>
                <h3><?= $destCount ?></h3>
            </div>
        </div>
    </div>
    <div class="col-lg">
        <div class="card text-center">
            <div class="card-body">
                <h5>Highlights</h5>
                <h3><?= $hlCount ?></h3>
            </div>
        </div>
    </div>
    <div class="col-lg">
        <div class="card text-center">
            <div class="card-body">
                <h5>Messages</h5>
                <h3><?= $msgCount ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="row mb-5">
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header">Booking Status</div>
            <div class="card-body">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
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
                    <th>User</th>
                    <th>Destination</th>
                    <th>Persons</th>
                    <th>Status</th>
                    <th>Travel Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($r = $recent->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['name']) ?></td>
                        <td><?= htmlspecialchars($r['destination_title']) ?></td>
                        <td><?= $r['persons'] ?></td>
                        <td><?= $r['status'] ?></td>
                        <td><?= $r['travel_date'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
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
                data: <?= json_encode($monthCount) ?>,
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