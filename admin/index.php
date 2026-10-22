<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdminLogin();

$pageTitle = "แผงควบคุมระบบ (Admin Dashboard)";

// สถิติรวมของระบบ
$totalRooms = $pdo->query("SELECT COUNT(*) FROM room")->fetchColumn();
$totalAnonBookings = $pdo->query("SELECT COUNT(*) FROM anonybooking")->fetchColumn();
$totalMemberBookings = $pdo->query("SELECT COUNT(*) FROM detailbooking")->fetchColumn();
$totalBookings = $totalAnonBookings + $totalMemberBookings;

// รายการที่รอตรวจสอบสลิป
$pendingAnon = $pdo->query("SELECT COUNT(*) FROM anonybooking WHERE status = 'รอดำเนินการ'")->fetchColumn();
$pendingMember = $pdo->query("SELECT COUNT(*) FROM detailbooking WHERE status = 'รอดำเนินการ'")->fetchColumn();
$totalPending = $pendingAnon + $pendingMember;

// ยอดสั่งอาหารหมูกระทะ
$totalFoodOrders = $pdo->query("SELECT COUNT(*) FROM menubill")->fetchColumn();
$totalFoodRevenue = $pdo->query("SELECT SUM(total) FROM menubill")->fetchColumn() ?: 0;

// รายการจองล่าสุด 5 รายการ
$recentBookings = $pdo->query("
    SELECT 'บุคคลทั่วไป' AS user_type, ab_id AS id, rm_id, am_name AS customer_name, am_tel AS tel, check_in, check_out, status, date_pay 
    FROM anonybooking
    UNION ALL
    SELECT 'ลูกค้าสมาชิก' AS user_type, db_id AS id, rm_id, cus_name AS customer_name, cus_tel AS tel, check_in, check_out, status, date_pay 
    FROM detailbooking
    ORDER BY date_pay DESC, id DESC
    LIMIT 6
")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-4">
    <!-- Admin Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1" style="color: #1a237e;">
                <i class="fa-solid fa-gauge-high me-2"></i> แผงควบคุมระบบ (Admin Dashboard)
            </h2>
            <p class="text-muted small">ยินดีต้อนรับ คุณ <strong><?= e($_SESSION['ad_name']) ?></strong> เข้าสู่ระบบจัดการหลังบ้าน House boat</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= BASE_URL ?>/admin/bookings.php" class="btn btn-primary custom-bg btn-sm shadow-none">
                <i class="fa-solid fa-list-check me-1"></i> ตรวจสอบการจอง
            </a>
            <a href="<?= BASE_URL ?>/admin/rooms.php" class="btn btn-outline-dark btn-sm shadow-none">
                <i class="fa-solid fa-bed me-1"></i> จัดการห้องพัก
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-3 h-100 border-start border-4 border-warning">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small fw-semibold">รอตรวจสอบสลิป</div>
                            <h3 class="fw-bold text-warning mb-0"><?= $totalPending ?> รายการ</h3>
                        </div>
                        <div class="fs-1 text-warning opacity-75"><i class="fa-solid fa-hourglass-half"></i></div>
                    </div>
                    <div class="mt-2 small">
                        <a href="<?= BASE_URL ?>/admin/bookings.php?filter=pending" class="text-decoration-none">ตรวจสอบตอนนี้ &raquo;</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-3 h-100 border-start border-4 border-primary">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small fw-semibold">การจองห้องพักทั้งหมด</div>
                            <h3 class="fw-bold text-primary mb-0"><?= $totalBookings ?> รายการ</h3>
                        </div>
                        <div class="fs-1 text-primary opacity-75"><i class="fa-solid fa-calendar-check"></i></div>
                    </div>
                    <div class="mt-2 small text-muted">
                        สมาชิก <?= $totalMemberBookings ?> | ทั่วไป <?= $totalAnonBookings ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-3 h-100 border-start border-4 border-success">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small fw-semibold">ออเดอร์หมูกระทะ</div>
                            <h3 class="fw-bold text-success mb-0"><?= $totalFoodOrders ?> บิล</h3>
                        </div>
                        <div class="fs-1 text-success opacity-75"><i class="fa-solid fa-utensils"></i></div>
                    </div>
                    <div class="mt-2 small text-muted">
                        ยอดรวม: <?= formatPrice($totalFoodRevenue) ?> บาท
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-3 h-100 border-start border-4 border-info">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small fw-semibold">ห้องพักในระบบ</div>
                            <h3 class="fw-bold text-info mb-0"><?= $totalRooms ?> ห้อง</h3>
                        </div>
                        <div class="fs-1 text-info opacity-75"><i class="fa-solid fa-bed"></i></div>
                    </div>
                    <div class="mt-2 small">
                        <a href="<?= BASE_URL ?>/admin/rooms.php" class="text-decoration-none">จัดการห้องพัก &raquo;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings Table -->
    <div class="card shadow-sm border-0 rounded-3 overflow-hidden mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0" style="color: #1a237e;">
                <i class="fa-solid fa-clock-rotate-left me-1"></i> รายการจองห้องพักล่าสุด
            </h5>
            <a href="<?= BASE_URL ?>/admin/bookings.php" class="btn btn-sm btn-outline-primary shadow-none">ดูทั้งหมด</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ประเภท</th>
                            <th>หมายเลขห้อง</th>
                            <th>ชื่อผู้เข้าพัก</th>
                            <th>เบอร์โทร</th>
                            <th>เช็คอิน</th>
                            <th>เช็คเอาท์</th>
                            <th>สถานะ</th>
                            <th class="text-end">การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($recentBookings) > 0): ?>
                            <?php foreach ($recentBookings as $b): ?>
                                <tr>
                                    <td>
                                        <span class="badge <?= ($b['user_type'] === 'ลูกค้าสมาชิก') ? 'bg-info text-dark' : 'bg-secondary' ?>">
                                            <?= e($b['user_type']) ?>
                                        </span>
                                    </td>
                                    <td><span class="badge bg-primary">ห้อง <?= e($b['rm_id']) ?></span></td>
                                    <td class="fw-semibold"><?= e($b['customer_name']) ?></td>
                                    <td><?= e($b['tel']) ?></td>
                                    <td><?= thaiDate($b['check_in']) ?></td>
                                    <td><?= thaiDate($b['check_out']) ?></td>
                                    <td>
                                        <?php if ($b['status'] === 'ยืนยันการจอง'): ?>
                                            <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i> ยืนยันการจอง</span>
                                        <?php elseif ($b['status'] === 'รอดำเนินการ'): ?>
                                            <span class="badge bg-warning text-dark"><i class="fa-solid fa-clock me-1"></i> รอดำเนินการ</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?= e($b['status'] ?: 'ยังไม่ชำระมัดจำ') ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="<?= BASE_URL ?>/admin/bookings.php?search=<?= urlencode($b['customer_name']) ?>" class="btn btn-sm btn-outline-dark shadow-none">
                                            ดูข้อมูล
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">ยังไม่มีรายการจองห้องพัก</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
