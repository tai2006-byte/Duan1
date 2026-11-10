<?php
require_once __DIR__ . '/pdo.php';
require_once __DIR__ . '/../Support/AccountRole.php';

class TaiKhoan {
    private $connection;
    public function __construct(?PDO $connection = null) { $this->connection = $connection; }
    private function conn() { return $this->connection ?? ($this->connection = pdo_get_connection()); }
    private function publicFields() { return 'id, username, ho_ten, is_active, auth_version, role'; }

    public function getAll() {
        return $this->conn()->query('SELECT ' . $this->publicFields() . ' FROM taikhoan ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getOne($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($id === false) return false;
        $stmt = $this->conn()->prepare('SELECT ' . $this->publicFields() . ' FROM taikhoan WHERE id=?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function authenticate($user, $pass) {
        if (!is_string($user) || !is_string($pass)) return false;
        $stmt = $this->conn()->prepare('SELECT * FROM taikhoan WHERE username=? AND is_active=1');
        $stmt->execute([trim($user)]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row || !password_verify($pass, $row['password'])) return false;
        if (password_needs_rehash($row['password'], PASSWORD_DEFAULT)) {
            $update = $this->conn()->prepare('UPDATE taikhoan SET password=? WHERE id=? AND password=?');
            $update->execute([password_hash($pass, PASSWORD_DEFAULT), $row['id'], $row['password']]);
        }
        unset($row['password']);
        return $row;
    }
    public function check_user($user, $pass) {
        $account = $this->authenticate($user, $pass);
        return $account && $account['role'] === AccountRole::ADMIN ? $account : false;
    }
    private function validateRole($role) {
        if (!is_string($role) || !array_key_exists($role, AccountRole::labels())) throw new DomainException('Vai trò tài khoản không hợp lệ.');
    }
    // Đăng ký công khai không nhận tham số vai trò, luôn gán khách hàng tại server.
    public function register($username, $name, $password) {
        $this->validate($username, $name);
        $hash = $this->hashPassword($password);
        return $this->transaction(function() use ($username, $name, $hash) {
            $stmt = $this->conn()->prepare("INSERT INTO taikhoan (username, ho_ten, password, role, is_active, auth_version) VALUES (?, ?, ?, 'customer', 1, 0)");
            $stmt->execute([$username, trim($name), $hash]);
            return $this->conn()->lastInsertId();
        });
    }
    private function validate($username, $name) {
        if (!is_string($username) || !preg_match('/^[A-Za-z0-9_.-]{3,50}$/D', $username)) {
            throw new DomainException('Tên đăng nhập gồm 3–50 ký tự: chữ không dấu, số, dấu chấm, gạch dưới hoặc gạch ngang.');
        }
        if (!is_string($name) || trim($name) === '' || mb_strlen($name, 'UTF-8') > 100) {
            throw new DomainException('Vui lòng nhập họ tên, tối đa 100 ký tự.');
        }
    }
    private function hashPassword($password) {
        if (!is_string($password) || mb_strlen($password, 'UTF-8') < 8 || strlen($password) > 72) {
            throw new DomainException('Mật khẩu phải có ít nhất 8 ký tự và không được quá dài.');
        }
        return password_hash($password, PASSWORD_DEFAULT);
    }
    // Khóa các dòng tài khoản khi thay đổi để bảo vệ tài khoản cuối cùng.
    private function lockAccounts($actorId) {
        $rows = $this->conn()->query('SELECT id, is_active, role FROM taikhoan ORDER BY id FOR UPDATE')->fetchAll(PDO::FETCH_ASSOC);
        $accounts = array_column($rows, null, 'id');
        if (!isset($accounts[$actorId]) || (int)$accounts[$actorId]['is_active'] !== 1 || $accounts[$actorId]['role'] !== AccountRole::ADMIN) {
            throw new DomainException('Chỉ quản trị viên đang hoạt động được quản lý tài khoản và vai trò.');
        }
        return $accounts;
    }
    private function transaction(callable $action) {
        $conn = $this->conn();
        $conn->beginTransaction();
        try { $result = $action(); $conn->commit(); return $result; }
        catch (Throwable $e) {
            if ($conn->inTransaction()) $conn->rollBack();
            if ($e instanceof PDOException && $e->getCode() === '23000') throw new DomainException('Tên đăng nhập đã tồn tại.');
            throw $e;
        }
    }
    public function create($username, $name, $password, $actorId, $role = AccountRole::CUSTOMER) {
        $this->validateRole($role);
        $this->validate($username, $name);
        $hash = $this->hashPassword($password);
        return $this->transaction(function() use ($username, $name, $hash, $actorId, $role) {
            $this->lockAccounts($actorId);
            $stmt = $this->conn()->prepare('INSERT INTO taikhoan (username, ho_ten, password, is_active, auth_version, role) VALUES (?, ?, ?, 1, 0, ?)');
            $stmt->execute([$username, trim($name), $hash, $role]);
            return $this->conn()->lastInsertId();
        });
    }
    public function update($id, $username, $name, $password, $actorId, $role = null) {
        $this->validate($username, $name);
        if ($role !== null) $this->validateRole($role);
        $hash = $password === '' ? null : $this->hashPassword($password);
        $this->transaction(function() use ($id, $username, $name, $hash, $actorId, $role) {
            $accounts = $this->lockAccounts($actorId);
            if (!isset($accounts[$id])) throw new DomainException('Không tìm thấy tài khoản.');
            $newRole = $role ?? $accounts[$id]['role'];
            $changedRole = $newRole !== $accounts[$id]['role'];
            if ((int)$id === (int)$actorId && $changedRole) throw new DomainException('Không thể đổi vai trò của tài khoản đang đăng nhập.');
            $activeAdmins = count(array_filter($accounts, fn($row) => (int)$row['is_active'] === 1 && $row['role'] === AccountRole::ADMIN));
            if ($changedRole && $accounts[$id]['role'] === AccountRole::ADMIN && (int)$accounts[$id]['is_active'] === 1 && $activeAdmins <= 1) {
                throw new DomainException('Cần giữ ít nhất một quản trị viên đang hoạt động.');
            }
            $versionChange = $hash !== null || $changedRole ? 1 : 0;
            if ($hash !== null) {
                $stmt = $this->conn()->prepare('UPDATE taikhoan SET username=?, ho_ten=?, password=?, role=?, auth_version=auth_version+? WHERE id=?');
                $stmt->execute([$username, trim($name), $hash, $newRole, $versionChange, $id]);
            } else {
                $stmt = $this->conn()->prepare('UPDATE taikhoan SET username=?, ho_ten=?, role=?, auth_version=auth_version+? WHERE id=?');
                $stmt->execute([$username, trim($name), $newRole, $versionChange, $id]);
            }
        });
    }
    public function setActive($id, $active, $actorId) {
        if (!in_array($active, [0, 1], true)) throw new DomainException('Trạng thái tài khoản không hợp lệ.');
        $this->transaction(function() use ($id, $active, $actorId) {
            $accounts = $this->lockAccounts($actorId);
            if (!isset($accounts[$id])) throw new DomainException('Không tìm thấy tài khoản.');
            if ($active === 0 && (int)$id === (int)$actorId) throw new DomainException('Không thể khóa tài khoản đang đăng nhập.');
            if ((int)$accounts[$id]['is_active'] === $active) return;
            $activeCount = count(array_filter($accounts, fn($row) => (int)$row['is_active'] === 1 && $row['role'] === AccountRole::ADMIN));
            if ($active === 0 && $accounts[$id]['role'] === AccountRole::ADMIN && $activeCount <= 1) throw new DomainException('Cần giữ ít nhất một tài khoản quản trị đang hoạt động.');
            $stmt = $this->conn()->prepare('UPDATE taikhoan SET is_active=?, auth_version=auth_version+1 WHERE id=?');
            $stmt->execute([$active, $id]);
        });
    }
}
