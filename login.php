<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

// หากล็อกอินลูกค้าอยู่แล้ว ให้ไปหน้าแดชบอร์ดลูกค้า
if (isCustomerLoggedIn()) {
    header("Location: " . BASE_URL . "/customer/index.php");
    exit();
}

$pageTitle = "เข้าสู่ระบบ";
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['cus_user'] ?? '');
    $password = trim($_POST['cus_pass'] ?? '');

    if (empty($username) || empty($password)) {
        $error = "กรุณากรอกชื่อผู้ใช้และรหัสผ่าน";
    } else {
        // ใช้ Prepared Statement เพื่อความปลอดภัยจาก SQL Injection
        $stmt = $pdo->prepare("SELECT * FROM customer WHERE cus_user = ?");
        $stmt->execute([$username]);
        $customer = $stmt->fetch();

        // ตรวจสอบรหัสผ่าน (รองรับทั้ง plain text เดิม และ password_verify)
        if ($customer && ($password === $customer['cus_pass'] || password_verify($password, $customer['cus_pass']))) {
            $_SESSION['cus_user'] = $customer['cus_user'];
            $_SESSION['cus_name'] = $customer['cus_name'];
            $_SESSION['cus_tel']  = $customer['cus_tel'];
            $_SESSION['cus_email'] = $customer['cus_email'];
            $_SESSION['logged_in'] = true;

            setFlashMessage('success', 'เข้าสู่ระบบสำเร็จ ยินดีต้อนรับคุณ ' . $customer['cus_name']);
            header("Location: " . BASE_URL . "/customer/index.php");
            exit();
        } else {
            $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <img src="<?= BASE_URL ?>/images/logo/logo.png" alt="logo" height="60" class="mb-2">
                        <h3 class="fw-bold" style="color: #1a237e;">เข้าสู่ระบบสมาชิก</h3>
                        <p class="text-muted small">กรอกข้อมูลเพื่อเข้าสู่ระบบ House boat</p>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger py-2 small" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-1"></i> <?= e($error) ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= BASE_URL ?>/login.php" method="POST">
                        <div class="mb-3">
                            <label for="cus_user" class="form-label">ชื่อผู้ใช้ (Username)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                                <input type="text" class="form-control" id="cus_user" name="cus_user" value="<?= e($_POST['cus_user'] ?? '') ?>" required autofocus>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="cus_pass" class="form-label">รหัสผ่าน (Password)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                <input type="password" class="form-control" id="cus_pass" name="cus_pass" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 custom-bg shadow-none mb-3">เข้าสู่ระบบ</button>

                        <div class="text-center">
                            <span class="text-muted small">ยังไม่มีบัญชีผู้ใช้?</span>
                            <a href="<?= BASE_URL ?>/register.php" class="text-decoration-none small fw-bold">สมัครสมาชิกที่นี่</a>
                        </div>
                    </form>

                    <hr class="my-4">
                    <div class="text-center">
                        <a href="<?= BASE_URL ?>/admin/login.php" class="text-secondary small text-decoration-none">
                            <i class="fa-solid fa-user-shield me-1"></i> เข้าสู่ระบบสำหรับผู้ดูแลระบบ (Admin)
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
