<?php
/**
 * Redirect home.php to appropriate dashboard
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

if (isAdminLoggedIn()) {
    header("Location: " . BASE_URL . "/admin/index.php");
    exit();
} elseif (isCustomerLoggedIn()) {
    header("Location: " . BASE_URL . "/customer/index.php");
    exit();
} else {
    header("Location: " . BASE_URL . "/index.php");
    exit();
}
