<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdminLogin();

$pageTitle = "จัดการวัตถุดิบอาหารหมูกระทะ";
$search = trim($_GET['search'] ?? '');

// จัดการ Add / Edit / Delete / Toggle Status
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $in_name  = trim($_POST['in_name'] ?? '');
        $in_unit  = intval($_POST['in_unit'] ?? 1);
        $in_price = intval($_POST['in_price'] ?? 0);

        if (empty($in_name) || $in_price <= 0) {
            setFlashMessage('error', 'กรุณาระบุชื่อวัตถุดิบและราคาให้ถูกต้อง');
        } else {
            $imageContent = '';
            if (isset($_FILES['in_picture']) && $_FILES['in_picture']['error'] === UPLOAD_ERR_OK) {
                $imageContent = file_get_contents($_FILES['in_picture']['tmp_name']);
            }

            // in_id ปล่อยให้ Auto Increment จัดการ (แก้ไขข้อผิดพลาดเดิมที่ส่ง NULL)
            $insertStmt = $pdo->prepare("INSERT INTO ingredients (in_name, in_unit, in_price, in_picture, in_status) VALUES (?, ?, ?, ?, 0)");
            if ($insertStmt->execute([$in_name, $in_unit, $in_price, $imageContent])) {
                setFlashMessage('success', 'เพิ่มวัตถุดิบใหม่เรียบร้อยแล้ว');
            } else {
                setFlashMessage('error', 'เกิดข้อผิดพลาดในการเพิ่มวัตถุดิบ');
            }
        }
    } elseif ($action === 'edit') {
        $in_id    = intval($_POST['in_id'] ?? 0);
        $in_name  = trim($_POST['in_name'] ?? '');
        $in_unit  = intval($_POST['in_unit'] ?? 1);
        $in_price = intval($_POST['in_price'] ?? 0);

        if ($in_id <= 0 || empty($in_name) || $in_price <= 0) {
            setFlashMessage('error', 'กรุณาระบุข้อมูลให้ถูกต้อง');
        } else {
            if (isset($_FILES['in_picture']) && $_FILES['in_picture']['error'] === UPLOAD_ERR_OK) {
                $imageContent = file_get_contents($_FILES['in_picture']['tmp_name']);
                $updateStmt = $pdo->prepare("UPDATE ingredients SET in_name = ?, in_unit = ?, in_price = ?, in_picture = ? WHERE in_id = ?");
                $updateStmt->execute([$in_name, $in_unit, $in_price, $imageContent, $in_id]);
            } else {
                $updateStmt = $pdo->prepare("UPDATE ingredients SET in_name = ?, in_unit = ?, in_price = ? WHERE in_id = ?");
                $updateStmt->execute([$in_name, $in_unit, $in_price, $in_id]);
            }
            setFlashMessage('success', 'แก้ไขข้อมูลวัตถุดิบเรียบร้อยแล้ว');
        }
    } elseif ($action === 'toggle_status') {
        $in_id = intval($_POST['in_id'] ?? 0);
        if ($in_id > 0) {
            $toggleStmt = $pdo->prepare("UPDATE ingredients SET in_status = IF(in_status = 0, 1, 0) WHERE in_id = ?");
            $toggleStmt->execute([$in_id]);
            setFlashMessage('success', 'ปรับเปลี่ยนสถานะสินค้าเรียบร้อยแล้ว');
        }
    } elseif ($action === 'delete') {
        $in_id = intval($_POST['in_id'] ?? 0);
        if ($in_id > 0) {
            $delStmt = $pdo->prepare("DELETE FROM ingredients WHERE in_id = ?");
            if ($delStmt->execute([$in_id])) {
                setFlashMessage('success', 'ลบวัตถุดิบเรียบร้อยแล้ว');
            } else {
                setFlashMessage('error', 'เกิดข้อผิดพลาดในการลบ');
            }
        }
    }

    header("Location: " . BASE_URL . "/admin/ingredients.php");
    exit();
}

