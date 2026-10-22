<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireCustomerLogin();

$pageTitle = "ตะกร้าอาหารหมูกระทะ";
$cus_name = $_SESSION['cus_name'];

// ลบรายการในตะกร้า
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['index'])) {
    $delIdx = intval($_GET['index']);
    if (isset($_SESSION['cart'][$delIdx])) {
        unset($_SESSION['cart'][$delIdx]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // รีเซ็ต index
        setFlashMessage('success', 'ลบรายการออกจากตะกร้าแล้ว');
    }
    header("Location: " . BASE_URL . "/customer/cart.php");
    exit();
}

// ล้างตะกร้าทั้งหมด
if (isset($_GET['action']) && $_GET['action'] === 'clear') {
    unset($_SESSION['cart']);
    setFlashMessage('success', 'ล้างตะกร้าสินค้าเรียบร้อยแล้ว');
    header("Location: " . BASE_URL . "/customer/cart.php");
    exit();
}

// ดึงห้องพักที่ลูกค้ามีการจองอยู่
$stmtRooms = $pdo->prepare("SELECT rm_id, check_in, check_out FROM detailbooking WHERE cus_name = ? ORDER BY db_id DESC");
$stmtRooms->execute([$cus_name]);
$customerBookings = $stmtRooms->fetchAll();

// ประมวลผลการยืนยันสั่งอาหาร
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'confirm_order') {
    $rm_id   = trim($_POST['rm_id'] ?? '');
    $receive = trim($_POST['receive'] ?? '');

    if (empty($rm_id)) {
        setFlashMessage('error', 'กรุณาเลือกห้องพักที่ต้องการรับอาหาร');
    } elseif (empty($receive)) {
        setFlashMessage('error', 'กรุณาเลือกวันที่ต้องการรับอาหาร');
    } elseif (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        setFlashMessage('error', 'ไม่มีรายการสินค้าในตะกร้า');
    } else {
        $insertStmt = $pdo->prepare("INSERT INTO menubill (mn_id, in_id, rm_id, cus_name, amount, total, receive) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        foreach ($_SESSION['cart'] as $item) {
            $amount = intval($item['amount']);
            $mn_id = 0;
            $in_id = 0;
            $itemTotal = 0;

            if ($item['type'] === 'menu') {
                $mn_id = intval($item['mn_id']);
                $mQuery = $pdo->prepare("SELECT price FROM menu WHERE mn_id = ?");
                $mQuery->execute([$mn_id]);
                $price = $mQuery->fetchColumn() ?: 0;
                $itemTotal = $price * $amount;
            } elseif ($item['type'] === 'ingredient') {
                $in_id = intval($item['in_id']);
                $iQuery = $pdo->prepare("SELECT in_price FROM ingredients WHERE in_id = ?");
                $iQuery->execute([$in_id]);
                $price = $iQuery->fetchColumn() ?: 0;
                $itemTotal = $price * $amount;
            }

            $insertStmt->execute([$mn_id, $in_id, $rm_id, $cus_name, $amount, $itemTotal, $receive]);
        }

        unset($_SESSION['cart']);
        setFlashMessage('success', 'บันทึกการสั่งอาหารหมูกระทะเรียบร้อยแล้ว! เจ้าหน้าที่จะจัดเตรียมตามวันที่คุณระบุ');
        header("Location: " . BASE_URL . "/customer/mybooking.php?tab=food");
        exit();
    }
}

