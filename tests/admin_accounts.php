<?php
// Chỉ dùng bảng TEMPORARY, không sửa tài khoản thật.
chdir(dirname(__DIR__) . '/admin');
require 'Controller/TaiKhoanController.php';
$conn = pdo_get_connection();
$conn->exec('CREATE TEMPORARY TABLE review_taikhoan LIKE taikhoan');
$conn->exec('ALTER TABLE review_taikhoan RENAME TO taikhoan');
$stmt = $conn->prepare('INSERT INTO taikhoan (username, password, ho_ten, role) VALUES (?, ?, ?, ?)');
$stmt->execute(['test_admin', password_hash('ActorPass123!', PASSWORD_DEFAULT), 'Quản trị thử nghiệm', AccountRole::ADMIN]);
$actor = (int)$conn->lastInsertId();
$model = new TaiKhoan($conn);
$checks = 0;
function verifyAccount($condition, $message) {
    global $checks;
    if (!$condition) throw new RuntimeException($message);
    $checks++;
}
function rejectAccount(callable $action) {
    try { $action(); } catch (DomainException $e) { return; }
    throw new RuntimeException('Expected rejected account operation.');
}
ob_start();
$id = (int)$model->create('staff.test', 'Người quản trị', 'StaffPass123!', $actor, AccountRole::ADMIN);
$row = $conn->query('SELECT * FROM taikhoan WHERE id=' . $id)->fetch(PDO::FETCH_ASSOC);
verifyAccount($row['password'] !== 'StaffPass123!' && password_verify('StaffPass123!', $row['password']), 'Password stored without hash');
verifyAccount($model->check_user('staff.test', 'StaffPass123!') !== false, 'Login failed');
verifyAccount(!isset($model->getOne($id)['password']) && !isset($model->getAll()[0]['password']), 'Password exposed in public account data');
rejectAccount(fn() => $model->create('STAFF.TEST', 'Trùng tên', 'StaffPass123!', $actor));
rejectAccount(fn() => $model->create('bad username', 'Test', 'StaffPass123!', $actor));
rejectAccount(fn() => $model->create('shortpass', 'Test', '123', $actor));
verifyAccount((int)$conn->query('SELECT COUNT(*) FROM taikhoan')->fetchColumn() === 2, 'Rejected create persisted');
$model->update($id, 'staff.edited', 'Đã sửa tên', '', $actor);
verifyAccount($model->check_user('staff.edited', 'StaffPass123!') !== false, 'Empty password changed existing password');
$oldSession = $model->getOne($id);
$model->update($id, 'staff.edited', 'Đã sửa tên', 'ChangedPass123!', $actor);
verifyAccount($model->check_user('staff.edited', 'StaffPass123!') === false && $model->check_user('staff.edited', 'ChangedPass123!') !== false, 'Password change failed');
$controller = new TaiKhoanController();
$property = new ReflectionProperty(TaiKhoanController::class, 'model'); $property->setAccessible(true); $property->setValue($controller, $model);
$_SESSION = ['admin_login' => $oldSession];
verifyAccount(!$controller->validateSession(), 'Old password session still active');
$newSession = $model->getOne($id);
$model->setActive($id, 0, $actor);
$_SESSION = ['admin_login' => $newSession];
verifyAccount(!$controller->validateSession() && $model->check_user('staff.edited', 'ChangedPass123!') === false, 'Locked account still has access');
rejectAccount(fn() => $model->create('blocked.actor', 'Test', 'StaffPass123!', $id));
$model->setActive($id, 1, $actor);
$_SESSION = ['admin_login' => $newSession];
verifyAccount(!$controller->validateSession(), 'Unlock revived revoked session');
verifyAccount($model->check_user('staff.edited', 'ChangedPass123!') !== false, 'Unlock did not allow new login');
rejectAccount(fn() => $model->setActive($actor, 0, $actor));
$model->setActive($id, 0, $actor);
verifyAccount((int)$model->getOne($actor)['is_active'] === 1, 'Last active admin lost access');
$_SESSION = ['admin_login' => $model->getOne($actor), 'accounts_csrf' => str_repeat('a', 64)];
$_SERVER['REQUEST_METHOD'] = 'POST'; $_POST = ['csrf_token' => 'wrong'];
$controller->store();
verifyAccount(http_response_code() === 403 && (int)$conn->query('SELECT COUNT(*) FROM taikhoan')->fetchColumn() === 2, 'CSRF rejection failed');
$_POST = ['csrf_token' => str_repeat('a', 64), 'username' => 'from.form', 'ho_ten' => 'Tạo từ form', 'password' => 'FormPass123!'];
$controller->store();
verifyAccount($model->authenticate('from.form', 'FormPass123!') !== false, 'Valid controller create failed');
set_error_handler(function($severity, $message, $file, $line) { throw new ErrorException($message, 0, $severity, $file, $line); });
foreach (['index', 'create', 'edit'] as $method) {
    $_GET = ['action' => $method === 'index' ? 'listtaikhoan' : ($method === 'create' ? 'createtaikhoan' : 'edittaikhoan'), 'id' => $id];
    ob_start(); $controller->$method(); $html = ob_get_clean();
    verifyAccount(strpos($html, 'name="csrf_token"') !== false && strpos($html, 'ChangedPass123!') === false && strpos($html, 'StaffPass123!') === false, 'Account view has missing token or leaked password');
}
restore_error_handler();
$_SESSION = ['admin_login' => $model->getOne($actor)];
verifyAccount($controller->validateSession() && !isset($_SESSION['admin_login']['password']), 'Active session refresh failed');
ob_end_clean();
echo "PASS: $checks account/authentication checks plus rejected duplicate, invalid input, self-lock and inactive-actor operations. Temporary table only.\n";