// ดึงข้อมูลวัตถุดิบทั้งหมด
$sql = "SELECT * FROM ingredients WHERE 1=1";
$params = [];
if (!empty($search)) {
    $sql .= " AND in_name LIKE ?";
    $params[] = "%$search%";
}
$sql .= " ORDER BY in_id ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$ingredients = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1" style="color: #1a237e;">
                <i class="fa-solid fa-bowl-food me-2"></i> จัดการวัตถุดิบอาหารหมูกระทะ
            </h2>
            <p class="text-muted small">เพิ่ม แก้ไข ลบ และตรวจสอบสต็อกวัตถุดิบสำหรับบริการลูกค้า</p>
        </div>
        <button type="button" class="btn btn-primary custom-bg shadow-none" data-bs-toggle="modal" data-bs-target="#addIngModal">
            <i class="fa-solid fa-plus me-1"></i> เพิ่มวัตถุดิบใหม่
        </button>
    </div>

    <!-- Search Bar -->
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-body p-3">
            <form action="<?= BASE_URL ?>/admin/ingredients.php" method="GET" class="row g-2 align-items-center">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" class="form-control" name="search" placeholder="ค้นหาด้วยชื่อวัตถุดิบ..." value="<?= e($search) ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary custom-bg w-100 shadow-none">ค้นหา</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Section -->
    <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 80px;">ภาพ</th>
                        <th>รหัส</th>
                        <th>ชื่อวัตถุดิบ</th>
                        <th>ปริมาณต่อหน่วย</th>
                        <th>ราคา</th>
                        <th>สถานะสินค้า</th>
                        <th class="text-end" style="width: 220px;">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($ingredients) > 0): ?>
                        <?php foreach ($ingredients as $in): ?>
                            <?php 
                                $imgSrc = getImageSrc($in['in_picture'], BASE_URL . '/images/cover/5.jpg');
                                $isAvailable = ($in['in_status'] == 0);
                            ?>
                            <tr>
                                <td>
                                    <img src="<?= $imgSrc ?>" class="rounded border shadow-sm" style="width: 60px; height: 50px; object-fit: cover;" 
                                         alt="<?= e($in['in_name']) ?>" onerror="this.src='<?= BASE_URL ?>/images/cover/5.jpg';">
                                </td>
                                <td><span class="text-muted">#<?= $in['in_id'] ?></span></td>
                                <td class="fw-semibold"><?= e($in['in_name']) ?></td>
                                <td><?= e($in['in_unit']) ?> หน่วย</td>
                                <td class="fw-bold text-success"><?= formatPrice($in['in_price']) ?> บาท</td>
                                <td>
                                    <form action="<?= BASE_URL ?>/admin/ingredients.php" method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="toggle_status">
                                        <input type="hidden" name="in_id" value="<?= $in['in_id'] ?>">
                                        <button type="submit" class="btn btn-sm <?= $isAvailable ? 'btn-success' : 'btn-secondary' ?> py-0 px-2" title="คลิกเพื่อสลับสถานะ">
                                            <?= $isAvailable ? '<i class="fa-solid fa-check me-1"></i> มีสินค้า' : '<i class="fa-solid fa-xmark me-1"></i> สินค้าหมด' ?>
                                        </button>
                                    </form>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary shadow-none me-1" 
                                            onclick='openEditIngModal(<?= json_encode($in) ?>)'>
                                        <i class="fa-solid fa-pen-to-square me-1"></i> แก้ไข
                                    </button>
                                    <form action="<?= BASE_URL ?>/admin/ingredients.php" method="POST" class="d-inline" onsubmit="return confirm('คุณต้องการลบวัตถุดิบ <?= e($in['in_name']) ?> ใช่หรือไม่?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="in_id" value="<?= $in['in_id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger shadow-none">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">ไม่พบข้อมูลวัตถุดิบ</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: เพิ่มวัตถุดิบ -->
<div class="modal fade" id="addIngModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" style="color: #1a237e;">เพิ่มวัตถุดิบใหม่</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= BASE_URL ?>/admin/ingredients.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add_in_name" class="form-label fw-semibold">ชื่อวัตถุดิบ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="in_name" id="add_in_name" required placeholder="เช่น หมูสามชั้นสไลด์, วุ้นเส้น">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="add_in_unit" class="form-label fw-semibold">ปริมาณต่อหน่วย <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="in_unit" id="add_in_unit" required min="1" value="1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="add_in_price" class="form-label fw-semibold">ราคา (บาท) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="in_price" id="add_in_price" required min="1" placeholder="50">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="add_in_picture" class="form-label fw-semibold">รูปภาพวัตถุดิบ</label>
                        <input type="file" class="form-control" name="in_picture" id="add_in_picture" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary custom-bg btn-sm shadow-none">บันทึกวัตถุดิบ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: แก้ไขวัตถุดิบ -->
<div class="modal fade" id="editIngModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" style="color: #1a237e;">แก้ไขข้อมูลวัตถุดิบ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= BASE_URL ?>/admin/ingredients.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="in_id" id="edit_in_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_in_name" class="form-label fw-semibold">ชื่อวัตถุดิบ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="in_name" id="edit_in_name" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_in_unit" class="form-label fw-semibold">ปริมาณต่อหน่วย <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="in_unit" id="edit_in_unit" required min="1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_in_price" class="form-label fw-semibold">ราคา (บาท) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="in_price" id="edit_in_price" required min="1">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_in_picture" class="form-label fw-semibold">เปลี่ยนรูปภาพ (เว้นว่างไว้หากไม่เปลี่ยน)</label>
                        <input type="file" class="form-control" name="in_picture" id="edit_in_picture" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary custom-bg btn-sm shadow-none">บันทึกการแก้ไข</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openEditIngModal(item) {
        document.getElementById('edit_in_id').value = item.in_id;
        document.getElementById('edit_in_name').value = item.in_name;
        document.getElementById('edit_in_unit').value = item.in_unit;
        document.getElementById('edit_in_price').value = item.in_price;
        var modal = new bootstrap.Modal(document.getElementById('editIngModal'));
        modal.show();
    }
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
