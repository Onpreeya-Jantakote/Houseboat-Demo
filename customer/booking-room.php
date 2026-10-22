<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireCustomerLogin();

$rm_id     = trim($_GET['rm_id'] ?? '');
$check_in  = trim($_GET['check_in'] ?? date('Y-m-d'));
$check_out = trim($_GET['check_out'] ?? date('Y-m-d', strtotime('+1 day')));
$amount    = trim($_GET['amount'] ?? '2');

if (empty($rm_id)) {
    setFlashMessage('error', 'กรุณาเลือกห้องพักที่ต้องการจอง');
    header("Location: " . BASE_URL . "/customer/rooms.php");
    exit();
}

// ดึงข้อมูลห้องพักที่เลือก
$stmt = $pdo->prepare("SELECT room.*, typeroom.tr_name FROM room INNER JOIN typeroom ON room.tr_id = typeroom.tr_id WHERE room.rm_id = ?");
$stmt->execute([$rm_id]);
$room = $stmt->fetch();

if (!$room) {
    setFlashMessage('error', 'ไม่พบข้อมูลห้องพักที่เลือก');
    header("Location: " . BASE_URL . "/customer/rooms.php");
    exit();
}

$pageTitle = "จองห้องพักสมาชิก - ห้อง " . $room['rm_id'];
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cus_name = $_SESSION['cus_name'];
    $cus_tel  = $_SESSION['cus_tel'];
    $add_bed  = isset($_POST['add_bed']) ? 1 : 0;

    // บันทึกการจองลงในตาราง detailbooking
    $sql = "INSERT INTO detailbooking (rm_id, check_in, check_out, amount, cus_name, cus_tel, add_bed, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'ยังไม่ชำระมัดจำ')";
    $insertStmt = $pdo->prepare($sql);
    if ($insertStmt->execute([$rm_id, $check_in, $check_out, $amount, $cus_name, $cus_tel, $add_bed])) {
        $db_id = $pdo->lastInsertId();
        $_SESSION['db_id'] = $db_id;

        setFlashMessage('success', 'บันทึกการจองเรียบร้อยแล้ว กรุณาชำระเงินมัดจำ');
        header("Location: " . BASE_URL . "/customer/payment.php?db_id=" . $db_id);
        exit();
    } else {
        $error = "เกิดข้อผิดพลาดในการบันทึกการจอง กรุณาลองใหม่อีกครั้ง";
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-4">
    <!-- Step Progress Bar -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-8">
            <div class="d-flex justify-content-between position-relative text-center">
                <div class="flex-fill">
                    <div class="badge rounded-pill bg-danger p-2 mb-1" style="width: 32px; height: 32px;">1</div>
                    <div class="small fw-bold text-danger">ข้อมูลการจอง</div>
                </div>
                <div class="flex-fill">
                    <div class="badge rounded-pill bg-secondary p-2 mb-1" style="width: 32px; height: 32px;">2</div>
                    <div class="small text-muted">ชำระเงินค่ามัดจำ</div>
                </div>
                <div class="flex-fill">
                    <div class="badge rounded-pill bg-secondary p-2 mb-1" style="width: 32px; height: 32px;">3</div>
                    <div class="small text-muted">เสร็จสิ้น</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Form Section -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-3" style="color: #1a237e;">
                        <i class="fa-solid fa-clipboard-check me-2"></i> ข้อมูลผู้เข้าพัก (ดึงจากบัญชีของคุณ)
                    </h4>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger py-2 small" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-1"></i> <?= e($error) ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= BASE_URL ?>/customer/booking-room.php?<?= http_build_query($_GET) ?>" method="POST">
                        <div class="mb-3">
                            <label class="form-label text-muted small">ชื่อ - นามสกุลผู้จอง</label>
                            <input type="text" class="form-control bg-light" value="<?= e($_SESSION['cus_name']) ?>" readonly>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small">เบอร์โทรศัพท์</label>
                                <input type="text" class="form-control bg-light" value="<?= e($_SESSION['cus_tel']) ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small">อีเมล</label>
                                <input type="text" class="form-control bg-light" value="<?= e($_SESSION['cus_email']) ?>" readonly>
                            </div>
                        </div>

                        <div class="card bg-light border-0 p-3 mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="add_bed" id="add_bed" value="1" <?= isset($_POST['add_bed']) ? 'checked' : '' ?>>
                                <label class="form-check-label fw-semibold" for="add_bed">
                                    เพิ่มเตียงเสริม (+<?= formatPrice(EXTRA_BED_PRICE) ?> บาท/คืน)
                                </label>
                                <div class="form-text small">บริการเตียงเสริมพร้อมเครื่องนอนครบชุด</div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="<?= BASE_URL ?>/customer/rooms.php" class="btn btn-outline-secondary shadow-none">
                                <i class="fa-solid fa-arrow-left me-1"></i> ย้อนกลับ
                            </a>
                            <button type="submit" class="btn btn-primary custom-bg px-4 py-2 shadow-none">
                                ดำเนินการต่อ (ชำระมัดจำ) <i class="fa-solid fa-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Room Summary Section -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 rounded-3">
                <?php 
                    $fallbackImage = BASE_URL . "/images/rooms/" . $room["rm_id"] . ".jpg";
                    $imgSrc = getImageSrc($room["rm_pic"], $fallbackImage);
                ?>
                <img src="<?= $imgSrc ?>" class="card-img-top room-img" alt="ห้อง <?= e($room["rm_id"]) ?>" onerror="this.src='<?= BASE_URL ?>/images/cover/1.jpg';">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-1" style="color: #1a237e;">สรุปการจองห้องพัก</h5>
                    <p class="text-muted small border-bottom pb-2">ห้อง <?= e($room['rm_id']) ?> - <?= e($room['tr_name']) ?></p>

                    <div class="d-flex justify-content-between py-1 small">
                        <span class="text-muted">วันที่เช็คอิน:</span>
                        <span class="fw-bold"><?= thaiDate($check_in) ?></span>
                    </div>
                    <div class="d-flex justify-content-between py-1 small">
                        <span class="text-muted">วันที่เช็คเอาท์:</span>
                        <span class="fw-bold"><?= thaiDate($check_out) ?></span>
                    </div>
                    <div class="d-flex justify-content-between py-1 small">
                        <span class="text-muted">จำนวนผู้เข้าพัก:</span>
                        <span class="fw-bold"><?= e($amount) ?> ท่าน</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 small">
                        <span class="text-muted">ราคาห้องพัก:</span>
                        <span class="fw-bold text-success"><?= formatPrice($room['price']) ?> บาท/คืน</span>
                    </div>

                    <div class="alert alert-info py-2 px-3 small mt-3 mb-0">
                        <i class="fa-solid fa-info-circle me-1"></i> ชำระเงินมัดจำล่วงหน้า <strong><?= formatPrice(DEPOSIT_AMOUNT) ?> บาท</strong> เพื่อยืนยันการจอง
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
