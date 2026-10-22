<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = "ห้องพักทั้งหมด";

$check_in  = trim($_GET['check_in'] ?? '');
$check_out = trim($_GET['check_out'] ?? '');
$amount    = trim($_GET['amount'] ?? '2');

// หากมีการค้นหาตามช่วงวันที่ ให้ค้นหาเฉพาะห้องที่ยังว่าง
if (!empty($check_in) && !empty($check_out)) {
    $pageTitle = "ผลการค้นหาห้องพักว่าง";
    $sql = "SELECT room.rm_id, typeroom.tr_id, typeroom.tr_name, room.description, room.price, room.rm_pic
            FROM room 
            INNER JOIN typeroom ON room.tr_id = typeroom.tr_id 
            WHERE room.rm_id NOT IN (
                SELECT rm_id FROM detailbooking 
                WHERE NOT (check_out <= ? OR check_in >= ?)
                UNION
                SELECT rm_id FROM anonybooking 
                WHERE NOT (check_out <= ? OR check_in >= ?)
            )
            ORDER BY room.rm_id ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$check_in, $check_out, $check_in, $check_out]);
    $rooms = $stmt->fetchAll();
    $isSearching = true;
} else {
    // แสดงห้องพักทั้งหมด
    $sql = "SELECT room.rm_id, typeroom.tr_id, typeroom.tr_name, room.description, room.price, room.rm_pic
            FROM room 
            INNER JOIN typeroom ON room.tr_id = typeroom.tr_id 
            ORDER BY room.rm_id ASC";
    $stmt = $pdo->query($sql);
    $rooms = $stmt->fetchAll();
    $isSearching = false;
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-4">
    <!-- Search Form Bar -->
    <div class="bg-white p-3 rounded-3 shadow-sm border mb-4">
        <form action="<?= BASE_URL ?>/rooms.php" method="GET" class="row align-items-end g-2">
            <div class="col-md-3">
                <label class="form-label small fw-semibold" for="check_in">วันที่เช็คอิน</label>
                <input type="date" class="form-control form-control-sm" name="check_in" id="check_in" 
                       value="<?= e($check_in ?: date('Y-m-d')) ?>" min="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold" for="check_out">วันที่เช็คเอาท์</label>
                <input type="date" class="form-control form-control-sm" name="check_out" id="check_out" 
                       value="<?= e($check_out ?: date('Y-m-d', strtotime('+1 day'))) ?>" min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold" for="amount">จำนวนผู้เข้าพัก</label>
                <select class="form-select form-select-sm" name="amount" id="amount">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <option value="<?= $i ?>" <?= ($amount == $i) ? 'selected' : '' ?>><?= $i ?> ท่าน</option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-sm w-100 custom-bg shadow-none">
                    <i class="fa-solid fa-search me-1"></i> ค้นหาห้องว่าง
                </button>
            </div>
        </form>
    </div>

    <!-- Room List Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="color: #1a237e;">
                <?= $isSearching ? 'ห้องพักที่ว่างตามช่วงวันที่ระบุ' : 'ห้องพักทั้งหมดของเรา' ?>
            </h3>
            <?php if ($isSearching): ?>
                <p class="text-muted small mb-0">
                    เช็คอิน: <strong><?= thaiDate($check_in) ?></strong> | 
                    เช็คเอาท์: <strong><?= thaiDate($check_out) ?></strong> | 
                    ผู้เข้าพัก: <strong><?= e($amount) ?> ท่าน</strong>
                </p>
            <?php endif; ?>
        </div>
        <span class="badge bg-primary fs-6 px-3 py-2"><?= count($rooms) ?> ห้อง</span>
    </div>

    <!-- Room Grid -->
    <?php if (count($rooms) > 0): ?>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($rooms as $room): ?>
                <?php 
                    $fallbackImage = BASE_URL . "/images/rooms/" . $room["rm_id"] . ".jpg";
                    $imgSrc = getImageSrc($room["rm_pic"], $fallbackImage);
                    $bookParams = http_build_query([
                        'rm_id'     => $room['rm_id'],
                        'check_in'  => $check_in ?: date('Y-m-d'),
                        'check_out' => $check_out ?: date('Y-m-d', strtotime('+1 day')),
                        'amount'    => $amount
                    ]);
                    $bookUrl = isCustomerLoggedIn() 
                        ? BASE_URL . "/customer/booking-room.php?" . $bookParams 
                        : BASE_URL . "/booking-room.php?" . $bookParams;
                ?>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden">
                        <img src="<?= $imgSrc ?>" class="card-img-top room-img" alt="ห้อง <?= e($room["rm_id"]) ?>" onerror="this.src='<?= BASE_URL ?>/images/cover/1.jpg';">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title fw-bold text-primary mb-0" style="color: #1a237e !important;">
                                    ห้อง <?= e($room["rm_id"]) ?>
                                </h5>
                                <span class="badge bg-light text-dark border"><?= e($room["tr_name"]) ?></span>
                            </div>
                            
                            <p class="card-text text-muted small flex-grow-1" style="white-space: pre-line;">
                                <?= e($room["description"]) ?>
                            </p>
                            
                            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                <div>
                                    <span class="fs-5 fw-bold text-success"><?= formatPrice($room["price"]) ?></span>
                                    <span class="text-muted small"> บาท/คืน</span>
                                </div>
                                <a href="<?= $bookUrl ?>" class="btn btn-outline-dark btn-sm shadow-none">
                                    <i class="fa-solid fa-calendar-check me-1"></i> จองห้องพัก
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5 bg-white rounded-3 shadow-sm border my-4">
            <i class="fa-solid fa-bed text-muted fs-1 mb-3"></i>
            <h4 class="text-muted">ไม่พบห้องพักที่ว่างตามเงื่อนไขที่ระบุ</h4>
            <p class="text-muted small">กรุณาลองเปลี่ยนวันที่เช็คอินหรือเช็คเอาท์ใหม่อีกครั้ง</p>
            <a href="<?= BASE_URL ?>/rooms.php" class="btn btn-primary custom-bg btn-sm shadow-none mt-2">ดูห้องพักทั้งหมด</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
