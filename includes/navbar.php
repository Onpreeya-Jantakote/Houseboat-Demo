<?php
/**
 * Navigation Bar Template
 * ปรับเปลี่ยนเมนูตามสถานะ: ผู้ใช้ทั่วไป, ลูกค้าสมาชิก, ผู้ดูแลระบบ
 */
$currentPage = basename($_SERVER['PHP_SELF'] ?? '');
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white px-lg-3 py-lg-2 shadow-sm sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL ?>/index.php">
            <img src="<?= BASE_URL ?>/images/logo/logo.png" alt="logo" class="me-2" style="height: 38px;">
            <span class="fw-bold fs-4 text-primary" style="color: #1a237e !important;"><?= SITE_NAME ?></span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if (isAdminLoggedIn()): ?>
                    <!-- เมนูสำหรับแอดมิน -->
                    <li class="nav-item me-2">
                        <a class="nav-link <?= ($currentPage == 'index.php') ? 'active fw-bold' : '' ?>" href="<?= BASE_URL ?>/admin/index.php">แดชบอร์ด</a>
                    </li>
                    <li class="nav-item me-2">
                        <a class="nav-link <?= ($currentPage == 'bookings.php') ? 'active fw-bold' : '' ?>" href="<?= BASE_URL ?>/admin/bookings.php">ข้อมูลผู้เข้าพัก</a>
                    </li>
                    <li class="nav-item me-2">
                        <a class="nav-link <?= ($currentPage == 'food-orders.php') ? 'active fw-bold' : '' ?>" href="<?= BASE_URL ?>/admin/food-orders.php">รายการสั่งอาหาร</a>
                    </li>
                    <li class="nav-item me-2">
                        <a class="nav-link <?= ($currentPage == 'rooms.php') ? 'active fw-bold' : '' ?>" href="<?= BASE_URL ?>/admin/rooms.php">จัดการห้องพัก</a>
                    </li>
                    <li class="nav-item me-2">
                        <a class="nav-link <?= ($currentPage == 'ingredients.php') ? 'active fw-bold' : '' ?>" href="<?= BASE_URL ?>/admin/ingredients.php">จัดการวัตถุดิบ</a>
                    </li>
                <?php elseif (isCustomerLoggedIn()): ?>
                    <!-- เมนูสำหรับลูกค้าสมาชิก -->
                    <li class="nav-item me-2">
                        <a class="nav-link <?= ($currentPage == 'index.php') ? 'active fw-bold' : '' ?>" href="<?= BASE_URL ?>/customer/index.php">หน้าหลัก</a>
                    </li>
                    <li class="nav-item me-2">
                        <a class="nav-link <?= ($currentPage == 'rooms.php') ? 'active fw-bold' : '' ?>" href="<?= BASE_URL ?>/customer/rooms.php">ห้องพัก</a>
                    </li>
                    <li class="nav-item me-2">
                        <a class="nav-link <?= ($currentPage == 'activity.php') ? 'active fw-bold' : '' ?>" href="<?= BASE_URL ?>/customer/activity.php">กิจกรรม</a>
                    </li>
                    <li class="nav-item me-2">
                        <a class="nav-link <?= ($currentPage == 'mybooking.php') ? 'active fw-bold' : '' ?>" href="<?= BASE_URL ?>/customer/mybooking.php">การจองของฉัน</a>
                    </li>
                    <li class="nav-item me-2">
                        <a class="nav-link <?= ($currentPage == 'menu.php' || $currentPage == 'cart.php') ? 'active fw-bold' : '' ?>" href="<?= BASE_URL ?>/customer/menu.php">สั่งอาหารหมูกระทะ</a>
                    </li>
                <?php else: ?>
                    <!-- เมนูสำหรับบุคคลทั่วไป -->
                    <li class="nav-item me-2">
                        <a class="nav-link <?= ($currentPage == 'index.php') ? 'active fw-bold' : '' ?>" href="<?= BASE_URL ?>/index.php">หน้าหลัก</a>
                    </li>
                    <li class="nav-item me-2">
                        <a class="nav-link <?= ($currentPage == 'rooms.php') ? 'active fw-bold' : '' ?>" href="<?= BASE_URL ?>/rooms.php">ห้องพัก</a>
                    </li>
                    <li class="nav-item me-2">
                        <a class="nav-link <?= ($currentPage == 'activity.php') ? 'active fw-bold' : '' ?>" href="<?= BASE_URL ?>/activity.php">กิจกรรม</a>
                    </li>
                    <li class="nav-item me-2">
                        <a class="nav-link <?= ($currentPage == 'booking.php') ? 'active fw-bold' : '' ?>" href="<?= BASE_URL ?>/booking.php">ตรวจสอบการจอง</a>
                    </li>
                <?php endif; ?>
            </ul>

            <div class="d-flex align-items-center">
                <?php if (isAdminLoggedIn()): ?>
                    <span class="me-3 text-secondary"><i class="fa-solid fa-user-shield me-1"></i> แอดมิน: <strong><?= e($_SESSION['ad_name'] ?? $_SESSION['ad_user']) ?></strong></span>
                    <a href="<?= BASE_URL ?>/logout.php" class="btn btn-outline-danger btn-sm shadow-none">ออกจากระบบ</a>
                <?php elseif (isCustomerLoggedIn()): ?>
                    <a href="<?= BASE_URL ?>/customer/profile.php" class="btn btn-outline-secondary btn-sm me-2 shadow-none d-flex align-items-center">
                        <img src="<?= BASE_URL ?>/images/logo/user.png" alt="user" style="height: 20px;" class="me-1">
                        <span><?= e($_SESSION['cus_name'] ?? $_SESSION['cus_user']) ?></span>
                    </a>
                    <a href="<?= BASE_URL ?>/logout.php" class="btn btn-outline-danger btn-sm shadow-none">ออกจากระบบ</a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/login.php" class="btn btn-outline-dark shadow-none me-2">เข้าสู่ระบบ</a>
                    <a href="<?= BASE_URL ?>/register.php" class="btn btn-primary shadow-none custom-bg">สมัครสมาชิก</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
