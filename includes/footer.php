<?php
/**
 * Common Footer Template
 */
?>
    <!-- Footer Section -->
    <footer class="bg-white text-center text-lg-start border-top mt-5">
        <div class="container p-4">
            <div class="row">
                <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
                    <h5 class="text-uppercase fw-bold" style="color: #1a237e;"><?= SITE_NAME ?></h5>
                    <p class="text-muted">
                        ที่พักเรือนแพท่ามกลางธรรมชาติ สายน้ำ และขุนเขา หลีกหนีความวุ่นวายจากโลกภายนอก ให้ธรรมชาติได้บำบัด พื้นที่สีเขียว พร้อมกิจกรรมพายเรือคายัค ล่องแพ และเซ็ตหมูกระทะแสนอร่อย
                    </p>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-uppercase fw-bold mb-3">ลิงก์ด่วน</h5>
                    <ul class="list-unstyled mb-0">
                        <li><a href="<?= BASE_URL ?>/index.php" class="text-decoration-none text-secondary">หน้าหลัก</a></li>
                        <li><a href="<?= BASE_URL ?>/rooms.php" class="text-decoration-none text-secondary">ห้องพัก</a></li>
                        <li><a href="<?= BASE_URL ?>/activity.php" class="text-decoration-none text-secondary">กิจกรรม</a></li>
                        <li><a href="<?= BASE_URL ?>/booking.php" class="text-decoration-none text-secondary">ตรวจสอบการจอง</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-uppercase fw-bold mb-3">ติดต่อเรา</h5>
                    <p class="text-muted mb-1"><i class="fa-solid fa-phone me-2"></i> 069-875-4463</p>
                    <p class="text-muted mb-1"><i class="fa-solid fa-envelope me-2"></i> contact@houseboat.com</p>
                    <p class="text-muted"><i class="fa-solid fa-location-dot me-2"></i> เขื่อนเชี่ยวหลาน สุราษฎร์ธานี</p>
                </div>
            </div>
        </div>
        <div class="text-center p-3 text-muted" style="background-color: rgba(0, 0, 0, 0.03);">
            &copy; <?= date('Y') ?> <?= SITE_NAME ?>. สงวนลิขสิทธิ์ทั้งหมด.
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        // เริ่มต้น Swiper สำหรับแบนเนอร์ถ้ามีในหน้า
        if (document.querySelector('.swiper-container')) {
            var swiper = new Swiper(".swiper-container", {
                spaceBetween: 30,
                effect: "fade",
                loop: true,
                autoplay: {
                    delay: 3500,
                    disableOnInteraction: false,
                }
            });
        }
    </script>
</body>
</html>
