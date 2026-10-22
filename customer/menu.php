<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireCustomerLogin();

$pageTitle = "สั่งอาหารชุดหมูกระทะและวัตถุดิบ";

// จัดการการเพิ่มรายการลงตะกร้า
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $amount = max(1, intval($_POST['amount'] ?? 1));

    if (isset($_POST['mn_id'])) {
        $mn_id = intval($_POST['mn_id']);
        $_SESSION['cart'][] = [
            'type'   => 'menu',
            'mn_id'  => $mn_id,
            'amount' => $amount
        ];
        setFlashMessage('success', 'เพิ่มชุดหมูกระทะลงตะกร้าเรียบร้อยแล้ว');
    } elseif (isset($_POST['in_id'])) {
        $in_id = intval($_POST['in_id']);
        $_SESSION['cart'][] = [
            'type'   => 'ingredient',
            'in_id'  => $in_id,
            'amount' => $amount
        ];
        setFlashMessage('success', 'เพิ่มวัตถุดิบลงตะกร้าเรียบร้อยแล้ว');
    }

    header("Location: " . BASE_URL . "/customer/menu.php");
    exit();
}

// ดึงรายการชุดหมูกระทะ
$menus = $pdo->query("SELECT * FROM menu ORDER BY price ASC")->fetchAll();

// ดึงรายการวัตถุดิบเพิ่มเติม
$ingredients = $pdo->query("SELECT * FROM ingredients ORDER BY in_id ASC")->fetchAll();

$cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1" style="color: #1a237e;">ชุดหมูกระทะ & วัตถุดิบเพิ่มเติม</h2>
            <p class="text-muted small">เลือกชุดหมูกระทะสุดคุ้ม หรือสั่งวัตถุดิบเพิ่มเติมสำหรับปิ้งย่างริมแพ</p>
        </div>
        <a href="<?= BASE_URL ?>/customer/cart.php" class="btn btn-warning shadow-none position-relative px-3 py-2">
            <i class="fa-solid fa-cart-shopping me-1"></i> ตะกร้าของฉัน
            <?php if ($cartCount > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    <?= $cartCount ?>
                </span>
            <?php endif; ?>
        </a>
    </div>

    <!-- Section 1: Set Menus -->
    <h4 class="fw-bold mb-3 border-start border-4 border-primary ps-2" style="color: #1a237e;">
        เซ็ตหมูกระทะสุดคุ้ม
    </h4>
    <div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
        <?php foreach ($menus as $m): ?>
            <div class="col">
                <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden">
                    <img src="<?= BASE_URL ?>/images/cover/4.jpg" class="card-img-top room-img" alt="<?= e($m['mn_name']) ?>">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title fw-bold text-primary mb-0" style="color: #1a237e !important;">
                                <?= e($m['mn_name']) ?>
                            </h5>
                            <span class="badge bg-danger fs-6">ไซส์ <?= e($m['size']) ?></span>
                        </div>

                        <p class="card-text text-muted small flex-grow-1" style="white-space: pre-line;">
                            <?= e($m['note']) ?>
                        </p>

                        <div class="mt-3 pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-4 fw-bold text-success"><?= formatPrice($m['price']) ?></span>
                                <span class="text-muted small">บาท / เซ็ต</span>
                            </div>

                            <form action="<?= BASE_URL ?>/customer/menu.php" method="POST" class="d-flex gap-2">
                                <input type="hidden" name="mn_id" value="<?= $m['mn_id'] ?>">
                                <select name="amount" class="form-select form-select-sm w-50" style="max-width: 90px;">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <option value="<?= $i ?>"><?= $i ?> ชุด</option>
                                    <?php endfor; ?>
                                </select>
                                <button type="submit" class="btn btn-primary custom-bg btn-sm flex-grow-1 shadow-none">
                                    <i class="fa-solid fa-cart-plus me-1"></i> เลือกใส่ตะกร้า
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Section 2: Extra Ingredients -->
    <h4 class="fw-bold mb-3 border-start border-4 border-warning ps-2" style="color: #1a237e;">
        วัตถุดิบและของทานเล่นเพิ่มเติม
    </h4>
    <div class="row row-cols-2 row-cols-md-4 g-3 mb-4">
        <?php foreach ($ingredients as $in): ?>
            <?php 
                $imgSrc = getImageSrc($in['in_picture'], BASE_URL . '/images/cover/5.jpg');
            ?>
            <div class="col">
                <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden">
                    <img src="<?= $imgSrc ?>" class="card-img-top" style="height: 140px; object-fit: cover;" alt="<?= e($in['in_name']) ?>">
                    <div class="card-body p-3 d-flex flex-column">
                        <h6 class="card-title fw-bold mb-1"><?= e($in['in_name']) ?></h6>
                        <div class="text-muted small mb-2">จำนวน: <?= e($in['in_unit']) ?> หน่วย</div>

                        <div class="mt-auto">
                            <div class="fw-bold text-success mb-2"><?= formatPrice($in['in_price']) ?> บาท</div>
                            <form action="<?= BASE_URL ?>/customer/menu.php" method="POST" class="d-flex gap-1">
                                <input type="hidden" name="in_id" value="<?= $in['in_id'] ?>">
                                <select name="amount" class="form-select form-select-sm" style="max-width: 70px;">
                                    <?php for ($i = 1; $i <= 10; $i++): ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                                <button type="submit" class="btn btn-outline-warning text-dark btn-sm flex-grow-1 shadow-none">
                                    <i class="fa-solid fa-plus"></i> เพิ่ม
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
