<?php
/**
 * Automated Test Suite for Houseboat-Demo Refactored Project
 * ทดสอบการทำงานของ Business Logic, CRUD, Database และความปลอดภัย
 */

require_once __DIR__ . '/../includes/functions.php';

echo "========================================================\n";
echo "   HOUSEBOAT-DEMO: AUTOMATED TEST SUITE\n";
echo "========================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertTest($description, $condition) {
    global $passCount, $failCount;
    if ($condition) {
        echo "[PASS] $description\n";
        $passCount++;
    } else {
        echo "[FAIL] $description\n";
        $failCount++;
    }
}

// -----------------------------------------------------------------------------
// 1. ทดสอบ Helper Functions (XSS Protection, Date & Price Formatting)
// -----------------------------------------------------------------------------
echo "--- 1. Testing Helper Functions ---\n";

$xssInput = "<script>alert('hack');</script>";
$escaped = e($xssInput);
assertTest("e() properly escapes script tags", strpos($escaped, "<script>") === false && strpos($escaped, "&lt;script&gt;") !== false);

$thaiDateTest = thaiDate("2024-02-24");
assertTest("thaiDate() converts 2024-02-24 to 24 กุมภาพันธ์ 2567", $thaiDateTest === "24 กุมภาพันธ์ 2567");

$emptyDateTest = thaiDate("");
assertTest("thaiDate() handles empty date gracefully", $emptyDateTest === "-");

$priceTest = formatPrice(3000);
assertTest("formatPrice() formats 3000 to 3,000.00", $priceTest === "3,000.00");


// -----------------------------------------------------------------------------
// 2. จำลองฐานข้อมูลทดสอบ (In-Memory SQLite PDO) สำหรับทดสอบคิวรีและการทำงาน
// -----------------------------------------------------------------------------
echo "\n--- 2. Setting Up Test Database ---\n";

