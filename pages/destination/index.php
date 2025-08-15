<div class="banner" style="background-image: url('<?= $base . $destination['background_image'] ?>')">
    <div class="banner-text">
        <h1><?= htmlspecialchars($destination['title']) ?></h1>
        <p><?= htmlspecialchars($destination['description']) ?></p>
    </div>
</div>

<div class="tour-details">
    <h2><?= htmlspecialchars($destination['second_title']) ?></h2>
    <p><?= nl2br(htmlspecialchars($destination['second_description'])) ?></p>

    <ul>
        <li><?= $destination['features'] ?></li>
        <li>💰 <strong>Price: Rs <?= number_format($destination['price'], 2) ?> per person</strong></li>
    </ul>

    <a href="<?= $base ?>?book=<?= urlencode($destination['slug']) ?>" class="book-button">Book Now</a>
</div>