<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

if (isAdminLoggedIn()) {
    header("Location: " . BASE_URL . "/admin/index.php");
    exit();
}

$pageTitle = "เข้าสู่ระบบผู้ดูแลระบบ (Admin)";
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['ad_user'] ?? '');
    $password = trim($_POST['ad_pass'] ?? '');

    if (empty($username) || empty($password)) {
        $error = "กรุณากรอกชื่อผู้ใช้และรหัสผ่าน";
    } else {
        // ใช้ Prepared Statement ตรวจสอบผู้ดูแลระบบ
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE ad_user = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && ($password === $admin['ad_pass'] || password_verify($password, $admin['ad_pass']))) {
            $_SESSION['ad_user']  = $admin['ad_user'];
            $_SESSION['ad_name']  = $admin['ad_name'];
            $_SESSION['ad_email'] = $admin['ad_email'];
            $_SESSION['ad_tel']   = $admin['ad_tel'];
            $_SESSION['logged_in'] = true;

            setFlashMessage('success', 'เข้าสู่ระบบผู้ดูแลระบบสำเร็จ ยินดีต้อนรับคุณ ' . $admin['ad_name']);
            header("Location: " . BASE_URL . "/admin/index.php");
            exit();
        } else {
            $error = "ชื่อผู้ใช้หรือรหัสผ่านผู้ดูแลระบบไม่ถูกต้อง";
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="text-primary fs-1 mb-2">
                            <i class="fa-solid fa-user-shield" style="color: #1a237e;"></i>
                        </div>
                        <h3 class="fw-bold" style="color: #1a237e;">Admin Login</h3>
                        <p class="text-muted small">ระบบจัดการหลังบ้านสำหรับเจ้าหน้าที่</p>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger py-2 small" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-1"></i> <?= e($error) ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= BASE_URL ?>/admin/login.php" method="POST">
                        <div class="mb-3">
                            <label for="ad_user" class="form-label">ชื่อผู้ใช้แอดมิน (Admin Username)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                                <input type="text" class="form-control" id="ad_user" name="ad_user" value="<?= e($_POST['ad_user'] ?? '') ?>" required autofocus>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="ad_pass" class="form-label">รหัสผ่าน (Password)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                <input type="password" class="form-control" id="ad_pass" name="ad_pass" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-dark w-100 py-2 shadow-none mb-3">เข้าสู่ระบบ Admin</button>

                        <div class="text-center">
                            <a href="<?= BASE_URL ?>/login.php" class="text-decoration-none small text-secondary">
                                <i class="fa-solid fa-arrow-left me-1"></i> กลับไปหน้าเข้าสู่ระบบลูกค้า
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
