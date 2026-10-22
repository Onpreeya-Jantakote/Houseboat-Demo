<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = "เสร็จสิ้นการจองห้องพัก";

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <!-- Step Progress Bar -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-8">
            <div class="d-flex justify-content-between position-relative text-center">
                <div class="flex-fill">
                    <div class="badge rounded-pill bg-success p-2 mb-1" style="width: 32px; height: 32px;"><i class="fa-solid fa-check"></i></div>
                    <div class="small text-muted">กรอกข้อมูลส่วนตัว</div>
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
                        ระบบได้รับข้อมูลการจองและหลักฐานการชำระเงินมัดจำของท่านเรียบร้อยแล้ว<br>
                        เจ้าหน้าที่จะดำเนินการตรวจสอบความถูกต้องของสลิปการโอนเงินโดยเร็วที่สุด
                    </p>

                    <div class="alert alert-info py-2 px-3 small text-start mb-4">
                        <i class="fa-solid fa-circle-info me-1"></i> 
                        ท่านสามารถตรวจสอบสถานะการจองได้ตลอดเวลาที่หน้า <strong>"ตรวจสอบการจอง"</strong> โดยใช้ชื่อ-นามสกุลที่ใช้ทำการจอง
                    </div>

                    <div class="d-flex justify-content-center gap-2">
                        <a href="<?= BASE_URL ?>/booking.php" class="btn btn-primary custom-bg shadow-none px-4">
                            <i class="fa-solid fa-search me-1"></i> ตรวจสอบสถานะการจอง
                        </a>
                        <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline-secondary shadow-none px-4">
                            กลับสู่หน้าหลัก
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
