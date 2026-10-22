<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = "หน้าหลัก - พักผ่อนท่ามกลางธรรมชาติสายน้ำ";
$today = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));

require_once __DIR__ . '/includes/header.php';
?>

<!-- Cover Carousel -->
<div class="container-fluid px-lg-4 mt-3">
    <div class="swiper swiper-container rounded-3 shadow-sm overflow-hidden" style="max-height: 520px;">
        <div class="swiper-wrapper">
            <div class="swiper-slide"><img src="<?= BASE_URL ?>/images/cover/1.jpg" class="w-100 d-block" style="height: 520px; object-fit: cover;"/></div>
            <div class="swiper-slide"><img src="<?= BASE_URL ?>/images/cover/2.jpg" class="w-100 d-block" style="height: 520px; object-fit: cover;"/></div>
            <div class="swiper-slide"><img src="<?= BASE_URL ?>/images/cover/3.jpg" class="w-100 d-block" style="height: 520px; object-fit: cover;"/></div>
            <div class="swiper-slide"><img src="<?= BASE_URL ?>/images/cover/4.jpg" class="w-100 d-block" style="height: 520px; object-fit: cover;"/></div>
            <div class="swiper-slide"><img src="<?= BASE_URL ?>/images/cover/5.jpg" class="w-100 d-block" style="height: 520px; object-fit: cover;"/></div>
            <div class="swiper-slide"><img src="<?= BASE_URL ?>/images/cover/6.jpg" class="w-100 d-block" style="height: 520px; object-fit: cover;"/></div>
        </div>
    </div>
</div>

<!-- Check Availability Form -->
<div class="container availability-form mx-auto">
    <div class="row justify-content-center">
        <div class="col-lg-10 bg-white shadow p-4 rounded-3 border">
            <h5 class="mb-3 fw-bold" style="color: #1a237e;"><i class="fa-solid fa-magnifying-glass me-2"></i> ค้นหาห้องพักว่าง</h5>
            <form action="<?= BASE_URL ?>/rooms.php" method="GET">
                <div class="row align-items-end g-3">
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
                        <button type="submit" class="btn text-white w-100 shadow-none custom-bg py-2">
                            <i class="fa-solid fa-search me-1"></i> ค้นหาห้องพัก
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- About Section -->
<div class="container mt-5 pt-3">
    <div class="row align-items-center mb-5">
        <div class="col-lg-6">
            <h2 class="fw-bold mb-3" style="color: #1a237e;">ยินดีต้อนรับสู่ House boat</h2>
            <p class="text-muted leading-relaxed">
                สัมผัสประสบการณ์การพักผ่อนบนแพพักลอยน้ำท่ามกลางธรรมชาติอันเงียบสงบ หลีกหนีความวุ่นวายมาสูดอากาศบริสุทธิ์ริมผืนน้ำใส 
                พร้อมสิ่งอำนวยความสะดวกครบครัน กิจกรรมสนุกสนานทางน้ำ และบริการเซ็ตอาหารหมูกระทะยามเย็นสุดฟิน
            </p>
            <div class="d-flex gap-2 mt-4">
                <a href="<?= BASE_URL ?>/rooms.php" class="btn btn-primary custom-bg shadow-none px-4 py-2">ดูห้องพักทั้งหมด</a>
                <a href="<?= BASE_URL ?>/activity.php" class="btn btn-outline-secondary shadow-none px-4 py-2">ดูกิจกรรม</a>
            </div>
        </div>
        <div class="col-lg-6 mt-4 mt-lg-0">
            <div class="row g-2">
                <div class="col-6">
                    <img src="<?= BASE_URL ?>/images/rooms/A1.jpg" class="img-fluid rounded shadow-sm w-100" style="height: 180px; object-fit: cover;" alt="room">
                </div>
                <div class="col-6">
                    <img src="<?= BASE_URL ?>/images/rooms/A0.jpg" class="img-fluid rounded shadow-sm w-100" style="height: 180px; object-fit: cover;" alt="room">
                </div>
                <div class="col-12">
                    <img src="<?= BASE_URL ?>/images/cover/3.jpg" class="img-fluid rounded shadow-sm w-100" style="height: 180px; object-fit: cover;" alt="scenery">
                </div>
            </div>
        </div>
    </div>

    <!-- Features / Highlights -->
    <div class="p-4 p-md-5 my-5 bg-white border rounded-3 shadow-sm">
        <h3 class="text-center fw-bold mb-4" style="color: #1a237e;">จุดเด่นและบริการของเรา</h3>
        <div class="row text-center g-4">
            <div class="col-md-4">
                <div class="p-3">
                    <div class="fs-1 text-success mb-3"><i class="fa-solid fa-leaf"></i></div>
                    <h5 class="fw-bold">ธรรมชาติบำบัด</h5>
                    <p class="text-muted">หลีกหนีความวุ่นวายจากโลกภายนอก ให้ธรรมชาติและสายน้ำได้บำบัด พร้อมพื้นที่สีเขียวสบายตา</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3">
                    <div class="fs-1 text-primary mb-3"><i class="fa-solid fa-camera-retro"></i></div>
                    <h5 class="fw-bold">ไฮไลท์และอาหารรสเลิศ</h5>
                    <p class="text-muted">มุมถ่ายรูปสวยงามหลายโซน พร้อมบริการชุดหมูกระทะริมน้ำ วัตถุดิบสดใหม่ ให้คุณอิ่มอร่อยอย่างจุใจ</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3">
                    <div class="fs-1 text-warning mb-3"><i class="fa-solid fa-headset"></i></div>
                    <h5 class="fw-bold">บริการเป็นกันเอง</h5>
                    <p class="text-muted">ดูแลลูกค้าอย่างอบอุ่น ติดต่อสอบถามได้ง่ายดายทั้งทาง Facebook, Line และโทรศัพท์</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Section -->
    <div class="my-5">
        <h3 class="fw-bold mb-3" style="color: #1a237e;"><i class="fa-solid fa-map-location-dot me-2"></i> แผนที่และการเดินทาง</h3>
        <div class="rounded-3 shadow-sm overflow-hidden border">
            <div class="map-container" style="height: 420px; padding-bottom: 0;">
                <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15760.26319552153!2d98.631874!3d9.057763!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30512d1171885e57%3A0xe1464a0720e523c4!2s500Rai%20Floating%20Resort!5e0!3m2!1sen!2sth!4v1708417721866!5m2!1sen!2sth" 
                    width="100%" height="420" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
