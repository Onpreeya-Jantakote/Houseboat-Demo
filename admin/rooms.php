<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdminLogin();

$pageTitle = "จัดการห้องพัก";

// จัดการ Add / Edit / Delete ห้องพัก
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $rm_id       = trim($_POST['rm_id'] ?? '');
        $tr_id       = intval($_POST['tr_id'] ?? 1);
        $description = trim($_POST['description'] ?? '');
        $price       = intval($_POST['price'] ?? 0);

        if (empty($rm_id) || $price <= 0) {
            setFlashMessage('error', 'กรุณาระบุรหัสห้องพักและราคาให้ถูกต้อง');
        } else {
            // ตรวจสอบว่ารหัสห้องซ้ำหรือไม่
            $checkStmt = $pdo->prepare("SELECT rm_id FROM room WHERE rm_id = ?");
            $checkStmt->execute([$rm_id]);
            if ($checkStmt->fetch()) {
                setFlashMessage('error', 'รหัสห้องพักนี้มีอยู่ในระบบแล้ว');
            } else {
                $imageContent = '';
                if (isset($_FILES['rm_pic']) && $_FILES['rm_pic']['error'] === UPLOAD_ERR_OK) {
                    $imageContent = file_get_contents($_FILES['rm_pic']['tmp_name']);
                }

                $insertStmt = $pdo->prepare("INSERT INTO room (rm_id, tr_id, description, price, rm_pic) VALUES (?, ?, ?, ?, ?)");
                if ($insertStmt->execute([$rm_id, $tr_id, $description, $price, $imageContent])) {
                    setFlashMessage('success', 'เพิ่มห้องพักใหม่เรียบร้อยแล้ว');
                } else {
                    setFlashMessage('error', 'เกิดข้อผิดพลาดในการเพิ่มห้องพัก');
                }
            }
        }
    } elseif ($action === 'edit') {
        $rm_id       = trim($_POST['rm_id'] ?? '');
        $tr_id       = intval($_POST['tr_id'] ?? 1);
        $description = trim($_POST['description'] ?? '');
        $price       = intval($_POST['price'] ?? 0);

        if (empty($rm_id) || $price <= 0) {
            setFlashMessage('error', 'กรุณาระบุข้อมูลให้ถูกต้อง');
        } else {
            if (isset($_FILES['rm_pic']) && $_FILES['rm_pic']['error'] === UPLOAD_ERR_OK) {
                $imageContent = file_get_contents($_FILES['rm_pic']['tmp_name']);
                $updateStmt = $pdo->prepare("UPDATE room SET tr_id = ?, description = ?, price = ?, rm_pic = ? WHERE rm_id = ?");
                $updateStmt->execute([$tr_id, $description, $price, $imageContent, $rm_id]);
            } else {
                $updateStmt = $pdo->prepare("UPDATE room SET tr_id = ?, description = ?, price = ? WHERE rm_id = ?");
                $updateStmt->execute([$tr_id, $description, $price, $rm_id]);
            }
            setFlashMessage('success', 'แก้ไขข้อมูลห้องพักเรียบร้อยแล้ว');
        }
    } elseif ($action === 'delete') {
        $rm_id = trim($_POST['rm_id'] ?? '');
        if (!empty($rm_id)) {
            $delStmt = $pdo->prepare("DELETE FROM room WHERE rm_id = ?");
            if ($delStmt->execute([$rm_id])) {
                setFlashMessage('success', 'ลบห้องพักเรียบร้อยแล้ว');
            } else {
                setFlashMessage('error', 'เกิดข้อผิดพลาดในการลบห้องพัก');
            }
        }
    }

    header("Location: " . BASE_URL . "/admin/rooms.php");
    exit();
}

// ดึงประเภทห้องพักทั้งหมด
$types = $pdo->query("SELECT * FROM typeroom ORDER BY tr_id ASC")->fetchAll();

