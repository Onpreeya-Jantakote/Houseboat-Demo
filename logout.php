<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

// เคลียร์ข้อมูล Session ทั้งหมด
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// เริ่ม Session ใหม่เพื่อส่ง Flash Message
session_start();
setFlashMessage('success', 'ออกจากระบบเรียบร้อยแล้ว');
header("Location: " . BASE_URL . "/index.php");
exit();
