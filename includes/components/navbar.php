<nav class="navbar">
    <div class="logo"><span class="explore">Explore</span><span class="world">World</span></div>

    <button class="menu-toggle" onclick="toggleMobileMenu()">☰</button>

    <div class="nav-links-container" id="navLinks">
        <ul class="nav-links">
            <li><a href="<?= $base ?>/">Home</a></li>
            <li><a href="<?= $base ?>/#destination">Destinations</a></li>

            <?php if (isset($_SESSION['user'])) { ?>
                <li><a href="<?= $base ?>/#book-form">Booking</a></li>
            <?php } ?>

            <li><a href="<?= $base ?>/about">About</a></li>
            <li><a href="<?= $base ?>/contact-us">Contact</a></li>

            <?php if (isset($_SESSION['user'])) { ?>
                <li class="dropdown-li">
                    <div class="custom-dropdown" id="userDropdown">
                        <button class="custom-dropdown-toggle" onclick="toggleDropdown()">👤 <?= $_SESSION['user']['name'] ?></button>
                        <ul class="custom-dropdown-menu">
                            <li>
                                <a href="<?= $_SESSION['user']['role'] === 'admin' ? $base . '/admin/setting/profile' : $base . '/user/setting/profile' ?>">
                                    Settings
                                </a>
                            </li>
                            <li><a href="<?= $base; ?>/logout">Logout</a></li>
                        </ul>
                    </div>
                </li>
            <?php } else { ?>
                <li><a href="<?= $base ?>/login" class="login-btn">Login</a></li>
            <?php } ?>
        </ul>
    </div>
</nav>

<script>
    function toggleMobileMenu() {
        document.getElementById('navLinks').classList.toggle('show');
    }

    function toggleDropdown() {
        const dropdown = document.getElementById("userDropdown");
        dropdown.classList.toggle("open");
    }
</script>