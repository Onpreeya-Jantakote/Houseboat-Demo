<?php
/**
 * Common Helper Functions
 * ฟังก์ชันกลางสำหรับใช้งานทั่วทั้งระบบ
 */

/**
 * ป้องกัน XSS โดยแปลงอักขระพิเศษเป็น HTML Entities
 */
function e($string) {
    return htmlspecialchars((string)($string ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * แปลงวันที่ ค.ศ. (YYYY-MM-DD) เป็นวันที่ภาษาไทย (พ.ศ.)
 * ตัวอย่าง: 2024-02-24 -> 24 กุมภาพันธ์ 2567
 */
function thaiDate($date) {
    if (empty($date) || $date === '0000-00-00') {
        return '-';
    }
    
    $thaiMonths = [
        "01" => "มกราคม",
        "02" => "กุมภาพันธ์",
        "03" => "มีนาคม",
        "04" => "เมษายน",
        "05" => "พฤษภาคม",
        "06" => "มิถุนายน",
        "07" => "กรกฎาคม",
        "08" => "สิงหาคม",
        "09" => "กันยายน",
        "10" => "ตุลาคม",
        "11" => "พฤศจิกายน",
        "12" => "ธันวาคม"
    ];

    $parts = explode("-", substr($date, 0, 10));
    if (count($parts) !== 3) {
        return $date;
    }
    
    $year = (int)$parts[0] + 543;
    $month = $thaiMonths[$parts[1]] ?? $parts[1];
    $day = (int)$parts[2];
    
    return "{$day} {$month} {$year}";
}

/**
 * จัดรูปแบบราคาตัวเลขพร้อมคอมม่าและทศนิยม
 */
function formatPrice($amount) {
    return number_format((float)$amount, 2);
}

/**
 * ตรวจสอบว่าลูกค้าเข้าสู่ระบบอยู่หรือไม่
 */
function isCustomerLoggedIn() {
    return isset($_SESSION['cus_user']) && !empty($_SESSION['cus_user']);
}

/**
 * บังคับให้ลูกค้าต้องล็อกอินก่อนเข้าหน้านั้น
 */
function requireCustomerLogin() {
    if (!isCustomerLoggedIn()) {
        header("Location: " . BASE_URL . "/login.php");
        exit();
    }
}

/**
 * ตรวจสอบว่าแอดมินเข้าสู่ระบบอยู่หรือไม่
 */
function isAdminLoggedIn() {
    return isset($_SESSION['ad_user']) && !empty($_SESSION['ad_user']);
}

/**
 * บังคับให้แอดมินต้องล็อกอินก่อนเข้าหน้านั้น
 */
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header("Location: " . BASE_URL . "/admin/login.php");
        exit();
    }
}

/**
 * ตั้งค่าข้อความแจ้งเตือน Flash Message
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash'][$type] = $message;
}

/**
 * แสดงข้อความแจ้งเตือน Flash Message (ถ้ามี)
 */
function renderFlashMessages() {
    if (isset($_SESSION['flash'])) {
        foreach ($_SESSION['flash'] as $type => $message) {
            $alertClass = ($type === 'error' || $type === 'danger') ? 'alert-danger' : 'alert-success';
            echo '<div class="alert ' . $alertClass . ' alert-dismissible fade show" role="alert">';
            echo e($message);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
        }
        unset($_SESSION['flash']);
    }
    
    // รองรับ session success / error แบบเดิม
    if (isset($_SESSION['success'])) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
        echo e($_SESSION['success']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
        echo e($_SESSION['error']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        unset($_SESSION['error']);
    }
}

/**
 * ฟังก์ชันสำหรับแปลงภาพ BLOB หรือ URL รูปภาพ
 */
function getImageSrc($blobData, $fallbackPath = '') {
    if (!empty($blobData)) {
        return 'data:image/jpeg;base64,' . base64_encode($blobData);
    }
    return !empty($fallbackPath) ? $fallbackPath : BASE_URL . '/images/logo/logo.png';
}
