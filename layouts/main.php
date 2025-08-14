<?php
$base = '/hassan';
?>

<!-- layout.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= isset($pageTitle) ? $pageTitle : 'Title'; ?></title>
    <meta name="description" content="<?= isset($metaDescription) ? $metaDescription : 'Description'; ?>">
    <meta name="keywords" content="<?= isset($metaKeywords) ? $metaKeywords : 'Keywords'; ?>">
    <link rel="stylesheet" href="<?= $base; ?>/assets/css/style.css" />
    <link rel="stylesheet" href="<?= $base; ?>/assets/css/chatbot.css" />
    <script defer src="<?= $base; ?>/assets/js/main.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Roboto:wght@400;500&display=swap"
        rel="stylesheet" />
</head>

<body>
    <?= require_once 'includes/components/navbar.php'; ?>
    <main>
        <!-- Chatbot Icon -->
        <div class="chatbot-icon" id="chatbot-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M8 10H8.01M12 10H12.01M16 10H16.01M9 16H5C3.89543 16 3 15.1046 3 14V6C3 4.89543 3.89543 4 5 4H19C20.1046 4 21 4.89543 21 6V14C21 15.1046 20.1046 16 19 16H14L9 21V16Z"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </div>

        <!-- Chatbot Popup -->
        <div class="chatbot-popup" id="chatbot-popup">
            <div class="chatbot-header">
                <div class="chatbot-title">
                    <div class="chatbot-avatar">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M8 10H8.01M12 10H12.01M16 10H16.01M9 16H5C3.89543 16 3 15.1046 3 14V6C3 4.89543 3.89543 4 5 4H19C20.1046 4 21 4.89543 21 6V14C21 15.1046 20.1046 16 19 16H14L9 21V16Z"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <h2>Travel Assistant</h2>
                    <button id="new-chat-btn" class="new-chat-btn" title="Start New Chat">
                        <svg xmlns="http://www.w3.org/2000/svg" height="20" width="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="1 4 1 10 7 10"></polyline>
                            <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
                        </svg>
                    </button>

                </div>

                <button class="chatbot-close" id="chatbot-close">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
            <div class="chat-box">
                <div class="chat-messages" id="chat-messages"></div>
            </div>
        </div>

        <!-- Main Content -->
        <?php require_once $contentFile; ?>
    </main>

    <?php require_once 'includes/components/footer.php'; ?>

    <script>
        // Toggle dropdown
        function toggleDropdown() {
            document.getElementById("userDropdown").classList.toggle("open");
        }

        // Close dropdown when clicking outside
        window.addEventListener("click", function(e) {
            const dropdown = document.getElementById("userDropdown");
            if (!dropdown.contains(e.target)) {
                dropdown.classList.remove("open");
            }
        });

        window.onpageshow = function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        };
    </script>
    <script>
        function toggleMobileMenu() {
            document.getElementById('navLinks').classList.toggle('show');
        }

        function toggleDropdown() {
            const dropdown = document.getElementById("userDropdown");
            dropdown.classList.toggle("open");
        }
    </script>
</body>

</html>