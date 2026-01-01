<?php
require_once(__DIR__ . '/config/config.php');
// require_once __DIR__ . "/core/license.php";


header("Location: " . $base_url . "/pages/auth/login.php");
exit;
