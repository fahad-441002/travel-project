<?php
require_once 'config/db.php';

$highlights = $conn->query("
    SELECT destination_highlights.*, destinations.title AS destination_title
    FROM destination_highlights
    JOIN destinations ON destination_highlights.destination_id = destinations.id
    ORDER BY destination_highlights.id DESC
");
?>

<style>
    .container {
        padding: 40px 20px;
    }

    h2 {
        text-align: center;
        font-size: 32px;
        margin-bottom: 40px;
    }

    .highlights-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 24px;
    }

    .highlight-card {
        background-color: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease;
    }

    .highlight-card:hover {
        transform: translateY(-5px);
    }

    .video-wrapper {
        position: relative;
        width: 100%;
        padding-bottom: 56.25%;
        background: #000;
    }

    .video-wrapper iframe,
    .video-wrapper video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: none;
    }

    .highlight-text {
        padding: 20px;
    }

    .highlight-title {
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 10px;
        color: #222;
    }

    .highlight-desc {
        font-size: 15px;
        color: #555;
        line-height: 1.5;
    }

    @media (max-width: 600px) {
        .highlight-title {
            font-size: 18px;
        }

        .highlight-desc {
            font-size: 14px;
        }
    }
</style>

<div class="container">
    <h2>🌍 Destination Highlights</h2>
    <div class="highlights-grid">
        <?php while ($row = $highlights->fetch_assoc()): ?>
            <div class="highlight-card">
                <div class="video-wrapper">
                    <?php if ($row['video_type'] === 'youtube'): ?>
                        <iframe src="<?= htmlspecialchars($row['video_url']) ?>" allowfullscreen></iframe>
                    <?php else: ?>
                        <video src="<?= $base . htmlspecialchars($row['video_url']) ?>" controls></video>
                    <?php endif; ?>
                </div>
                <div class="highlight-text">
                    <div class="highlight-title"><?= htmlspecialchars($row['video_title']) ?></div>
                    <div class="highlight-desc"><?= htmlspecialchars($row['video_description']) ?></div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>