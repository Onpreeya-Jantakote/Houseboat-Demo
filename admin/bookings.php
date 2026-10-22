<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdminLogin();

$pageTitle = "จัดการข้อมูลผู้เข้าพัก";
$search = trim($_GET['search'] ?? '');
$filter = trim($_GET['filter'] ?? 'all');

// จัดการการยืนยัน หรือ ยกเลิกการจอง
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';
    $type   = $_POST['type'] ?? '';
    $id     = intval($_POST['id'] ?? 0);

    if ($id > 0 && in_array($type, ['anon', 'member'])) {
        $table = ($type === 'anon') ? 'anonybooking' : 'detailbooking';
        $pk    = ($type === 'anon') ? 'ab_id' : 'db_id';

        if ($action === 'confirm') {
            $stmt = $pdo->prepare("UPDATE {$table} SET status = 'ยืนยันการจอง' WHERE {$pk} = ?");
            if ($stmt->execute([$id])) {
                setFlashMessage('success', 'ยืนยันการจองเรียบร้อยแล้ว');
            } else {
                setFlashMessage('error', 'เกิดข้อผิดพลาดในการยืนยัน');
            }
        } elseif ($action === 'cancel') {
            $stmt = $pdo->prepare("DELETE FROM {$table} WHERE {$pk} = ?");
            if ($stmt->execute([$id])) {
                setFlashMessage('success', 'ยกเลิกการจองเรียบร้อยแล้ว');
            } else {
                setFlashMessage('error', 'เกิดข้อผิดพลาดในการยกเลิก');
            }
        }
    }
    header("Location: " . BASE_URL . "/admin/bookings.php?" . http_build_query($_GET));
    exit();
}

// สร้างคิวรีดึงข้อมูลการจองทั้ง 2 ตาราง
$anonQuery = "SELECT 'anon' AS type, 'บุคคลทั่วไป' AS user_type, ab_id AS id, rm_id, amount, check_in, check_out, 
              am_name AS name, am_email AS email, am_tel AS tel, add_bed, status, date_pay, receipt 
              FROM anonybooking WHERE 1=1";
$memQuery  = "SELECT 'member' AS type, 'ลูกค้าสมาชิก' AS user_type, db_id AS id, rm_id, amount, check_in, check_out, 
              cus_name AS name, '' AS email, cus_tel AS tel, add_bed, status, date_pay, receipt 
              FROM detailbooking WHERE 1=1";

$anonParams = [];
$memParams  = [];

if (!empty($search)) {
    $anonQuery .= " AND am_name LIKE ?";
    $anonParams[] = "%$search%";
    $memQuery  .= " AND cus_name LIKE ?";
    $memParams[] = "%$search%";
}

if ($filter === 'pending') {
    $anonQuery .= " AND status = 'รอดำเนินการ'";
    $memQuery  .= " AND status = 'รอดำเนินการ'";
} elseif ($filter === 'confirmed') {
    $anonQuery .= " AND status = 'ยืนยันการจอง'";
    $memQuery  .= " AND status = 'ยืนยันการจอง'";
}

$unionSql = "({$anonQuery}) UNION ALL ({$memQuery}) ORDER BY date_pay DESC, id DESC";
$fullParams = array_merge($anonParams, $memParams);

