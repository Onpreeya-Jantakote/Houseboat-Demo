<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdminLogin();

$pageTitle = "รายการสั่งอาหารหมูกระทะ";
$search = trim($_GET['search'] ?? '');

// จัดการลบรายการสั่งอาหาร
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $delId = intval($_POST['mnbill_id'] ?? 0);
    if ($delId > 0) {
        $stmt = $pdo->prepare("DELETE FROM menubill WHERE mnbill_id = ?");
        if ($stmt->execute([$delId])) {
            setFlashMessage('success', 'ลบรายการสั่งอาหารเรียบร้อยแล้ว');
        } else {
            setFlashMessage('error', 'เกิดข้อผิดพลาดในการลบรายการ');
        }
    }
    header("Location: " . BASE_URL . "/admin/food-orders.php");
    exit();
}

// ดึงข้อมูลคำสั่งซื้ออาหาร
$sql = "SELECT mb.*, m.mn_name, m.size, i.in_name 
        FROM menubill mb 
        LEFT JOIN menu m ON mb.mn_id = m.mn_id 
        LEFT JOIN ingredients i ON mb.in_id = i.in_id 
        WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (mb.cus_name LIKE ? OR mb.rm_id LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY mb.receive DESC, mb.mnbill_id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

// คำนวณยอดรวมทั้งหมด
$totalRevenue = 0;
foreach ($orders as $o) {
    $totalRevenue += $o['total'];
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1" style="color: #1a237e;">
                <i class="fa-solid fa-utensils me-2"></i> ข้อมูลการสั่งอาหารหมูกระทะ
            </h2>
            <p class="text-muted small">ตรวจสอบรายการสั่งอาหารหมูกระทะและวัตถุดิบเพื่อเตรียมบริการให้ลูกค้าตามห้องพัก</p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-success fs-6 px-3 py-2">ยอดรวม: <?= formatPrice($totalRevenue) ?> บาท</span>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-body p-3">
            <form action="<?= BASE_URL ?>/admin/food-orders.php" method="GET" class="row g-2 align-items-center">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" class="form-control" name="search" placeholder="ค้นหาด้วยชื่อผู้เข้าพัก หรือหมายเลขห้อง..." value="<?= e($search) ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary custom-bg w-100 shadow-none">ค้นหา</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>รหัสบิล</th>
                        <th>หมายเลขห้อง</th>
                        <th>ชื่อผู้สั่งอาหาร</th>
                        <th>วันที่รับอาหาร</th>
                        <th>เซ็ตหมูกระทะ</th>
                        <th>วัตถุดิบเพิ่มเติม</th>
                        <th class="text-center">จำนวน</th>
                        <th class="text-end">ราคารวม</th>
                        <th class="text-end">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($orders) > 0): ?>
                        <?php foreach ($orders as $o): ?>
                            <tr>
                                <td><span class="text-muted">#<?= $o['mnbill_id'] ?></span></td>
                                <td><span class="badge bg-primary fs-6">ห้อง <?= e($o['rm_id']) ?></span></td>
                                <td class="fw-semibold"><?= e($o['cus_name']) ?></td>
                                <td class="fw-bold text-primary"><?= thaiDate($o['receive']) ?></td>
                                <td>
                                    <?php if (!empty($o['mn_name'])): ?>
                                        <?= e($o['mn_name']) ?> <span class="badge bg-light text-dark border">ไซส์ <?= e($o['size'] ?? '') ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= !empty($o['in_name']) ? e($o['in_name']) : '<span class="text-muted">-</span>' ?></td>
                                <td class="text-center fw-bold"><?= e($o['amount']) ?></td>
                                <td class="text-end fw-bold text-success"><?= formatPrice($o['total']) ?> บาท</td>
                                <td class="text-end">
                                    <form action="<?= BASE_URL ?>/admin/food-orders.php?<?= http_build_query($_GET) ?>" method="POST" onsubmit="return confirm('ต้องการลบบิลอาหารนี้ใช่หรือไม่?');" class="d-inline">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="mnbill_id" value="<?= $o['mnbill_id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger shadow-none">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">ไม่พบข้อมูลการสั่งอาหาร</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
