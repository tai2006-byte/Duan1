<?php
require_once __DIR__ . '/../Model/TaiKhoan.php';
require_once __DIR__ . '/../../Support/RequestGuard.php';

class TaiKhoanController {
    private $model;
    public function __construct() { $this->model = new TaiKhoan(); }
    public function login($error_login = null) { include __DIR__ . '/../views/auth/login.php'; }
    public function checkLogin() {
        $result = $this->model->check_user($_POST['username'] ?? null, $_POST['password'] ?? null);
        if ($result) {
            session_regenerate_id(true);
            $_SESSION['admin_login'] = $result;
            unset($_SESSION['accounts_csrf']);
            header('Location: index.php');
            return;
        }
        $this->login('Sai tài khoản, mật khẩu hoặc tài khoản đã bị khóa.');
    }
    public function validateSession() {
        $session = $_SESSION['admin_login'] ?? null;
        $account = is_array($session) ? $this->model->getOne($session['id'] ?? null) : false;
        if (!$account || (int)$account['is_active'] !== 1 || $account['role'] !== AccountRole::ADMIN || (int)($session['auth_version'] ?? 0) !== (int)$account['auth_version']) {
            unset($_SESSION['admin_login'], $_SESSION['accounts_csrf']);
            return false;
        }
        $_SESSION['admin_login'] = $account;
        return true;
    }
    public function logout() {
        unset($_SESSION['admin_login'], $_SESSION['accounts_csrf']);
        session_regenerate_id(true);
        header('Location: index.php');
    }
    private function token() {
        if (empty($_SESSION['accounts_csrf'])) $_SESSION['accounts_csrf'] = bin2hex(random_bytes(32));
        return $_SESSION['accounts_csrf'];
    }
    private function validPost() {
        $provided = $_POST['csrf_token'] ?? null;
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !is_string($provided)
            || !hash_equals($this->token(), $provided)) {
            http_response_code(403);
            echo 'Yêu cầu không hợp lệ. Vui lòng tải lại trang và thử lại.';
            return false;
        }
        return true;
    }
    private function findAccount($value) {
        return RequestGuard::findRecord($this->model, $value, 'Không tìm thấy tài khoản.', 'index.php?action=listtaikhoan');
    }
    public function index() {
        $accounts = $this->model->getAll();
        $csrfToken = $this->token();
        $success = $_SESSION['account_success'] ?? null;
        $error = $_SESSION['account_error'] ?? null;
        unset($_SESSION['account_success'], $_SESSION['account_error']);
        include __DIR__ . '/../views/taikhoan/list.php';
    }
    public function create() {
        $editing = false; $account = []; $csrfToken = $this->token();
        include __DIR__ . '/../views/taikhoan/form.php';
    }
    public function edit() {
        $account = $this->findAccount($_GET['id'] ?? null);
        if (!$account) return;
        $editing = true; $csrfToken = $this->token();
        include __DIR__ . '/../views/taikhoan/form.php';
    }
    private function finish($message) {
        $_SESSION['account_success'] = $message;
        header('Location: index.php?action=listtaikhoan');
    }
    private function save($editing) {
        if (!$this->validPost()) return;
        $original = $editing ? $this->findAccount($_POST['id'] ?? null) : [];
        if ($editing && !$original) return;
        $username = is_string($_POST['username'] ?? null) ? trim($_POST['username']) : '';
        $name = is_string($_POST['ho_ten'] ?? null) ? trim($_POST['ho_ten']) : '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? AccountRole::CUSTOMER;
        try {
            $actor = (int)$_SESSION['admin_login']['id'];
            if ($editing) {
                $this->model->update($original['id'], $username, $name, $password, $actor, $role);
                if ((int)$original['id'] === $actor) {
                    $_SESSION['admin_login'] = $this->model->getOne($actor);
                    if ($password !== '') session_regenerate_id(true);
                }
            } else {
                $this->model->create($username, $name, $password, $actor, $role);
            }
            $this->finish($editing ? 'Đã cập nhật tài khoản.' : 'Đã thêm tài khoản.');
            return;
        } catch (DomainException $e) { $error = $e->getMessage(); }
        catch (Throwable $e) { error_log('Lưu tài khoản thất bại: ' . $e->getMessage()); $error = 'Không thể lưu tài khoản. Vui lòng thử lại.'; }
        http_response_code(422);
        $account = array_merge($original, ['username' => $username, 'ho_ten' => $name, 'role' => is_string($role) ? $role : AccountRole::CUSTOMER]);
        $csrfToken = $this->token();
        include __DIR__ . '/../views/taikhoan/form.php';
    }
    public function store() { $this->save(false); }
    public function update() { $this->save(true); }
    public function setActive() {
        if (!$this->validPost()) return;
        $account = $this->findAccount($_POST['id'] ?? null);
        if (!$account) return;
        $active = filter_var($_POST['is_active'] ?? null, FILTER_VALIDATE_INT);
        try {
            $this->model->setActive($account['id'], $active, (int)$_SESSION['admin_login']['id']);
            $this->finish($active === 1 ? 'Đã mở khóa tài khoản.' : 'Đã khóa tài khoản.');
        } catch (DomainException $e) { $_SESSION['account_error'] = $e->getMessage(); header('Location: index.php?action=listtaikhoan'); }
        catch (Throwable $e) { error_log('Khóa tài khoản thất bại: ' . $e->getMessage()); $_SESSION['account_error'] = 'Không thể cập nhật tài khoản. Vui lòng thử lại.'; header('Location: index.php?action=listtaikhoan'); }
    }
}
