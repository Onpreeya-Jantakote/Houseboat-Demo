<?php
/**
 * Root server.php (Backward-compatible connection for existing files)
 */
$servername = getenv('DB_HOST') ?: "localhost";
$username = getenv('DB_USER') ?: "root";
$password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : "";
$dbname = getenv('DB_NAME') ?: "houseboat";

// สร้างการเชื่อมต่อ MySQLi
$conn = @new mysqli($servername, $username, $password, $dbname);

// หากเชื่อมต่อผิดพลาด ให้จัดการข้อความ Error อย่างเหมาะสม
if ($conn->connect_error) {
    // ไม่ die ด้วยข้อความระบบดิบ แต่แนะนำวิธีแก้ไข
    // die("Connection failed: " . $conn->connect_error);
} else {
    $conn->set_charset("utf8mb4");
}
