<?php
require_once(__DIR__ . '/config/config.php');

# Header
header("Location: " . $base_url . "/pages/auth/login.php");
exit;
