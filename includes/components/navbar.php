<nav class="navbar">
    <div class="logo"><span class="explore">Explore</span><span class="world">World</span></div>
    <ul class="nav-links">
        <li><a href="<?= $base ?>/">Home</a></li>
        <li><a href="<?= $base ?>/#destination">Destinations</a></li>
        <?php if (isset($_SESSION['user'])) { ?>
            <li><a href="<?= $base ?>/#book-form">Booking</a></li>
        <?php } ?>
        <li><a href="<?= $base ?>/about">About</a></li>
        <li><a href="<?= $base ?>/contact-us">Contact</a></li>
        <li><a href="<?= $base ?>/login" class="login-btn">Login</a></li>
    </ul>
</nav>