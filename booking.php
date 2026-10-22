<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = "ตรวจสอบการจอง";
$search = trim($_GET['search'] ?? '');
$bookings = [];

if (!empty($search)) {
    // ใช้ Prepared Statement เพื่อป้องกัน SQL Injection
    $stmt = $pdo->prepare("SELECT * FROM anonybooking WHERE am_name LIKE ? ORDER BY ab_id DESC");
    $stmt->execute(["%$search%"]);
    $bookings = $stmt->fetchAll();
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1" style="color: #1a237e;">ตรวจสอบสถานะการจอง (สำหรับบุคคลทั่วไป)</h2>
            <p class="text-muted small">ค้นหาข้อมูลการจองห้องพักด้วยชื่อ-นามสกุลของผู้เข้าพัก</p>
        </div>
        <a href="<?= BASE_URL ?>/rooms.php" class="btn btn-primary custom-bg btn-sm shadow-none">
            <i class="fa-solid fa-calendar-plus me-1"></i> จองห้องพักใหม่
        </a>
    </div>

    <!-- Search Form -->
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-body p-4">
            <form action="<?= BASE_URL ?>/booking.php" method="GET" class="row g-2 align-items-center">
                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" class="form-control" name="search" id="search" 
                               placeholder="กรอกชื่อ หรือนามสกุล ที่ใช้ในการจองห้องพัก..." 
                               value="<?= e($search) ?>" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100 custom-bg shadow-none">ค้นหาการจอง</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Table -->
    <?php if (!empty($search)): ?>
        <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">ผลลัพธ์การค้นหา: "<?= e($search) ?>" (พบ <?= count($bookings) ?> รายการ)</h5>
            </div>
            <div class="card-body p-0">
                <?php if (count($bookings) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>รหัสห้อง</th>
                                    <th>ชื่อผู้เข้าพัก</th>
                                    <th>เบอร์โทร</th>
                                    <th>เช็คอิน</th>
                                    <th>เช็คเอาท์</th>
                                    <th>เตียงเสริม</th>
                                    <th>สถานะ</th>
                                    <th>วันที่ชำระเงิน</th>
                                    <th>การจัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bookings as $b): ?>
                                    <tr>
                                        <td><span class="badge bg-secondary fs-6">ห้อง <?= e($b['rm_id']) ?></span></td>
                                        <td class="fw-semibold"><?= e($b['am_name']) ?></td>
                                        <td><?= e($b['am_tel']) ?></td>
                                        <td><?= thaiDate($b['check_in']) ?></td>
                                        <td><?= thaiDate($b['check_out']) ?></td>
                                        <td><?= ($b['add_bed'] == 1) ? '<span class="text-success"><i class="fa-solid fa-check"></i> เพิ่ม (+500)</span>' : '<span class="text-muted">-</span>' ?></td>
                                        <td>
                                            <?php if ($b['status'] === 'ยืนยันการจอง'): ?>
                                                <span class="badge bg-success"><i class="fa-solid fa-check-circle me-1"></i> ยืนยันการจอง</span>
                                            <?php elseif ($b['status'] === 'รอดำเนินการ'): ?>
                                                <span class="badge bg-warning text-dark"><i class="fa-solid fa-clock me-1"></i> รอดำเนินการตรวจสอบ</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?= e($b['status'] ?: 'ยังไม่ชำระมัดจำ') ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= !empty($b['date_pay']) ? thaiDate($b['date_pay']) : '<span class="text-muted">-</span>' ?></td>
                                        <td>
                                            <?php if (empty($b['receipt'])): ?>
                                                <a href="<?= BASE_URL ?>/payment.php?ab_id=<?= $b['ab_id'] ?>" class="btn btn-sm btn-outline-danger shadow-none">
                                                    <i class="fa-solid fa-upload me-1"></i> แนบสลิป
                                                </a>
                                            <?php else: ?>
                                                <span class="text-success small"><i class="fa-solid fa-receipt me-1"></i> แนบสลิปแล้ว</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fa-solid fa-file-circle-question text-muted fs-1 mb-2"></i>
                        <p class="text-muted">ไม่พบข้อมูลการจองที่ตรงกับชื่อ "<?= e($search) ?>"</p>
                        <small class="text-muted">กรุณาตรวจสอบการสะกดชื่อ-นามสกุล หรือติดต่อเจ้าหน้าที่</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
