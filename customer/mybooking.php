<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireCustomerLogin();

$pageTitle = "การจองของฉัน";
$cus_name = $_SESSION['cus_name'];
$activeTab = trim($_GET['tab'] ?? 'room');

// ดำเนินการยกเลิกการจองห้องพัก
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'cancel_booking') {
    $cancelDbId = intval($_POST['db_id'] ?? 0);
    if ($cancelDbId > 0) {
        $cancelStmt = $pdo->prepare("DELETE FROM detailbooking WHERE db_id = ? AND cus_name = ?");
        if ($cancelStmt->execute([$cancelDbId, $cus_name])) {
            setFlashMessage('success', 'ยกเลิกการจองห้องพักเรียบร้อยแล้ว');
        } else {
            setFlashMessage('error', 'ไม่สามารถยกเลิกการจองได้ กรุณาลองใหม่อีกครั้ง');
        }
    }
    header("Location: " . BASE_URL . "/customer/mybooking.php?tab=room");
    exit();
}

// ดึงข้อมูลการจองห้องพัก
$stmtRoom = $pdo->prepare("SELECT d.*, r.price, t.tr_name FROM detailbooking d 
                           LEFT JOIN room r ON d.rm_id = r.rm_id 
                           LEFT JOIN typeroom t ON r.tr_id = t.tr_id 
                           WHERE d.cus_name = ? 
                           ORDER BY d.db_id DESC");
$stmtRoom->execute([$cus_name]);
$roomBookings = $stmtRoom->fetchAll();

// ดึงข้อมูลการจองอาหารหมูกระทะ
$stmtFood = $pdo->prepare("SELECT mb.*, m.mn_name, m.size, i.in_name 
                           FROM menubill mb 
                           LEFT JOIN menu m ON mb.mn_id = m.mn_id 
                           LEFT JOIN ingredients i ON mb.in_id = i.in_id 
                           WHERE mb.cus_name = ? 
                           ORDER BY mb.mnbill_id DESC");
$stmtFood->execute([$cus_name]);
$foodOrders = $stmtFood->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1" style="color: #1a237e;">การจองของฉัน</h2>
            <p class="text-muted small">ตรวจสอบรายการจองห้องพักและรายการสั่งอาหารหมูกระทะของคุณ</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= BASE_URL ?>/customer/rooms.php" class="btn btn-outline-primary btn-sm shadow-none">
                <i class="fa-solid fa-bed me-1"></i> จองห้องพักเพิ่ม
            </a>
            <a href="<?= BASE_URL ?>/customer/menu.php" class="btn btn-warning btn-sm shadow-none">
                <i class="fa-solid fa-utensils me-1"></i> สั่งหมูกระทะ
            </a>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-pills mb-4 border-bottom pb-2">
        <li class="nav-item">
            <a class="nav-link <?= ($activeTab === 'room') ? 'active custom-bg' : 'text-dark' ?>" 
               href="<?= BASE_URL ?>/customer/mybooking.php?tab=room">
                <i class="fa-solid fa-house-chimney-window me-1"></i> จองห้องพัก (<?= count($roomBookings) ?>)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($activeTab === 'food') ? 'active custom-bg' : 'text-dark' ?>" 
               href="<?= BASE_URL ?>/customer/mybooking.php?tab=food">
                <i class="fa-solid fa-bowl-food me-1"></i> สั่งอาหารหมูกระทะ (<?= count($foodOrders) ?>)
            </a>
        </li>
    </ul>

    <!-- Tab 1: Room Bookings -->
    <?php if ($activeTab === 'room'): ?>
        <?php if (count($roomBookings) > 0): ?>
            <div class="row g-3">
                <?php foreach ($roomBookings as $b): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card shadow-sm border-0 rounded-3 h-100">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                                <span class="badge bg-primary fs-6 px-3">ห้อง <?= e($b['rm_id']) ?></span>
                                <div>
                                    <?php if ($b['status'] === 'ยืนยันการจอง'): ?>
                                        <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i> ยืนยันการจอง</span>
                                    <?php elseif ($b['status'] === 'รอดำเนินการ'): ?>
                                        <span class="badge bg-warning text-dark"><i class="fa-solid fa-clock me-1"></i> รอดำเนินการตรวจสอบ</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?= e($b['status'] ?: 'ยังไม่ชำระมัดจำ') ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted"><?= e($b['tr_name'] ?? 'ห้องพักเรือนแพ') ?></h6>
                                <div class="py-1 small d-flex justify-content-between">
                                    <span class="text-muted">วันที่เช็คอิน:</span>
                                    <span class="fw-bold"><?= thaiDate($b['check_in']) ?></span>
                                </div>
                                <div class="py-1 small d-flex justify-content-between">
                                    <span class="text-muted">วันที่เช็คเอาท์:</span>
                                    <span class="fw-bold"><?= thaiDate($b['check_out']) ?></span>
                                </div>
                                <div class="py-1 small d-flex justify-content-between">
                                    <span class="text-muted">จำนวนผู้เข้าพัก:</span>
                                    <span><?= e($b['amount']) ?> ท่าน</span>
                                </div>
                                <div class="py-1 small d-flex justify-content-between">
                                    <span class="text-muted">เตียงเสริม:</span>
                                    <span><?= ($b['add_bed'] == 1) ? '<span class="text-success fw-bold">+เตียงเสริม</span>' : 'ไม่เพิ่ม' ?></span>
                                </div>
                                <div class="py-1 small d-flex justify-content-between">
                                    <span class="text-muted">วันที่ชำระเงินมัดจำ:</span>
                                    <span><?= !empty($b['date_pay']) ? thaiDate($b['date_pay']) : '<span class="text-muted">-</span>' ?></span>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
                                <?php if (empty($b['receipt'])): ?>
                                    <a href="<?= BASE_URL ?>/customer/payment.php?db_id=<?= $b['db_id'] ?>" class="btn btn-sm btn-outline-danger shadow-none">
                                        <i class="fa-solid fa-upload me-1"></i> แนบสลิปมัดจำ
                                    </a>
                                <?php else: ?>
                                    <span class="text-success small"><i class="fa-solid fa-receipt me-1"></i> แนบสลิปแล้ว</span>
                                <?php endif; ?>

                                <?php if ($b['status'] !== 'ยืนยันการจอง'): ?>
                                    <form action="<?= BASE_URL ?>/customer/mybooking.php?tab=room" method="POST" onsubmit="return confirm('คุณต้องการยกเลิกการจองห้องนี้ใช่หรือไม่?');">
                                        <input type="hidden" name="action" value="cancel_booking">
                                        <input type="hidden" name="db_id" value="<?= $b['db_id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-secondary shadow-none text-danger border-danger">
                                            <i class="fa-solid fa-trash me-1"></i> ยกเลิก
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5 bg-white rounded-3 shadow-sm border">
                <i class="fa-solid fa-calendar-xmark text-muted fs-1 mb-2"></i>
                <h5 class="text-muted">คุณยังไม่มีรายการจองห้องพัก</h5>
                <p class="text-muted small">สามารถเลือกดูห้องพักว่างและทำรายการจองได้ทันที</p>
                <a href="<?= BASE_URL ?>/customer/rooms.php" class="btn btn-primary custom-bg btn-sm shadow-none mt-2">
                    <i class="fa-solid fa-magnifying-glass me-1"></i> ค้นหาห้องพัก
                </a>
            </div>
        <?php endif; ?>

    <!-- Tab 2: Food Orders -->
    <?php else: ?>
        <?php if (count($foodOrders) > 0): ?>
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>รหัสบิล</th>
                                <th>หมายเลขห้อง</th>
                                <th>วันที่รับอาหาร</th>
                                <th>ชุดหมูกระทะ</th>
                                <th>วัตถุดิบเพิ่มเติม</th>
                                <th>จำนวน</th>
                                <th>ราคารวม</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($foodOrders as $f): ?>
                                <tr>
                                    <td><span class="text-muted">#<?= $f['mnbill_id'] ?></span></td>
                                    <td><span class="badge bg-secondary">ห้อง <?= e($f['rm_id']) ?></span></td>
                                    <td class="fw-semibold text-primary"><?= thaiDate($f['receive']) ?></td>
                                    <td><?= !empty($f['mn_name']) ? e($f['mn_name']) . " (ไซส์ " . e($f['size'] ?? '') . ")" : '<span class="text-muted">-</span>' ?></td>
                                    <td><?= !empty($f['in_name']) ? e($f['in_name']) : '<span class="text-muted">-</span>' ?></td>
                                    <td><?= e($f['amount']) ?></td>
                                    <td class="fw-bold text-success"><?= formatPrice($f['total']) ?> บาท</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center py-5 bg-white rounded-3 shadow-sm border">
                <i class="fa-solid fa-utensils text-muted fs-1 mb-2"></i>
                <h5 class="text-muted">ยังไม่มีรายการสั่งอาหารหมูกระทะ</h5>
                <p class="text-muted small">สัมผัสความอร่อยริมสายน้ำ สั่งเซ็ตหมูกระทะและวัตถุดิบสดใหม่ล่วงหน้าได้เลย</p>
                <a href="<?= BASE_URL ?>/customer/menu.php" class="btn btn-warning btn-sm shadow-none mt-2">
                    <i class="fa-solid fa-cart-plus me-1"></i> สั่งหมูกระทะเลย
                </a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