// ดึงห้องพักทั้งหมด
$rooms = $pdo->query("SELECT room.*, typeroom.tr_name FROM room INNER JOIN typeroom ON room.tr_id = typeroom.tr_id ORDER BY room.rm_id ASC")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1" style="color: #1a237e;">
                <i class="fa-solid fa-bed me-2"></i> จัดการข้อมูลห้องพัก
            </h2>
            <p class="text-muted small">เพิ่ม แก้ไข หรือลบข้อมูลห้องพัก ราคา และประเภทเตียง</p>
        </div>
        <button type="button" class="btn btn-primary custom-bg shadow-none" data-bs-toggle="modal" data-bs-target="#addRoomModal">
            <i class="fa-solid fa-plus me-1"></i> เพิ่มห้องพักใหม่
        </button>
    </div>

    <!-- Rooms Table -->
    <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 120px;">รูปห้องพัก</th>
                        <th>รหัสห้อง</th>
                        <th>ประเภทเตียง</th>
                        <th>ราคา/คืน</th>
                        <th>รายละเอียด</th>
                        <th class="text-end" style="width: 180px;">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($rooms) > 0): ?>
                        <?php foreach ($rooms as $r): ?>
                            <?php 
                                $fallbackImage = BASE_URL . "/images/rooms/" . $r["rm_id"] . ".jpg";
                                $imgSrc = getImageSrc($r["rm_pic"], $fallbackImage);
                            ?>
                            <tr>
                                <td>
                                    <img src="<?= $imgSrc ?>" class="rounded border shadow-sm" style="width: 100px; height: 65px; object-fit: cover;" 
                                         alt="ห้อง <?= e($r['rm_id']) ?>" onerror="this.src='<?= BASE_URL ?>/images/cover/1.jpg';">
                                </td>
                                <td><span class="badge bg-primary fs-6">ห้อง <?= e($r['rm_id']) ?></span></td>
                                <td><span class="badge bg-light text-dark border"><?= e($r['tr_name']) ?></span></td>
                                <td class="fw-bold text-success"><?= formatPrice($r['price']) ?> บาท</td>
                                <td class="small text-muted" style="max-width: 320px; white-space: pre-line;">
                                    <?= e($r['description']) ?>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary shadow-none me-1" 
                                            onclick='openEditRoomModal(<?= json_encode($r) ?>)'>
                                        <i class="fa-solid fa-pen-to-square me-1"></i> แก้ไข
                                    </button>
                                    <form action="<?= BASE_URL ?>/admin/rooms.php" method="POST" class="d-inline" onsubmit="return confirm('คุณต้องการลบห้อง <?= e($r['rm_id']) ?> ใช่หรือไม่?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="rm_id" value="<?= e($r['rm_id']) ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger shadow-none">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">ไม่พบข้อมูลห้องพักในระบบ</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: เพิ่มห้องพักใหม่ -->
<div class="modal fade" id="addRoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" style="color: #1a237e;">เพิ่มห้องพักใหม่</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= BASE_URL ?>/admin/rooms.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add_rm_id" class="form-label fw-semibold">รหัสห้องพัก (เช่น A10, B1) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="rm_id" id="add_rm_id" required placeholder="A10">
                    </div>
                    <div class="mb-3">
                        <label for="add_tr_id" class="form-label fw-semibold">ประเภทห้องพัก <span class="text-danger">*</span></label>
                        <select class="form-select" name="tr_id" id="add_tr_id" required>
                            <?php foreach ($types as $t): ?>
                                <option value="<?= $t['tr_id'] ?>"><?= e($t['tr_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="add_price" class="form-label fw-semibold">ราคาต่อคืน (บาท) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="price" id="add_price" required min="100" placeholder="3000">
                    </div>
                    <div class="mb-3">
                        <label for="add_description" class="form-label fw-semibold">รายละเอียดห้องพัก</label>
                        <textarea class="form-control" name="description" id="add_description" rows="4" placeholder="เครื่องปรับอากาศ, Wi-Fi, ฟรีอาหารเช้า..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="add_rm_pic" class="form-label fw-semibold">รูปภาพห้องพัก</label>
                        <input type="file" class="form-control" name="rm_pic" id="add_rm_pic" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary custom-bg btn-sm shadow-none">บันทึกห้องพัก</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: แก้ไขห้องพัก -->
<div class="modal fade" id="editRoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" style="color: #1a237e;">แก้ไขข้อมูลห้องพัก</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= BASE_URL ?>/admin/rooms.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small">รหัสห้องพัก</label>
                        <input type="text" class="form-control bg-light" name="rm_id" id="edit_rm_id" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="edit_tr_id" class="form-label fw-semibold">ประเภทห้องพัก</label>
                        <select class="form-select" name="tr_id" id="edit_tr_id" required>
                            <?php foreach ($types as $t): ?>
                                <option value="<?= $t['tr_id'] ?>"><?= e($t['tr_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_price" class="form-label fw-semibold">ราคาต่อคืน (บาท) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="price" id="edit_price" required min="100">
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label fw-semibold">รายละเอียดห้องพัก</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="4"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_rm_pic" class="form-label fw-semibold">เปลี่ยนรูปภาพ (เว้นว่างไว้หากไม่เปลี่ยน)</label>
                        <input type="file" class="form-control" name="rm_pic" id="edit_rm_pic" accept="image/*">
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
    function openEditRoomModal(room) {
        document.getElementById('edit_rm_id').value = room.rm_id;
        document.getElementById('edit_tr_id').value = room.tr_id;
        document.getElementById('edit_price').value = room.price;
        document.getElementById('edit_description').value = room.description;
        var modal = new bootstrap.Modal(document.getElementById('editRoomModal'));
        modal.show();
    }
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
