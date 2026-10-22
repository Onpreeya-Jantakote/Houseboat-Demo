<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

if (isCustomerLoggedIn()) {
    header("Location: " . BASE_URL . "/customer/index.php");
    exit();
}

$pageTitle = "สมัครสมาชิก";
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['cus_user'] ?? '');
    $password = trim($_POST['cus_pass'] ?? '');
    $name     = trim($_POST['cus_name'] ?? '');
    $tel      = trim($_POST['cus_tel'] ?? '');
    $email    = trim($_POST['cus_email'] ?? '');

    // ตรวจสอบความถูกต้องเบื้องต้น
    if (empty($username) || empty($password) || empty($name) || empty($tel) || empty($email)) {
        $error = "กรุณากรอกข้อมูลให้ครบทุกช่อง";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "รูปแบบอีเมลไม่ถูกต้อง";
    } else {
        // ตรวจสอบว่าชื่อผู้ใช้ซ้ำหรือไม่
        $checkStmt = $pdo->prepare("SELECT cus_user FROM customer WHERE cus_user = ?");
        $checkStmt->execute([$username]);
        if ($checkStmt->fetch()) {
            $error = "ชื่อผู้ใช้นี้ถูกใช้งานแล้ว กรุณาใช้ชื่ออื่น";
        } else {
            // บันทึกข้อมูลลูกค้าใหม่โดยใช้ Prepared Statement
            $stmt = $pdo->prepare("INSERT INTO customer (cus_user, cus_pass, cus_name, cus_tel, cus_email) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$username, $password, $name, $tel, $email])) {
                setFlashMessage('success', 'สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบด้วยชื่อผู้ใช้และรหัสผ่านของคุณ');
                header("Location: " . BASE_URL . "/login.php");
                exit();
            } else {
                $error = "เกิดข้อผิดพลาดในการบันทึกข้อมูล กรุณาลองใหม่อีกครั้ง";
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <img src="<?= BASE_URL ?>/images/logo/logo.png" alt="logo" height="60" class="mb-2">
                        <h3 class="fw-bold" style="color: #1a237e;">สมัครสมาชิกใหม่</h3>
                        <p class="text-muted small">สร้างบัญชีสำหรับจองห้องพักและบริการอาหารหมูกระทะ</p>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger py-2 small" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-1"></i> <?= e($error) ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= BASE_URL ?>/register.php" method="POST">
                        <div class="mb-3">
                            <label for="cus_user" class="form-label">ชื่อผู้ใช้ (Username) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="cus_user" name="cus_user" value="<?= e($_POST['cus_user'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="cus_pass" class="form-label">รหัสผ่าน (Password) <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="cus_pass" name="cus_pass" required>
                        </div>

                        <div class="mb-3">
                            <label for="cus_name" class="form-label">ชื่อ-นามสกุล <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="cus_name" name="cus_name" value="<?= e($_POST['cus_name'] ?? '') ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cus_tel" class="form-label">เบอร์โทรศัพท์ <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="cus_tel" name="cus_tel" value="<?= e($_POST['cus_tel'] ?? '') ?>" required placeholder="08xxxxxxxx">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="cus_email" class="form-label">อีเมล <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="cus_email" name="cus_email" value="<?= e($_POST['cus_email'] ?? '') ?>" required placeholder="example@mail.com">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 custom-bg shadow-none mb-3">ยืนยันการสมัครสมาชิก</button>

                        <div class="text-center">
                            <span class="text-muted small">มีบัญชีผู้ใช้อยู่แล้ว?</span>
                            <a href="<?= BASE_URL ?>/login.php" class="text-decoration-none small fw-bold">เข้าสู่ระบบที่นี่</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
