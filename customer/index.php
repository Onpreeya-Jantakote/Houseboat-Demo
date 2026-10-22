<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireCustomerLogin();

$pageTitle = "หน้าแรกสมาชิก";
$today = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));

// ดึงจำนวนรายการจองที่กำลังใช้งานของลูกค้า
$cus_name = $_SESSION['cus_name'];
$stmtBook = $pdo->prepare("SELECT COUNT(*) AS total FROM detailbooking WHERE cus_name = ?");
$stmtBook->execute([$cus_name]);
$totalBookings = $stmtBook->fetch()['total'] ?? 0;

$stmtFood = $pdo->prepare("SELECT COUNT(*) AS total FROM menubill WHERE cus_name = ?");
$stmtFood->execute([$cus_name]);
$totalFoodOrders = $stmtFood->fetch()['total'] ?? 0;

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-4">
    <!-- Welcome Banner -->
    <div class="p-4 p-md-5 mb-4 rounded-3 text-white shadow-sm" style="background: linear-gradient(135deg, #1a237e 0%, #283593 100%);">
        <div class="row align-items-center">
            <div class="col-md-8">
                <span class="badge bg-light text-primary mb-2 px-3 py-1 fw-bold">Customer Portal</span>
                <h2 class="fw-bold mb-2">ยินดีต้อนรับ, คุณ<?= e($_SESSION['cus_name']) ?>!</h2>
                <p class="mb-0 text-white-50">
                    จัดการการจองห้องพัก สั่งเซ็ตหมูกระทะริมน้ำ และตรวจสอบสถานะได้อย่างสะดวกรวดเร็ว
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="<?= BASE_URL ?>/customer/profile.php" class="btn btn-outline-light shadow-none btn-sm">
                    <i class="fa-solid fa-user-pen me-1"></i> แก้ไขข้อมูลส่วนตัว
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats & Actions -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 me-3 fs-3">
                        <i class="fa-solid fa-bed"></i>
                    </div>
                    <div>
                        <div class="text-muted small">การจองห้องพักของฉัน</div>
                        <h4 class="fw-bold mb-0 text-primary"><?= $totalBookings ?> รายการ</h4>
                        <a href="<?= BASE_URL ?>/customer/mybooking.php" class="small text-decoration-none">ดูรายละเอียด &raquo;</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 me-3 fs-3">
                        <i class="fa-solid fa-utensils"></i>
                    </div>
                    <div>
                        <div class="text-muted small">รายการสั่งอาหารหมูกระทะ</div>
                        <h4 class="fw-bold mb-0 text-success"><?= $totalFoodOrders ?> รายการ</h4>
                        <a href="<?= BASE_URL ?>/customer/mybooking.php?tab=food" class="small text-decoration-none">ดูรายละเอียด &raquo;</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-3 me-3 fs-3">
                        <i class="fa-solid fa-bowl-food"></i>
                    </div>
                    <div>
                        <div class="text-muted small">สั่งอาหารเพิ่ม</div>
                        <h5 class="fw-bold mb-0 text-dark">หมูกระทะ & วัตถุดิบ</h5>
                        <a href="<?= BASE_URL ?>/customer/menu.php" class="small text-decoration-none">เลือกเมนูอาหาร &raquo;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Availability Search -->
    <div class="card shadow-sm border-0 rounded-3 mb-5">
        <div class="card-body p-4">
            <h4 class="fw-bold mb-3" style="color: #1a237e;">
                <i class="fa-solid fa-calendar-days me-2"></i> ค้นหาห้องพักว่างสำหรับสมาชิก
            </h4>
            <form action="<?= BASE_URL ?>/customer/rooms.php" method="GET" class="row align-items-end g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold" for="check_in">วันที่เช็คอิน</label>
                    <input type="date" class="form-control shadow-none" name="check_in" id="check_in" min="<?= $today ?>" value="<?= $today ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold" for="check_out">วันที่เช็คเอาท์</label>
                    <input type="date" class="form-control shadow-none" name="check_out" id="check_out" min="<?= $tomorrow ?>" value="<?= $tomorrow ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold" for="amount">จำนวนผู้เข้าพัก</label>
                    <select class="form-select shadow-none" name="amount" id="amount">
                        <option value="1">1 ท่าน</option>
                        <option value="2" selected>2 ท่าน</option>
                        <option value="3">3 ท่าน</option>
                        <option value="4">4 ท่าน</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100 shadow-none custom-bg py-2">
                        <i class="fa-solid fa-search me-1"></i> ค้นหาห้องพัก
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Cover Carousel -->
    <div class="mb-4">
        <div class="swiper swiper-container rounded-3 shadow-sm overflow-hidden" style="max-height: 400px;">
            <div class="swiper-wrapper">
                <div class="swiper-slide"><img src="<?= BASE_URL ?>/images/cover/1.jpg" class="w-100 d-block" style="height: 400px; object-fit: cover;"/></div>
                <div class="swiper-slide"><img src="<?= BASE_URL ?>/images/cover/2.jpg" class="w-100 d-block" style="height: 400px; object-fit: cover;"/></div>
                <div class="swiper-slide"><img src="<?= BASE_URL ?>/images/cover/3.jpg" class="w-100 d-block" style="height: 400px; object-fit: cover;"/></div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
