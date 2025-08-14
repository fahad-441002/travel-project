<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
require_once 'helpers/mail_helper.php';


$pageTitle = "Welcome to Travel & Tour";
$metaDescription = "Best travel packages and tour management.";
$metaKeywords = "travel, tour, booking, vacation";

$bookSlug = $_GET['book'] ?? null;
// If not logged in, redirect to login with return
if ($bookSlug && !isset($_SESSION['user'])) {
    header('Location: login?redirectTo=' . urlencode('book=' . $bookSlug));
    exit;
}

// load the destination data
$destinations = [];
$stmt = $conn->prepare("SELECT * FROM destinations");
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $destinations[] = $row;
    }
}


// Save booking
$bookingSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user'])) {
    $userId = $_SESSION['user']['id'];
    $phone = trim($_POST['phone']);
    $slug = $_POST['destination'];
    $travelDate = $_POST['traveldate'];
    $message = trim($_POST['message'] ?? '');
    $persons = max(1, intval($_POST['persons'] ?? 1));

    // Validate destination
    $stmt = $conn->prepare("SELECT * FROM destinations WHERE slug = ?");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $dest = $result->fetch_assoc();
        $price = $dest['price'];
        $duration = $dest['duration'];
        $total = $price * $persons;
        $title = $dest['title'];

        // Insert booking
        $insert = $conn->prepare("INSERT INTO bookings (user_id, destination_slug, destination_title, phone, travel_date, persons, amount, total_price, message) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insert->bind_param("issssiids", $userId, $slug, $title, $phone, $travelDate, $persons, $price, $total, $message);
        if ($insert->execute()) {
            $bookingSuccess = true;

            // Prepare data
            $user = $_SESSION['user'];
            $bookingData = [
                'phone' => $phone,
                'travel_date' => $travelDate,
                'persons' => $persons,
                'total_price' => $total,
                'message' => $message
            ];
            $userEmail = $user['email'];
            $userName = $user['name'];
            $status = 'Pending';

            // User email template
            $userBody = '
    <div style="font-family: Arial, sans-serif; padding: 20px; max-width: 600px; margin: auto; border: 1px solid #ddd; border-radius: 10px;">
        <h2 style="color: #f1c40f;">Booking Pending</h2>
        <p>Dear ' . htmlspecialchars($userName) . ',</p>
        <p>Your booking has been <strong>' . $status . '</strong> for the following destination:</p>
        <table cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse;">
            <tr>
                <td><strong>Destination:</strong></td>
                <td>' . htmlspecialchars($dest['title']) . '</td>
            </tr>
            <tr>
                <td><strong>Travel Date:</strong></td>
                <td>' . htmlspecialchars($bookingData['travel_date']) . '</td>
            </tr>
            <tr>
                <td><strong>Persons:</strong></td>
                <td>' . $bookingData['persons'] . '</td>
            </tr>
            <tr>
                <td><strong>Total Price:</strong></td>
                <td>$' . number_format($bookingData['total_price'], 2) . '</td>
            </tr>
            <tr>
                <td><strong>Status:</strong></td>
                <td>' . $status . '</td>
            </tr>
        </table>
        <p style="margin-top: 20px;">We will contact you soon to confirm your booking.</p>
        <p>Thank you,<br><strong>Travel Team</strong></p>
    </div>
';

            sendMail($user['email'], "Booking Confirmation - {$dest['title']}", $userBody);

            // Admin email template
            $adminBody = '
    <div style="font-family: Arial, sans-serif; padding: 20px; max-width: 600px; margin: auto; border: 1px solid #ddd; border-radius: 10px;">
        <h2 style="color: #3498db;">New Booking Received</h2>
        <p><strong>User:</strong> ' . htmlspecialchars($userName) . '</p>
        <table cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse;">
            <tr>
                <td><strong>Email:</strong></td>
                <td>' . htmlspecialchars($userEmail) . '</td>
            </tr>
            <tr>
                <td><strong>Phone:</strong></td>
                <td>' . htmlspecialchars($bookingData['phone']) . '</td>
            </tr>
            <tr>
                <td><strong>Destination:</strong></td>
                <td>' . htmlspecialchars($dest['title']) . '</td>
            </tr>
            <tr>
                <td><strong>Date:</strong></td>
                <td>' . htmlspecialchars($bookingData['travel_date']) . '</td>
            </tr>
            <tr>
                <td><strong>Persons:</strong></td>
                <td>' . $bookingData['persons'] . '</td>
            </tr>
            <tr>
                <td><strong>Total:</strong></td>
                <td>$' . number_format($bookingData['total_price'], 2) . '</td>
            </tr>
            <tr>
                <td><strong>Message:</strong></td>
                <td>' . nl2br(htmlspecialchars($bookingData['message'])) . '</td>
            </tr>
        </table>
        <p>Login to your dashboard to review and take action.</p>
    </div>
';

            sendMail('hassanaltaf468348@gmail.com', "🧾 New Booking from {$userName}", $adminBody);
        } else {
            echo "<p style='color:red'>Error saving booking: {$insert->error}</p>";
        }
    }
}

