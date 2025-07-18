<?php
require_once 'auth.php';

if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: ../user/dashboard');
    exit;
}