// คำนวณรายการในตะกร้า
$cartItems = [];
$grandTotal = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $idx => $item) {
        if ($item['type'] === 'menu') {
            $mStmt = $pdo->prepare("SELECT mn_name, size, price FROM menu WHERE mn_id = ?");
            $mStmt->execute([$item['mn_id']]);
            $menuData = $mStmt->fetch();
            if ($menuData) {
                $subtotal = $menuData['price'] * $item['amount'];
                $grandTotal += $subtotal;
                $cartItems[] = [
                    'index'    => $idx,
                    'name'     => $menuData['mn_name'] . " (ไซส์ " . $menuData['size'] . ")",
                    'type'     => 'ชุดหมูกระทะ',
                    'price'    => $menuData['price'],
                    'amount'   => $item['amount'],
                    'subtotal' => $subtotal
                ];
            }
        } elseif ($item['type'] === 'ingredient') {
            $iStmt = $pdo->prepare("SELECT in_name, in_price FROM ingredients WHERE in_id = ?");
            $iStmt->execute([$item['in_id']]);
            $ingreData = $iStmt->fetch();
            if ($ingreData) {
                $subtotal = $ingreData['in_price'] * $item['amount'];
                $grandTotal += $subtotal;
                $cartItems[] = [
                    'index'    => $idx,
                    'name'     => $ingreData['in_name'],
                    'type'     => 'วัตถุดิบเพิ่มเติม',
                    'price'    => $ingreData['in_price'],
                    'amount'   => $item['amount'],
                    'subtotal' => $subtotal
                ];
            }
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1" style="color: #1a237e;">
                <i class="fa-solid fa-cart-shopping me-2"></i> ตะกร้าสินค้าหมูกระทะ
            </h2>
            <p class="text-muted small">ตรวจสอบรายการอาหารและระบุห้องพักรวมถึงวันที่ต้องการรับประทาน</p>
        </div>
        <a href="<?= BASE_URL ?>/customer/menu.php" class="btn btn-outline-secondary shadow-none btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> เลือกเมนูอาหารเพิ่ม
        </a>
    </div>

    <?php if (count($cartItems) > 0): ?>
        <div class="row g-4">
            <!-- Items Table -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-3 overflow-hidden mb-3">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>รายการ</th>
                                    <th>ประเภท</th>
                                    <th class="text-center">จำนวน</th>
                                    <th class="text-end">ราคา/หน่วย</th>
                                    <th class="text-end">ราคารวม</th>
                                    <th class="text-center">ลบ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cartItems as $c): ?>
                                    <tr>
                                        <td class="fw-semibold"><?= e($c['name']) ?></td>
                                        <td><span class="badge bg-light text-dark border"><?= e($c['type']) ?></span></td>
                                        <td class="text-center"><?= $c['amount'] ?></td>
                                        <td class="text-end"><?= formatPrice($c['price']) ?></td>
                                        <td class="text-end fw-bold text-success"><?= formatPrice($c['subtotal']) ?></td>
                                        <td class="text-center">
                                            <a href="<?= BASE_URL ?>/customer/cart.php?action=delete&index=<?= $c['index'] ?>" 
                                               class="btn btn-sm btn-outline-danger shadow-none" 
                                               onclick="return confirm('ต้องการลบรายการนี้ใช่หรือไม่?');">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold fs-5">ยอดรวมทั้งหมด:</td>
                                    <td class="text-end fw-bold text-success fs-5"><?= formatPrice($grandTotal) ?> บาท</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <a href="<?= BASE_URL ?>/customer/cart.php?action=clear" 
                       class="btn btn-outline-secondary btn-sm shadow-none"
                       onclick="return confirm('ต้องการล้างตะกร้าทั้งหมดใช่หรือไม่?');">
                        <i class="fa-solid fa-trash-can me-1"></i> ล้างตะกร้าทั้งหมด
                    </a>
                </div>
            </div>

            <!-- Order Confirmation Box -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3" style="color: #1a237e;">ยืนยันการสั่งอาหาร</h5>
                        
                        <form action="<?= BASE_URL ?>/customer/cart.php" method="POST">
                            <input type="hidden" name="action" value="confirm_order">

                            <div class="mb-3">
                                <label for="rm_id" class="form-label fw-semibold">เลือกห้องพักที่จะรับอาหาร <span class="text-danger">*</span></label>
                                <select class="form-select" name="rm_id" id="rm_id" required>
                                    <?php if (count($customerBookings) > 0): ?>
                                        <?php foreach ($customerBookings as $cb): ?>
                                            <option value="<?= e($cb['rm_id']) ?>">
                                                ห้อง <?= e($cb['rm_id']) ?> (พัก <?= thaiDate($cb['check_in']) ?> - <?= thaiDate($cb['check_out']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="A0">ห้อง A0</option>
                                        <option value="A1">ห้อง A1</option>
                                        <option value="A2">ห้อง A2</option>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="receive" class="form-label fw-semibold">วันที่ต้องการรับอาหาร <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="receive" id="receive" 
                                       min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>" required>
                            </div>

                            <div class="border-top pt-3 mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">จำนวนรายการ:</span>
                                    <span class="fw-bold"><?= count($cartItems) ?> รายการ</span>
                                </div>
                                <div class="d-flex justify-content-between fs-5 fw-bold">
                                    <span>ยอดชำระ:</span>
                                    <span class="text-success"><?= formatPrice($grandTotal) ?> บาท</span>
                                </div>
                                <small class="text-muted d-block mt-1">* ชำระเงินค่าอาหารเมื่อพนักงานนำไปเสิร์ฟที่แพพัก *</small>
                            </div>

                            <button type="submit" class="btn btn-warning w-100 py-2 shadow-none fw-bold">
                                <i class="fa-solid fa-check me-1"></i> ยืนยันการสั่งอาหาร
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-5 bg-white rounded-3 shadow-sm border">
            <i class="fa-solid fa-cart-shopping text-muted fs-1 mb-3"></i>
            <h4 class="text-muted">ไม่มีรายการอาหารในตะกร้า</h4>
            <p class="text-muted small">คุณสามารถเลือกสั่งชุดหมูกระทะและวัตถุดิบสดใหม่เพื่อนำไปปิ้งย่างที่ห้องพักได้</p>
            <a href="<?= BASE_URL ?>/customer/menu.php" class="btn btn-warning shadow-none px-4 mt-2">
                <i class="fa-solid fa-utensils me-1"></i> ไปหน้าเลือกสั่งอาหาร
            </a>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