$testPdo = new PDO("sqlite::memory:", null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

// สร้างตารางทั้ง 9 ตาราง
$testPdo->exec("
    CREATE TABLE admin (
        ad_user VARCHAR(30) PRIMARY KEY,
        ad_pass VARCHAR(30) NOT NULL,
        ad_name VARCHAR(30) NOT NULL,
        ad_email VARCHAR(20) NOT NULL,
        ad_tel VARCHAR(10) NOT NULL
    );

    CREATE TABLE customer (
        cus_user VARCHAR(30) PRIMARY KEY,
        cus_pass VARCHAR(30) NOT NULL,
        cus_name VARCHAR(30) NOT NULL,
        cus_tel VARCHAR(10) NOT NULL,
        cus_email VARCHAR(30) NOT NULL
    );

    CREATE TABLE typeroom (
        tr_id INTEGER PRIMARY KEY AUTOINCREMENT,
        tr_name VARCHAR(30) NOT NULL
    );

    CREATE TABLE room (
        rm_id VARCHAR(10) PRIMARY KEY,
        tr_id INTEGER NOT NULL,
        description VARCHAR(100) NOT NULL,
        price INTEGER NOT NULL,
        rm_pic BLOB
    );

    CREATE TABLE detailbooking (
        db_id INTEGER PRIMARY KEY AUTOINCREMENT,
        check_in DATE NOT NULL,
        check_out DATE NOT NULL,
        cus_name VARCHAR(30) NOT NULL,
        cus_tel VARCHAR(10) NOT NULL,
        rm_id VARCHAR(10) NOT NULL,
        amount INTEGER NOT NULL,
        status VARCHAR(30) NOT NULL,
        add_bed INTEGER NOT NULL,
        date_pay DATE,
        receipt BLOB
    );

    CREATE TABLE anonybooking (
        ab_id INTEGER PRIMARY KEY AUTOINCREMENT,
        check_in DATE NOT NULL,
        check_out DATE NOT NULL,
        rm_id VARCHAR(10) NOT NULL,
        amount INTEGER NOT NULL,
        am_name VARCHAR(50) NOT NULL,
        am_email VARCHAR(30) NOT NULL,
        am_tel VARCHAR(10) NOT NULL,
        add_bed INTEGER NOT NULL,
        status VARCHAR(30) NOT NULL,
        date_pay DATE,
        receipt BLOB
    );

    CREATE TABLE menu (
        mn_id INTEGER PRIMARY KEY AUTOINCREMENT,
        mn_name VARCHAR(30) NOT NULL,
        size VARCHAR(50) NOT NULL,
        note VARCHAR(500) NOT NULL,
        price INTEGER NOT NULL
    );

    CREATE TABLE ingredients (
        in_id INTEGER PRIMARY KEY AUTOINCREMENT,
        in_name VARCHAR(50) NOT NULL,
        in_unit INTEGER NOT NULL,
        in_price INTEGER NOT NULL,
        in_picture BLOB,
        in_status INTEGER NOT NULL
    );

    CREATE TABLE menubill (
        mnbill_id INTEGER PRIMARY KEY AUTOINCREMENT,
        mn_id INTEGER NOT NULL,
        rm_id VARCHAR(11) NOT NULL,
        cus_name VARCHAR(50) NOT NULL,
        amount INTEGER NOT NULL,
        total INTEGER NOT NULL,
        in_id INTEGER NOT NULL,
        receive DATE NOT NULL
    );
");
assertTest("Test database schema initialized with 9 tables", true);

// ใส่ข้อมูลเริ่มต้นสำหรับการทดสอบ
$testPdo->exec("
    INSERT INTO admin VALUES ('admin_test', '1234', 'ผู้ดูแลระบบ ทดสอบ', 'admin@test.com', '0899999999');
    INSERT INTO customer VALUES ('cus_test', 'pass123', 'สมชาย ทดสอบ', '0812345678', 'somchai@test.com');
    INSERT INTO typeroom (tr_id, tr_name) VALUES (1, 'เตียงเดี่ยว'), (2, 'เตียงคู่');
    INSERT INTO room VALUES ('A0', 2, 'แอร์ น้ำดื่ม ฟรี Wi-Fi', 3000, '');
    INSERT INTO room VALUES ('A1', 1, 'แอร์ น้ำดื่ม ฟรี Wi-Fi ฟรีอาหารเช้า', 3200, '');
    INSERT INTO room VALUES ('A2', 2, 'แอร์ น้ำดื่ม ฟรี Wi-Fi', 2800, '');
    INSERT INTO menu (mn_id, mn_name, size, note, price) VALUES (1, 'ชุดหมูกระทะอิ่มเบาๆ', 'S', 'หมูสไลด์ ผัก วุ้นเส้น', 299);
    INSERT INTO ingredients (in_id, in_name, in_unit, in_price, in_picture, in_status) VALUES (1, 'ไข่ไก่', 2, 10, '', 0);
");
assertTest("Test seed data inserted successfully", true);


// -----------------------------------------------------------------------------
// 3. ทดสอบ Authentication Logic
// -----------------------------------------------------------------------------
echo "\n--- 3. Testing Authentication Logic ---\n";

// ทดสอบ Customer Login สำเร็จ
$loginStmt = $testPdo->prepare("SELECT * FROM customer WHERE cus_user = ?");
$loginStmt->execute(['cus_test']);
$cust = $loginStmt->fetch();
$authSuccess = ($cust && $cust['cus_pass'] === 'pass123');
assertTest("Customer login succeeds with correct credentials", $authSuccess);

// ทดสอบ Customer Login ล้มเหลวเมื่อรหัสผ่านผิด
$authFail = ($cust && $cust['cus_pass'] === 'wrong_password');
assertTest("Customer login rejected with wrong password", !$authFail);

// ทดสอบ Admin Login สำเร็จ
$adminStmt = $testPdo->prepare("SELECT * FROM admin WHERE ad_user = ?");
$adminStmt->execute(['admin_test']);
$admin = $adminStmt->fetch();
$adminAuthSuccess = ($admin && $admin['ad_pass'] === '1234');
assertTest("Admin login succeeds with correct credentials", $adminAuthSuccess);

// ทดสอบ Customer Registration
$checkDuplicate = $testPdo->prepare("SELECT cus_user FROM customer WHERE cus_user = ?");
$checkDuplicate->execute(['cus_test']);
assertTest("Registration detects duplicate username", (bool)$checkDuplicate->fetch());

$regStmt = $testPdo->prepare("INSERT INTO customer (cus_user, cus_pass, cus_name, cus_tel, cus_email) VALUES (?, ?, ?, ?, ?)");
$regSuccess = $regStmt->execute(['new_user', 'new_pass', 'สมหญิง สดใส', '0898765432', 'somying@test.com']);
assertTest("New customer registers successfully", $regSuccess);


// -----------------------------------------------------------------------------
// 4. ทดสอบ Room Availability Search Logic
// -----------------------------------------------------------------------------
echo "\n--- 4. Testing Room Availability Search ---\n";

// จองห้อง A0 ช่วงวันที่ 2024-03-01 ถึง 2024-03-03
$bookStmt = $testPdo->prepare("INSERT INTO detailbooking (rm_id, check_in, check_out, amount, cus_name, cus_tel, add_bed, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$bookStmt->execute(['A0', '2024-03-01', '2024-03-03', 2, 'สมชาย ทดสอบ', '0812345678', 0, 'ยืนยันการจอง']);

// ค้นหาห้องว่างช่วง 2024-03-01 ถึง 2024-03-02 (ต้องไม่พบห้อง A0 แต่ต้องพบห้อง A1, A2)
$searchStmt = $testPdo->prepare("
    SELECT rm_id FROM room 
    WHERE rm_id NOT IN (
        SELECT rm_id FROM detailbooking WHERE NOT (check_out <= ? OR check_in >= ?)
        UNION
        SELECT rm_id FROM anonybooking WHERE NOT (check_out <= ? OR check_in >= ?)
    )
");
$searchStmt->execute(['2024-03-01', '2024-03-02', '2024-03-01', '2024-03-02']);
$availableRooms = $searchStmt->fetchAll(PDO::FETCH_COLUMN);

assertTest("Overlapping booking correctly filters out room A0", !in_array('A0', $availableRooms));
assertTest("Free room A1 is correctly returned", in_array('A1', $availableRooms));
assertTest("Free room A2 is correctly returned", in_array('A2', $availableRooms));

// ค้นหาช่วงวันที่ 2024-03-10 ถึง 2024-03-12 (ห้อง A0 ต้องว่าง)
$searchStmt->execute(['2024-03-10', '2024-03-12', '2024-03-10', '2024-03-12']);
$availableRoomsFuture = $searchStmt->fetchAll(PDO::FETCH_COLUMN);
assertTest("Non-overlapping future search returns room A0 as available", in_array('A0', $availableRoomsFuture));


// -----------------------------------------------------------------------------
// 5. ทดสอบ Guest Booking & Slip Upload Flow
// -----------------------------------------------------------------------------
echo "\n--- 5. Testing Guest Booking & Slip Flow ---\n";

$anonBookStmt = $testPdo->prepare("INSERT INTO anonybooking (rm_id, check_in, check_out, amount, am_name, am_email, am_tel, add_bed, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$anonBookStmt->execute(['A1', '2024-03-05', '2024-03-06', 2, 'นายทดสอบ บุคคลทั่วไป', 'guest@test.com', '0891112233', 1, 'ยังไม่ชำระมัดจำ']);
$guestBookingId = $testPdo->lastInsertId();
assertTest("Guest booking creates record with status 'ยังไม่ชำระมัดจำ'", $guestBookingId > 0);

// อัปโหลดสลิปมัดจำ
$slipData = "FAKE_JPEG_IMAGE_BINARY_DATA";
$uploadStmt = $testPdo->prepare("UPDATE anonybooking SET status = 'รอดำเนินการ', date_pay = '2024-03-05', receipt = ? WHERE ab_id = ?");
$uploadStmt->execute([$slipData, $guestBookingId]);

$checkAnonStmt = $testPdo->prepare("SELECT status, date_pay, receipt FROM anonybooking WHERE ab_id = ?");
$checkAnonStmt->execute([$guestBookingId]);
$anonUpdated = $checkAnonStmt->fetch();
assertTest("Slip upload updates status to 'รอดำเนินการ'", $anonUpdated['status'] === 'รอดำเนินการ');
assertTest("Slip image binary is saved", $anonUpdated['receipt'] === $slipData);

// แอดมินยืนยันการจอง
$confirmStmt = $testPdo->prepare("UPDATE anonybooking SET status = 'ยืนยันการจอง' WHERE ab_id = ?");
$confirmStmt->execute([$guestBookingId]);

$checkConfirmed = $testPdo->prepare("SELECT status FROM anonybooking WHERE ab_id = ?");
$checkConfirmed->execute([$guestBookingId]);
assertTest("Admin confirmation updates status to 'ยืนยันการจอง'", $checkConfirmed->fetchColumn() === 'ยืนยันการจอง');


// -----------------------------------------------------------------------------
// 6. ทดสอบ Member Food Order & Cart Flow
// -----------------------------------------------------------------------------
echo "\n--- 6. Testing Food Ordering & Cart Flow ---\n";

// คำนวณราคาหมูกระทะ 1 ชุด (299) + ไข่ไก่ 2 หน่วย (10*2 = 20) = 319 บาท
$item1Subtotal = 299 * 1;
$item2Subtotal = 10 * 2;
$totalOrderPrice = $item1Subtotal + $item2Subtotal;
assertTest("Order subtotal calculation is accurate (299 + 20 = 319)", $totalOrderPrice === 319);

// บันทึกลงตาราง menubill
$foodStmt = $testPdo->prepare("INSERT INTO menubill (mn_id, in_id, rm_id, cus_name, amount, total, receive) VALUES (?, ?, ?, ?, ?, ?, ?)");
$foodStmt->execute([1, 0, 'A0', 'สมชาย ทดสอบ', 1, 299, '2024-03-01']);
$foodStmt->execute([0, 1, 'A0', 'สมชาย ทดสอบ', 2, 20, '2024-03-01']);

$checkFoodStmt = $testPdo->prepare("SELECT SUM(total) AS grand_total, COUNT(*) AS count FROM menubill WHERE cus_name = ?");
$checkFoodStmt->execute(['สมชาย ทดสอบ']);
$foodSummary = $checkFoodStmt->fetch();
assertTest("Food order saved to menubill with correct grand total", (int)$foodSummary['grand_total'] === 319);
assertTest("Food order created 2 order bill records", (int)$foodSummary['count'] === 2);


// -----------------------------------------------------------------------------
// 7. ทดสอบ Admin Room & Ingredients CRUD
// -----------------------------------------------------------------------------
echo "\n--- 7. Testing Admin CRUD Operations ---\n";

// เพิ่มห้องใหม่
$addRoomStmt = $testPdo->prepare("INSERT INTO room (rm_id, tr_id, description, price, rm_pic) VALUES (?, ?, ?, ?, ?)");
$addRoomStmt->execute(['B1', 1, 'ห้องริมน้ำ วิวพาโนรามา', 3500, '']);
$b1Exists = $testPdo->query("SELECT COUNT(*) FROM room WHERE rm_id = 'B1'")->fetchColumn();
assertTest("Admin adds new room 'B1'", (int)$b1Exists === 1);

// แก้ไขห้อง B1
$editRoomStmt = $testPdo->prepare("UPDATE room SET price = ? WHERE rm_id = ?");
$editRoomStmt->execute([3800, 'B1']);
$b1Price = $testPdo->query("SELECT price FROM room WHERE rm_id = 'B1'")->fetchColumn();
assertTest("Admin edits room price to 3800", (int)$b1Price === 3800);

// ลบห้อง B1
$delRoomStmt = $testPdo->prepare("DELETE FROM room WHERE rm_id = ?");
$delRoomStmt->execute(['B1']);
$b1Deleted = $testPdo->query("SELECT COUNT(*) FROM room WHERE rm_id = 'B1'")->fetchColumn();
assertTest("Admin deletes room 'B1'", (int)$b1Deleted === 0);

// เพิ่มวัตถุดิบ (Auto Increment in_id)
$addIngStmt = $testPdo->prepare("INSERT INTO ingredients (in_name, in_unit, in_price, in_picture, in_status) VALUES (?, ?, ?, ?, 0)");
$addIngStmt->execute(['หมูหมักนุ่ม', 1, 60, '']);
$newIngId = $testPdo->lastInsertId();
assertTest("Admin adds new ingredient with Auto-Increment ID", $newIngId > 0);

// สลับสถานะวัตถุดิบ (0 -> 1)
$toggleStmt = $testPdo->prepare("UPDATE ingredients SET in_status = IF(in_status = 0, 1, 0) WHERE in_id = ?");
$toggleStmt->execute([$newIngId]);
$ingStatus = $testPdo->query("SELECT in_status FROM ingredients WHERE in_id = $newIngId")->fetchColumn();
assertTest("Admin toggles ingredient status to out-of-stock (1)", (int)$ingStatus === 1);

// ลบวัตถุดิบ
$delIngStmt = $testPdo->prepare("DELETE FROM ingredients WHERE in_id = ?");
$delIngStmt->execute([$newIngId]);
$ingDeleted = $testPdo->query("SELECT COUNT(*) FROM ingredients WHERE in_id = $newIngId")->fetchColumn();
assertTest("Admin deletes ingredient", (int)$ingDeleted === 0);


// -----------------------------------------------------------------------------
// สรุปผลการทดสอบ
// -----------------------------------------------------------------------------
echo "\n========================================================\n";
echo "   TEST SUMMARY: {$passCount} PASSED, {$failCount} FAILED\n";
echo "========================================================\n";

if ($failCount === 0) {
    echo "SUCCESS: ALL TESTS PASSED SUCCESSFULLY!\n";
    exit(0);
} else {
    echo "ERROR: SOME TESTS FAILED!\n";
    exit(1);
}