$stmt = $pdo->prepare($unionSql);
$stmt->execute($fullParams);
$bookings = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1" style="color: #1a237e;">
                <i class="fa-solid fa-hotel me-2"></i> จัดการข้อมูลผู้เข้าพัก
            </h2>
            <p class="text-muted small">ตรวจสอบสลิปการโอนเงิน ยืนยันการจอง หรือยกเลิกการจองห้องพัก</p>
        </div>
        <div>
            <span class="badge bg-primary fs-6 px-3 py-2">พบ <?= count($bookings) ?> รายการ</span>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-body p-3">
            <form action="<?= BASE_URL ?>/admin/bookings.php" method="GET" class="row g-2 align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" class="form-control" name="search" placeholder="ค้นหาด้วยชื่อผู้เข้าพัก..." value="<?= e($search) ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <select class="form-select" name="filter">
                        <option value="all" <?= ($filter === 'all') ? 'selected' : '' ?>>สถานะทั้งหมด</option>
                        <option value="pending" <?= ($filter === 'pending') ? 'selected' : '' ?>>เฉพาะรอดำเนินการ (รอตรวจสลิป)</option>
                        <option value="confirmed" <?= ($filter === 'confirmed') ? 'selected' : '' ?>>เฉพาะยืนยันการจองแล้ว</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary custom-bg w-100 shadow-none">ค้นหา</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>รหัส/ประเภท</th>
                        <th>ห้อง</th>
                        <th>ชื่อผู้เข้าพัก</th>
                        <th>เบอร์โทร</th>
                        <th>เช็คอิน</th>
                        <th>เช็คเอาท์</th>
                        <th>เตียงเสริม</th>
                        <th>สถานะ</th>
                        <th>สลิปมัดจำ</th>
                        <th class="text-end">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($bookings) > 0): ?>
                        <?php foreach ($bookings as $b): ?>
                            <tr>
                                <td>
                                    <span class="badge <?= ($b['user_type'] === 'ลูกค้าสมาชิก') ? 'bg-info text-dark' : 'bg-secondary' ?> small">
                                        <?= e($b['user_type']) ?>
                                    </span>
                                    <div class="text-muted small">#<?= $b['id'] ?></div>
                                </td>
                                <td><span class="badge bg-primary fs-6">ห้อง <?= e($b['rm_id']) ?></span></td>
                                <td class="fw-semibold">
                                    <?= e($b['name']) ?>
                                    <?php if (!empty($b['email'])): ?>
                                        <div class="text-muted small"><?= e($b['email']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><?= e($b['tel']) ?></td>
                                <td><?= thaiDate($b['check_in']) ?></td>
                                <td><?= thaiDate($b['check_out']) ?></td>
                                <td><?= ($b['add_bed'] == 1) ? '<span class="text-success small fw-bold">+500</span>' : '<span class="text-muted">-</span>' ?></td>
                                <td>
                                    <?php if ($b['status'] === 'ยืนยันการจอง'): ?>
                                        <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i> ยืนยันแล้ว</span>
                                    <?php elseif ($b['status'] === 'รอดำเนินการ'): ?>
                                        <span class="badge bg-warning text-dark"><i class="fa-solid fa-clock me-1"></i> รอดำเนินการ</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?= e($b['status'] ?: 'ยังไม่ชำระ') ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($b['receipt'])): ?>
                                        <?php $slipSrc = 'data:image/jpeg;base64,' . base64_encode($b['receipt']); ?>
                                        <img src="<?= $slipSrc ?>" class="rounded border shadow-sm" style="width: 55px; height: 55px; object-fit: cover; cursor: pointer;" 
                                             alt="สลิป" onclick="showSlipModal('<?= $slipSrc ?>', 'ห้อง <?= e($b['rm_id']) ?> - <?= e($b['name']) ?>')">
                                    <?php else: ?>
                                        <span class="text-muted small"><i class="fa-solid fa-image-slash"></i> ไม่มีสลิป</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <?php if ($b['status'] !== 'ยืนยันการจอง'): ?>
                                            <form action="<?= BASE_URL ?>/admin/bookings.php?<?= http_build_query($_GET) ?>" method="POST" onsubmit="return confirm('ยืนยันการจองห้องนี้ใช่หรือไม่?');">
                                                <input type="hidden" name="action" value="confirm">
                                                <input type="hidden" name="type" value="<?= $b['type'] ?>">
                                                <input type="hidden" name="id" value="<?= $b['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-success shadow-none">
                                                    <i class="fa-solid fa-check"></i> ยืนยัน
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <form action="<?= BASE_URL ?>/admin/bookings.php?<?= http_build_query($_GET) ?>" method="POST" onsubmit="return confirm('คุณต้องการยกเลิก/ลบการจองนี้ใช่หรือไม่?');">
                                            <input type="hidden" name="action" value="cancel">
                                            <input type="hidden" name="type" value="<?= $b['type'] ?>">
                                            <input type="hidden" name="id" value="<?= $b['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger shadow-none">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">ไม่พบข้อมูลการจองห้องพัก</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Preview Slip -->
<div class="modal fade" id="slipModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="slipModalTitle">หลักฐานการชำระเงินมัดจำ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-3">
                <img id="slipModalImg" src="" class="img-fluid rounded shadow" style="max-height: 500px;" alt="สลิป">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<script>
    function showSlipModal(src, title) {
        document.getElementById('slipModalImg').src = src;
        document.getElementById('slipModalTitle').innerText = 'หลักฐานสลิป: ' + title;
        var myModal = new bootstrap.Modal(document.getElementById('slipModal'));
        myModal.show();
    }
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
