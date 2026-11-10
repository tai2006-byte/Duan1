<?php
require_once __DIR__ . '/../Model/TaiKhoan.php';

class AccountController {
    private $model;
    public function __construct(?TaiKhoan $model = null) {
        $this->model = $model ?? new TaiKhoan();
        if (isset($_SESSION['user_login'])) {
            $old = $_SESSION['user_login'];
            $account = is_array($old) ? $this->model->getOne($old['id'] ?? null) : false;
            if (!$account || (int)$account['is_active'] !== 1 || (int)$account['auth_version'] !== (int)($old['auth_version'] ?? 0)) {
                unset($_SESSION['user_login']);
            } else $_SESSION['user_login'] = $account;
        }
        if (empty($_SESSION['front_csrf'])) $_SESSION['front_csrf'] = bin2hex(random_bytes(32));
    }
    private function validPost() {
        $token = $_POST['csrf_token'] ?? null;
        return is_string($token) && hash_equals($_SESSION['front_csrf'], $token);
    }
    private function show($mode, $error = null) {
        $csrfToken = $_SESSION['front_csrf'];
        include __DIR__ . '/../views/account/form.php';
    }
    public function register() { $this->handle('register'); }
    public function login() { $this->handle('login'); }
    private function handle($mode) {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') { $this->show($mode); return; }
        if (!$this->validPost()) {
            http_response_code(403);
            $this->show($mode, 'Phiên biểu mẫu không hợp lệ. Vui lòng tải lại trang.');
            return;
        }
        $username = is_string($_POST['username'] ?? null) ? trim($_POST['username']) : '';
        $password = $_POST['password'] ?? null;
        try {
            if ($mode === 'register') {
                $confirm = $_POST['password_confirmation'] ?? null;
                if (!is_string($password) || !is_string($confirm) || $password !== $confirm) throw new DomainException('Mật khẩu nhập lại chưa khớp.');
                $name = is_string($_POST['ho_ten'] ?? null) ? trim($_POST['ho_ten']) : '';
                // Không đọc role từ POST: đăng ký công khai luôn là khách hàng.
                $id = $this->model->register($username, $name, $password);
                $account = $this->model->getOne($id);
            } else {
                $account = $this->model->authenticate($username, $password);
                if (!$account) throw new DomainException('Sai tài khoản, mật khẩu hoặc tài khoản đã bị khóa.');
            }
            session_regenerate_id(true);
            $_SESSION['user_login'] = $account;
            $_SESSION['front_csrf'] = bin2hex(random_bytes(32));
            header('Location: index.php');
            return;
        } catch (DomainException $e) { $error = $e->getMessage(); }
        catch (Throwable $e) { error_log('Tài khoản khách hàng: ' . $e->getMessage()); $error = 'Không thể xử lý tài khoản. Vui lòng thử lại.'; }
        http_response_code(422);
        $this->show($mode, $error);
    }
    public function logout() {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !$this->validPost()) {
            http_response_code(403);
            echo 'Yêu cầu không hợp lệ.';
            return;
        }
        unset($_SESSION['user_login']);
        $_SESSION['front_csrf'] = bin2hex(random_bytes(32));
        session_regenerate_id(true);
        header('Location: index.php');
    }
}
