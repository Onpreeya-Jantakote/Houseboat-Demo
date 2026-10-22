<?php
/**
 * Application Configuration
 */

// เริ่มต้น Session หากยังไม่ได้เริ่ม
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ตั้งค่า Timezone ของประเทศไทย
date_default_timezone_set('Asia/Bangkok');

// โหลดการเชื่อมต่อฐานข้อมูล
require_once __DIR__ . '/database.php';

// กำหนด BASE_URL อัตโนมัติ (รองรับทั้ง XAMPP Subdirectory และ PHP Built-in Server)
if (!defined('BASE_URL')) {
    $envUrl = getenv('APP_URL');
    if ($envUrl) {
        define('BASE_URL', rtrim($envUrl, '/'));
    } else {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
        
        // ตรวจสอบ subfolder จากตำแหน่งของไฟล์โปรเจกต์
        $docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'])) : '';
        $appDir = str_replace('\\', '/', realpath(__DIR__ . '/..'));
        
        $subFolder = '';
        if (!empty($docRoot) && strpos($appDir, $docRoot) === 0) {
            $subFolder = substr($appDir, strlen($docRoot));
        }
        
        define('BASE_URL', rtrim($protocol . $host . $subFolder, '/'));
    }
}

// กำหนดค่าคงที่ของระบบ
if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'House boat');
}
if (!defined('DEPOSIT_AMOUNT')) {
    define('DEPOSIT_AMOUNT', 500); // ค่ามัดจำเริ่มต้น 500 บาท
}
if (!defined('EXTRA_BED_PRICE')) {
    define('EXTRA_BED_PRICE', 500); // เตียงเสริม +500 บาท
}
