<?php
/**
 * Database Connection File (PDO)
 * รองรับการทำงานร่วมกับ XAMPP (Apache & MySQL / MariaDB)
 */

// โหลดค่าจากไฟล์ .env หากมี (แบบง่าย ไม่ต้องใช้ Library ภายนอก)
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }
        if (strpos($line, '=') !== false) {
            list($key, $val) = explode('=', $line, 2);
            $key = trim($key);
            $val = trim($val, " \t\n\r\0\x0B\"'");
            if (!getenv($key)) {
                putenv("$key=$val");
            }
        }
    }
}

// กำหนดค่าเริ่มต้นสำหรับ XAMPP
$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbPort = getenv('DB_PORT') ?: '3306';
$dbName = getenv('DB_NAME') ?: 'houseboat';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

try {
    $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
    $conn = $pdo; // รองรับทั้งตัวแปร $conn และ $pdo
} catch (PDOException $e) {
    // แสดงข้อความแนะนำที่ชัดเจนสำหรับ Junior Developer โดยไม่เปิดเผย Credential
    die(
        "<div style='font-family: Arial, sans-serif; padding: 20px; background-color: #fee; border: 1px solid #fcc; border-radius: 5px; margin: 20px;'>" .
        "<h3 style='color: #c00;'>เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล</h3>" .
        "<p>ไม่สามารถเชื่อมต่อฐานข้อมูลได้ กรุณาตรวจสอบดังนี้:</p>" .
        "<ul>" .
        "<li>ตรวจสอบว่าได้เปิด <strong>MySQL</strong> ใน <strong>XAMPP Control Panel</strong> หรือยัง</li>" .
        "<li>ตรวจสอบว่าได้สร้างฐานข้อมูลชื่อ <code>" . htmlspecialchars($dbName) . "</code> ใน phpMyAdmin หรือยัง</li>" .
        "<li>นำเข้าไฟล์ <code>database/houseboat.sql</code> ผ่าน phpMyAdmin</li>" .
        "<li>ตรวจสอบรหัสผ่าน MySQL ในไฟล์ <code>.env</code> หรือ <code>config/database.php</code></li>" .
        "</ul>" .
        "<p><small style='color: #666;'>รายละเอียดข้อผิดพลาด: " . htmlspecialchars($e->getMessage()) . "</small></p>" .
        "</div>"
    );
}
