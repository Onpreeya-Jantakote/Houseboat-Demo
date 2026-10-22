<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireCustomerLogin();

$pageTitle = "ข้อมูลส่วนตัวของฉัน";
$cus_user = $_SESSION['cus_user'];
$error = '';

// ดำเนินการอัปเดตข้อมูลส่วนตัว
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_name  = trim($_POST['new_name'] ?? '');
    $new_tel   = trim($_POST['new_tel'] ?? '');
    $new_email = trim($_POST['new_email'] ?? '');
    $new_pass  = trim($_POST['new_pass'] ?? '');

    if (empty($new_name) || empty($new_tel) || empty($new_email)) {
        $error = "กรุณากรอกชื่อ, เบอร์โทรศัพท์ และอีเมลให้ครบถ้วน";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error = "รูปแบบอีเมลไม่ถูกต้อง";
    } else {
        if (!empty($new_pass)) {
            $updateSql = "UPDATE customer SET cus_name = ?, cus_tel = ?, cus_email = ?, cus_pass = ? WHERE cus_user = ?";
            $updateStmt = $pdo->prepare($updateSql);
            $success = $updateStmt->execute([$new_name, $new_tel, $new_email, $new_pass, $cus_user]);
        } else {
            $updateSql = "UPDATE customer SET cus_name = ?, cus_tel = ?, cus_email = ? WHERE cus_user = ?";
            $updateStmt = $pdo->prepare($updateSql);
            $success = $updateStmt->execute([$new_name, $new_tel, $new_email, $cus_user]);
        }

        if ($success) {
            $_SESSION['cus_name']  = $new_name;
            $_SESSION['cus_tel']   = $new_tel;
            $_SESSION['cus_email'] = $new_email;

            setFlashMessage('success', 'บันทึกข้อมูลส่วนตัวเรียบร้อยแล้ว');
            header("Location: " . BASE_URL . "/customer/profile.php");
            exit();
        } else {
            $error = "เกิดข้อผิดพลาดในการบันทึกข้อมูล กรุณาลองใหม่อีกครั้ง";
        }
    }
}

// ดึงข้อมูลปัจจุบันของลูกค้า
$stmt = $pdo->prepare("SELECT * FROM customer WHERE cus_user = ?");
$stmt->execute([$cus_user]);
$customer = $stmt->fetch();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <img src="<?= BASE_URL ?>/images/logo/user.png" alt="user" height="70" class="mb-2">
                        <h4 class="fw-bold" style="color: #1a237e;">ข้อมูลส่วนตัวของฉัน</h4>
                        <p class="text-muted small">คุณสามารถตรวจสอบและแก้ไขข้อมูลผู้ใช้งานได้ที่นี่</p>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger py-2 small" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-1"></i> <?= e($error) ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= BASE_URL ?>/customer/profile.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label text-muted small">ชื่อผู้ใช้ (Username)</label>
                            <input type="text" class="form-control bg-light" value="<?= e($customer['cus_user']) ?>" readonly>
                            <div class="form-text small">ชื่อผู้ใช้ไม่สามารถเปลี่ยนแปลงได้</div>
                        </div>

                        <div class="mb-3">
                            <label for="new_name" class="form-label fw-semibold">ชื่อ - นามสกุล <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="new_name" id="new_name" value="<?= e($customer['cus_name']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="new_tel" class="form-label fw-semibold">เบอร์โทรศัพท์ <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" name="new_tel" id="new_tel" value="<?= e($customer['cus_tel']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="new_email" class="form-label fw-semibold">อีเมล <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="new_email" id="new_email" value="<?= e($customer['cus_email']) ?>" required>
                        </div>

                        <div class="mb-4">
                            <label for="new_pass" class="form-label fw-semibold">รหัสผ่านใหม่ (เว้นว่างไว้หากไม่ต้องการเปลี่ยน)</label>
                            <input type="password" class="form-control" name="new_pass" id="new_pass" placeholder="กรอกรหัสผ่านใหม่หากต้องการเปลี่ยน">
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="<?= BASE_URL ?>/customer/index.php" class="btn btn-outline-secondary shadow-none">
                                <i class="fa-solid fa-arrow-left me-1"></i> กลับสู่หน้าหลัก
                            </a>
                            <button type="submit" class="btn btn-primary custom-bg px-4 shadow-none">
                                <i class="fa-solid fa-floppy-disk me-1"></i> บันทึกข้อมูล
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
