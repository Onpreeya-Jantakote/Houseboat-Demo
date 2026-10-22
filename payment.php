<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

$ab_id = intval($_GET['ab_id'] ?? $_SESSION['ab_id'] ?? 0);

if ($ab_id <= 0) {
    setFlashMessage('error', 'ไม่พบรหัสการจอง กรุณาทำรายการใหม่อีกครั้ง');
    header("Location: " . BASE_URL . "/rooms.php");
    exit();
}

// ค้นหาข้อมูลการจอง
$stmt = $pdo->prepare("SELECT a.*, r.price, t.tr_name FROM anonybooking a 
                       INNER JOIN room r ON a.rm_id = r.rm_id 
                       INNER JOIN typeroom t ON r.tr_id = t.tr_id 
                       WHERE a.ab_id = ?");
$stmt->execute([$ab_id]);
$booking = $stmt->fetch();

if (!$booking) {
    setFlashMessage('error', 'ไม่พบข้อมูลการจองห้องพักในระบบ');
    header("Location: " . BASE_URL . "/booking.php");
    exit();
}

$pageTitle = "ชำระเงินค่ามัดจำ - ห้อง " . $booking['rm_id'];
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
        $fileTmp  = $_FILES['receipt']['tmp_name'];
        $fileSize = $_FILES['receipt']['size'];
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($fileInfo, $fileTmp);
        finfo_close($fileInfo);

        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($mimeType, $allowedMimes)) {
            $error = "กรุณาอัปโหลดไฟล์รูปภาพที่ถูกต้อง (JPG, PNG หรือ WebP เท่านั้น)";
        } elseif ($fileSize > 5 * 1024 * 1024) { // ไม่เกิน 5MB
            $error = "ขนาดไฟล์ต้องไม่เกิน 5 MB";
        } else {
            $imageContent = file_get_contents($fileTmp);
            
            // อัปเดตข้อมูลสลิปและสถานะเป็น 'รอดำเนินการ'
            $updateSql = "UPDATE anonybooking SET status = 'รอดำเนินการ', date_pay = CURDATE(), receipt = ? WHERE ab_id = ?";
            $updateStmt = $pdo->prepare($updateSql);
            if ($updateStmt->execute([$imageContent, $ab_id])) {
                // บันทึกไฟล์สำรองลงใน uploads/receipts ด้วย
                $ext = ($mimeType === 'image/png') ? 'png' : (($mimeType === 'image/webp') ? 'webp' : 'jpg');
                $savePath = __DIR__ . "/uploads/receipts/anon_{$ab_id}_" . time() . ".{$ext}";
                @file_put_contents($savePath, $imageContent);

                setFlashMessage('success', 'อัปโหลดสลิปเรียบร้อยแล้ว กรุณารอเจ้าหน้าที่ตรวจสอบ');
                header("Location: " . BASE_URL . "/finish.php");
                exit();
            } else {
                $error = "เกิดข้อผิดพลาดในการบันทึกข้อมูลสลิป กรุณาลองใหม่อีกครั้ง";
            }
        }
    } else {
        $error = "กรุณาเลือกไฟล์สลิปหลักฐานการโอนเงิน";
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-4">
    <!-- Step Progress Bar -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-8">
            <div class="d-flex justify-content-between position-relative text-center">
                <div class="flex-fill">
                    <div class="badge rounded-pill bg-success p-2 mb-1" style="width: 32px; height: 32px;"><i class="fa-solid fa-check"></i></div>
                    <div class="small text-muted">กรอกข้อมูลส่วนตัว</div>
                </div>
                <div class="flex-fill">
                    <div class="badge rounded-pill bg-danger p-2 mb-1" style="width: 32px; height: 32px;">2</div>
                    <div class="small fw-bold text-danger">ชำระเงินค่ามัดจำ</div>
                </div>
                <div class="flex-fill">
                    <div class="badge rounded-pill bg-secondary p-2 mb-1" style="width: 32px; height: 32px;">3</div>
                    <div class="small text-muted">เสร็จสิ้น</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-4 text-center">
                    <h4 class="fw-bold mb-2" style="color: #1a237e;">ชำระเงินค่ามัดจำห้องพัก</h4>
                    <p class="text-danger fw-bold fs-5 mb-3">* ค่ามัดจำ <?= formatPrice(DEPOSIT_AMOUNT) ?> บาท *</p>

                    <!-- Booking Info Box -->
                    <div class="bg-light p-3 rounded-3 text-start small mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">ผู้เข้าพัก:</span>
                            <span class="fw-bold"><?= e($booking['am_name']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">ห้องพัก:</span>
                            <span class="fw-bold">ห้อง <?= e($booking['rm_id']) ?> (<?= e($booking['tr_name']) ?>)</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">ช่วงเวลาเข้าพัก:</span>
                            <span><?= thaiDate($booking['check_in']) ?> - <?= thaiDate($booking['check_out']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">เตียงเสริม:</span>
                            <span><?= ($booking['add_bed'] == 1) ? 'เพิ่มเตียงเสริม (+500 บาท)' : 'ไม่เพิ่ม' ?></span>
                        </div>
                    </div>

                    <!-- Payment QR Code Image -->
                    <div class="mb-4">
                        <p class="small text-muted mb-2">สแกน QR Code หรือโอนเงินผ่านบัญชีธนาคารด้านล่าง</p>
                        <img src="<?= BASE_URL ?>/images/payment/1000016739.png" alt="QR Code สำหรับชำระเงิน" 
                             class="img-fluid border rounded-3 p-2 shadow-sm" style="max-height: 280px;">
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger py-2 small text-start" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-1"></i> <?= e($error) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Upload Form -->
                    <form action="<?= BASE_URL ?>/payment.php?ab_id=<?= $ab_id ?>" method="POST" enctype="multipart/form-data" class="text-start">
                        <div class="mb-3">
                            <label for="receipt" class="form-label fw-semibold">อัปโหลดสลิปหลักฐานการโอนเงิน <span class="text-danger">*</span></label>
                            <input class="form-control" type="file" name="receipt" id="receipt" accept="image/*" required>
                            <div class="form-text small">รองรับไฟล์ภาพ JPG, PNG, WebP ขนาดไม่เกิน 5MB</div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-2 shadow-none fw-bold">
                            <i class="fa-solid fa-cloud-arrow-up me-1"></i> ยืนยันการชำระเงิน
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