?>

<div class="hero">
    <div class="hero-content">
        <h1>Discover New Horizons</h1>
        <p>Your adventure starts here. Explore the world with us.</p>
        <a href="<?= $base ?>/highlights" class="btn">Start Your Journey</a>
    </div>
</div>

<section class="dest_section" id="destination">
    <h2 class="dest-content">Popular Destinations</h2>
    <div class="dest-grid">
        <?php foreach ($destinations as $destination): ?>
            <div class="dest-item">
                <img src="<?= $base . htmlspecialchars($destination['background_image']) ?>" alt="<?= htmlspecialchars($destination['title']) ?>">
                <h3><?= htmlspecialchars($destination['title']) ?></h3>
                <p class="price"><strong>Price:</strong> $<?= number_format($destination['price'], 2) ?></p>
                <a href="<?= $base ?>/destination/<?= urlencode($destination['slug']) ?>" class="btn">View details</a>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Booking Form -->
<?php if (isset($_SESSION['user'])) { ?>
    <section id="book-form">
        <div class="booking-form">
            <h2>Book Your Tour</h2>
            <?php if ($bookingSuccess): ?>
                <div class="success-msg">🎉 Your booking has been successfully submitted!</div>
            <?php endif; ?>
            <form action="#" method="post">
                <input type="hidden" name="user_id" value="<?php echo $_SESSION['user']['id']; ?>">
                <input type="text" value="<?php echo $_SESSION['user']['name']; ?>" name="fullname" placeholder="Full Name" disabled required>
                <input type="email" name="email" value="<?php echo $_SESSION['user']['email']; ?>" placeholder="Email Address" disabled required>
                <input type="tel" name="phone" placeholder="Phone Number" required>
                <select name="destination" required>
                    <option value="">Select Destination</option>
                    <?php foreach ($destinations as $destination): ?>
                        <option value="<?= $destination['slug'] ?>" <?= $bookSlug === $destination['slug'] ? 'selected' : '' ?>><?= $destination['title'] ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="number" name="persons" placeholder="Number of Persons" min="1" value="1" required>
                <input type="date" name="traveldate" value="<?php echo date('Y-m-d'); ?>" required>
                <textarea name="message" placeholder="Any special request?" rows="4"></textarea>
                <div class="info-box" style="margin: 15px 0;"></div>
                <button type="submit">Submit Booking</button>
            </form>
        </div>

    </section>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const bookingForm = document.querySelector("#book-form form");
            const submitBtn = bookingForm.querySelector("button[type='submit']");

            bookingForm.addEventListener("submit", function() {
                submitBtn.disabled = true;
                submitBtn.textContent = "Submitting...";

                // Add loading class for spinner or styles
                submitBtn.classList.add("loading");
            });

            // If page reloads (on success/error), it will reset the button automatically
        });

        document.addEventListener("DOMContentLoaded", function() {
            const params = new URLSearchParams(window.location.search);
            const book = params.get("book");

            const form = document.querySelector("#book-form");

            const wasSubmitted = <?= json_encode($_SERVER['REQUEST_METHOD'] === 'POST') ?>;
            const bookingSuccess = <?= json_encode($bookingSuccess) ?>;

            if (book || (wasSubmitted && bookingSuccess)) {
                if (form) {
                    setTimeout(() => {
                        form.scrollIntoView({
                            behavior: "smooth"
                        });
                    }, 300); // Delay for smooth scroll
                }
            }
        });
        document.addEventListener("DOMContentLoaded", function() {
            const destinationSelect = document.querySelector('select[name="destination"]');
            const personsInput = document.querySelector('input[name="persons"]');
            const infoBox = document.querySelector('.info-box');

            const destinationData = <?= json_encode(array_column($destinations, null, 'slug')) ?>;

            function updateInfo() {
                const slug = destinationSelect.value;
                const persons = parseInt(personsInput.value || 1);
                const data = destinationData[slug];
                if (data) {
                    const price = parseFloat(data.price);
                    const duration = data.duration;
                    const total = (price * persons).toFixed(2);

                    infoBox.innerHTML = `
    <p><strong>Duration:</strong> ${duration} Days Tour</p>
    <p><strong>Price per person:</strong> $${price.toFixed(2)}</p>
    <p><strong>Total for ${persons}:</strong> $${total}</p>
    `;
                } else {
                    infoBox.innerHTML = '';
                }
            }

            destinationSelect.addEventListener("change", updateInfo);
            personsInput.addEventListener("input", updateInfo);

            updateInfo(); // run on load if pre-selected
        });
    </script>

<?php } ?>