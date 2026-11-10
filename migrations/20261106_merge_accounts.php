<?php
// CLI; chạy khi không có thao tác ghi tài khoản. DDL không thể rollback, nên sao lưu trước.
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
require_once __DIR__ . '/../Model/pdo.php';
require_once __DIR__ . '/../Support/DatabaseExport.php';
$conn = pdo_get_connection();
$tables = $conn->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
$hasAdmin = in_array('admin', $tables, true);
DatabaseExport::write($conn, __DIR__ . '/../backups/dochoi_before_account_merge_' . gmdate('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.sql');
if ($hasAdmin) {
    $before = $conn->query('SELECT * FROM admin ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
    if (in_array('taikhoan', $tables, true)) {
        // Đã kiểm tra database dự án: bảng tài khoản cũ không có bản ghi.
        if ((int)$conn->query('SELECT COUNT(*) FROM taikhoan')->fetchColumn() !== 0) {
            throw new RuntimeException('Bảng taikhoan cũ có dữ liệu. Migration dừng để tránh mất hoặc trùng tài khoản.');
        }
        $refs = $conn->query("SELECT TABLE_NAME, CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=DATABASE() AND REFERENCED_TABLE_NAME='taikhoan'")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($refs as $ref) {
            $conn->exec('ALTER TABLE `' . str_replace('`', '``', $ref['TABLE_NAME']) . '` DROP FOREIGN KEY `' . str_replace('`', '``', $ref['CONSTRAINT_NAME']) . '`');
        }
        $conn->exec('DROP TABLE taikhoan');
    }
    $conn->exec('RENAME TABLE admin TO taikhoan');
    if ($before !== $conn->query('SELECT * FROM taikhoan ORDER BY id')->fetchAll(PDO::FETCH_ASSOC)) {
        throw new RuntimeException('Dữ liệu tài khoản sau khi đổi tên không khớp. Hãy kiểm tra bản sao lưu.');
    }
}
// Xác nhận đây là bảng tài khoản chung mới, không phải cấu trúc cũ.
$columns = $conn->query('SHOW COLUMNS FROM taikhoan')->fetchAll(PDO::FETCH_COLUMN);
foreach (['id', 'username', 'password', 'role', 'is_active', 'auth_version'] as $column) {
    if (!in_array($column, $columns, true)) throw new RuntimeException('Cấu trúc tài khoản chưa hợp lệ.');
}
$oldIndex = $conn->query("SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='taikhoan' AND INDEX_NAME='admin_username_unique'")->fetchColumn();
if ($oldIndex) $conn->exec('ALTER TABLE taikhoan RENAME INDEX admin_username_unique TO taikhoan_username_unique');
foreach (['binh_luan' => 'binh_luan_ibfk_1', 'gio_hang' => 'gio_hang_ibfk_2'] as $table => $constraint) {
    $check = $conn->prepare('SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=? AND CONSTRAINT_NAME=? AND REFERENCED_TABLE_NAME=\'taikhoan\'');
    $check->execute([$table, $constraint]);
    if (!$check->fetchColumn()) {
        $conn->exec('ALTER TABLE `' . $table . '` ADD CONSTRAINT `' . $constraint . '` FOREIGN KEY (id_taikhoan) REFERENCES taikhoan(id)');
    }
}
DatabaseExport::write($conn, __DIR__ . '/../dochoi.sql');
echo "Merged into taikhoan; account data preserved, foreign keys updated, dochoi.sql exported.\n";
