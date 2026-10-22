<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = "กิจกรรมและสันทนาการ";

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold" style="color: #1a237e;">กิจกรรมและสันทนาการ</h2>
        <p class="text-muted">เติมเต็มวันหยุดของคุณด้วยกิจกรรมสนุกสนานท่ามกลางสายน้ำและธรรมชาติ</p>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-4">
        <!-- กิจกรรมที่ 1 -->
        <div class="col">
            <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden">
                <img src="https://www.chillpainai.com/src/wewakeup/scoop/img_scoop/scoop/kat/beautiful%20raft/500rai/11754915_841554609227662_3060870916074172922_o.jpg" 
                     class="card-img-top" style="height: 250px; object-fit: cover;" alt="พายเรือคายัค">
                <div class="card-body">
                    <h5 class="card-title fw-bold" style="color: #1a237e;">พายเรือคายัค (Kayak)</h5>
                    <p class="card-text text-muted small">
                        พายเรือชมทัศนียภาพอันงดงามรอบขุนเขาหินปูนและผืนน้ำใสสะอาด เพลิดเพลินกับความสงบอย่างแท้จริง
                    </p>
                </div>
                <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center py-3">
                    <span class="fw-bold text-success fs-5">500 บาท</span>
                    <span class="text-muted small">ต่อ 1 ชั่วโมง</span>
                </div>
            </div>
        </div>

        <!-- กิจกรรมที่ 2 -->
        <div class="col">
            <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden">
                <img src="https://แพกาญ.com/wp-content/uploads/2019/07/%E0%B9%81%E0%B8%9Echillsunset-12.jpg" 
                     class="card-img-top" style="height: 250px; object-fit: cover;" alt="เล่นน้ำที่แพ">
                <div class="card-body">
                    <h5 class="card-title fw-bold" style="color: #1a237e;">เล่นน้ำที่แพพัก</h5>
                    <p class="card-text text-muted small">
                        ผ่อนคลายกับเครื่องเล่นและโซนเล่นน้ำที่แพพัก พร้อมเสื้อชูชีพเพื่อความปลอดภัย เหมาะสำหรับครอบครัวและกลุ่มเพื่อน
                    </p>
                </div>
                <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center py-3">
                    <span class="fw-bold text-success fs-5">300 บาท</span>
                    <span class="text-muted small">ต่อ 1 ชั่วโมง</span>
                </div>
            </div>
        </div>

        <!-- กิจกรรมที่ 3 -->
        <div class="col">
            <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden">
                <img src="https://www.mylifemytravels.com/wp-content/uploads/2020/09/GOPR0017-1.jpg" 
                     class="card-img-top" style="height: 250px; object-fit: cover;" alt="ล่องแพ">
                <div class="card-body">
                    <h5 class="card-title fw-bold" style="color: #1a237e;">ล่องแพเปียกชมวิว</h5>
                    <p class="card-text text-muted small">
                        นั่งแพลากชมทัศนียภาพยามเย็น ดื่มด่ำกับลมเย็นสบายและแสงอาทิตย์อัสดงที่สะท้อนผิวน้ำ
                    </p>
                </div>
                <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center py-3">
                    <span class="fw-bold text-success fs-5">200 บาท</span>
                    <span class="text-muted small">ต่อ 30 นาที</span>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-5">
        <p class="text-muted small">* ท่านสามารถแจ้งความประสงค์เข้าร่วมกิจกรรมได้ที่เคาน์เตอร์บริการของแพพักเมื่อเช็คอิน *</p>
        <a href="<?= BASE_URL ?>/rooms.php" class="btn btn-primary custom-bg px-4 py-2 shadow-none">จองห้องพักเลย</a>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
