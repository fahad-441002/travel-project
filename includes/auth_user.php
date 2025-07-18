<?php
require_once 'auth.php';

if ($_SESSION['user']['role'] !== 'user') {
    header('Location: ../admin/dashboard');
    exit;
}