<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireCustomerLogin();

$pageTitle = "เสร็จสิ้นการจองห้องพัก";

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-5">
    <!-- Step Progress Bar -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-8">
            <div class="d-flex justify-content-between position-relative text-center">
                <div class="flex-fill">
                    <div class="badge rounded-pill bg-success p-2 mb-1" style="width: 32px; height: 32px;"><i class="fa-solid fa-check"></i></div>
                    <div class="small text-muted">ข้อมูลการจอง</div>
                </div>
                <div class="flex-fill">
                    <div class="badge rounded-pill bg-success p-2 mb-1" style="width: 32px; height: 32px;"><i class="fa-solid fa-check"></i></div>
                    <div class="small text-muted">ชำระเงินค่ามัดจำ</div>
                </div>
                <div class="flex-fill">
                    <div class="badge rounded-pill bg-danger p-2 mb-1" style="width: 32px; height: 32px;">3</div>
                    <div class="small fw-bold text-danger">เสร็จสิ้น</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-3 text-center p-4">
                <div class="card-body">
                    <div class="text-success fs-1 mb-3">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <h3 class="fw-bold mb-3" style="color: #1a237e;">การจองห้องพักเสร็จสิ้น!</h3>
                    <p class="text-muted leading-relaxed mb-4">
                        ระบบได้รับข้อมูลการจองและหลักฐานการชำระเงินมัดจำของคุณแล้ว<br>
                        เจ้าหน้าที่จะดำเนินการตรวจสอบความถูกต้องของสลิปการโอนเงินโดยเร็วที่สุด
                    </p>

                    <div class="alert alert-info py-2 px-3 small text-start mb-4">
                        <i class="fa-solid fa-circle-info me-1"></i> 
                        คุณสามารถตรวจสอบสถานะการจอง หรือสั่งชุดหมูกระทะเพิ่มเติมได้ที่หน้า <strong>"การจองของฉัน"</strong>
                    </div>

                    <div class="d-flex justify-content-center gap-2">
                        <a href="<?= BASE_URL ?>/customer/mybooking.php" class="btn btn-primary custom-bg shadow-none px-4">
                            <i class="fa-solid fa-list-check me-1"></i> ดูการจองของฉัน
                        </a>
                        <a href="<?= BASE_URL ?>/customer/menu.php" class="btn btn-warning shadow-none px-4">
                            <i class="fa-solid fa-utensils me-1"></i> สั่งหมูกระทะเพิ่ม
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
