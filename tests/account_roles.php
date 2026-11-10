<?php
require __DIR__ . '/../Controller/AccountController.php';
require __DIR__ . '/../admin/Controller/TaiKhoanController.php';
$sessionDir = sys_get_temp_dir() . '/role_session_' . bin2hex(random_bytes(6));
mkdir($sessionDir);
ini_set('session.use_cookies', '0'); session_save_path($sessionDir); session_start();
$conn = pdo_get_connection();
$conn->exec('CREATE TEMPORARY TABLE review_taikhoan LIKE taikhoan');
$conn->exec('ALTER TABLE review_taikhoan RENAME TO taikhoan');
$stmt = $conn->prepare('INSERT INTO taikhoan (username, password, ho_ten, role) VALUES (?, ?, ?, ?)');
$stmt->execute(['role_admin', password_hash('AdminTest123!', PASSWORD_DEFAULT), 'Admin test', AccountRole::ADMIN]);
$actor = (int)$conn->lastInsertId();
$model = new TaiKhoan($conn);
$checks = 0;
function verifyRole($ok, $message) { global $checks; if (!$ok) throw new RuntimeException($message); $checks++; }
function rejectRole(callable $action) { try { $action(); } catch (DomainException $e) { return; } throw new RuntimeException('Expected role permission rejection.'); }
ob_start();
try {
    $customer = (int)$model->register('role_customer', 'Khách hàng', 'CustomerTest123!');
    verifyRole($model->getOne($customer)['role'] === AccountRole::CUSTOMER, 'Registration has elevated role');
    verifyRole($model->check_user('role_customer', 'CustomerTest123!') === false, 'Customer logged into admin');
    verifyRole($model->authenticate('role_customer', 'CustomerTest123!') !== false, 'Customer cannot log into storefront');
    rejectRole(fn() => $model->create('forged_admin', 'Test', 'CustomerTest123!', $customer, AccountRole::ADMIN));
    rejectRole(fn() => $model->update($customer, 'role_customer', 'Test', '', $customer, AccountRole::ADMIN));
    rejectRole(fn() => $model->setActive($actor, 0, $customer));
    verifyRole($model->getOne($customer)['role'] === AccountRole::CUSTOMER, 'Customer promoted self');
    $oldSession = $model->getOne($customer);
    $model->update($customer, 'role_customer', 'Khách được cấp quyền', '', $actor, AccountRole::ADMIN);
    verifyRole($model->getOne($customer)['role'] === AccountRole::ADMIN && $model->check_user('role_customer', 'CustomerTest123!') !== false, 'Admin could not grant role');
    verifyRole((int)$model->getOne($customer)['auth_version'] > (int)$oldSession['auth_version'], 'Role change did not revoke old session');
    $elevatedSession = $model->getOne($customer);
    $model->update($customer, 'role_customer', 'Khách hàng', '', $actor, AccountRole::CUSTOMER);
    $adminController = new TaiKhoanController(); $p = new ReflectionProperty(TaiKhoanController::class, 'model'); $p->setAccessible(true); $p->setValue($adminController, $model);
    $_SESSION['admin_login'] = $elevatedSession;
    verifyRole(!$adminController->validateSession(), 'Demoted admin session stayed valid');
    rejectRole(fn() => $model->update($actor, 'role_admin', 'Test', '', $actor, AccountRole::CUSTOMER));
    verifyRole($model->getOne($actor)['role'] === AccountRole::ADMIN, 'Current administrator lost role');
    $default = (int)$model->create('default_customer', 'Khách mặc định', 'CustomerTest123!', $actor);
    verifyRole($model->getOne($default)['role'] === AccountRole::CUSTOMER, 'Account default role is not customer');
    // Gửi role=admin trực tiếp tới controller đăng ký; không có quyền nâng vai trò.
    $_SESSION = []; $public = new AccountController($model);
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST = ['csrf_token' => $_SESSION['front_csrf'], 'username' => 'forged_signup', 'ho_ten' => 'Đăng ký thử', 'password' => 'CustomerTest123!', 'password_confirmation' => 'CustomerTest123!', 'role' => 'admin'];
    $public->register();
    verifyRole($_SESSION['user_login']['role'] === AccountRole::CUSTOMER, 'Forged signup role accepted');
    verifyRole($model->check_user('forged_signup', 'CustomerTest123!') === false, 'Forged signup can enter admin');
    $_SESSION['admin_login'] = $_SESSION['user_login'];
    verifyRole(!$adminController->validateSession(), 'Forged customer session bypassed admin gate');
    $model->setActive($_SESSION['user_login']['id'], 0, $actor);
    new AccountController($model);
    verifyRole(!isset($_SESSION['user_login']), 'Locked customer session stayed valid');
    // Mặc định ngay tại database khi không truyền cột role.
    $conn->exec("INSERT INTO taikhoan (username, password, ho_ten) VALUES ('database_default', 'test-only', 'Test')");
    verifyRole($model->getOne($conn->lastInsertId())['role'] === AccountRole::CUSTOMER, 'Database default is not customer');
} finally {
    ob_end_clean(); session_destroy();
    foreach (glob($sessionDir . '/sess_*') as $file) unlink($file);
    rmdir($sessionDir);
}
echo "PASS: $checks role, signup and session checks plus rejected customer privilege changes. Temporary table only.\n";
