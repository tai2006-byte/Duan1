<?php
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
require_once __DIR__ . '/../admin/Model/pdo.php';
$conn = pdo_get_connection();
// Migration lịch sử: đã được thay thế sau khi gộp bảng tài khoản.
$tables = $conn->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
if (!in_array('admin', $tables, true) && in_array('taikhoan', $tables, true)) {
    echo "Database đã dùng bảng taikhoan chung; bỏ qua migration cũ.\n";
    exit;
}

$schema = $conn->query('SHOW CREATE TABLE admin')->fetch(PDO::FETCH_NUM)[1];
$rows = $conn->query('SELECT * FROM admin')->fetchAll(PDO::FETCH_ASSOC);
$sql = $schema . ";\n";
foreach ($rows as $row) {
    $sql .= 'INSERT INTO admin (`' . implode('`,`', array_keys($row)) . '`) VALUES ('
        . implode(',', array_map(fn($v) => $v === null ? 'NULL' : $conn->quote($v), array_values($row))) . ");\n";
}
$backup = __DIR__ . '/../backups/admin_before_management_' . gmdate('Ymd_His') . '.sql';
if (file_put_contents($backup, $sql, LOCK_EX) === false) throw new RuntimeException('Không thể sao lưu.');
foreach (['is_active' => 'TINYINT NOT NULL DEFAULT 1', 'auth_version' => 'INT NOT NULL DEFAULT 0'] as $column => $definition) {
    $check = $conn->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='admin' AND COLUMN_NAME=?");
    $check->execute([$column]);
    if (!$check->fetchColumn()) $conn->exec('ALTER TABLE admin ADD COLUMN `' . $column . '` ' . $definition);
}
$conn->exec('ALTER TABLE admin MODIFY password VARCHAR(255) NOT NULL');
$duplicate = $conn->query('SELECT username FROM admin GROUP BY username HAVING COUNT(*) > 1 LIMIT 1')->fetchColumn();
if ($duplicate !== false) throw new RuntimeException('Có tên đăng nhập trùng, hãy xử lý trước khi tạo chỉ mục duy nhất.');
$index = $conn->query("SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='admin' AND INDEX_NAME='admin_username_unique'")->fetchColumn();
if (!$index) $conn->exec('ALTER TABLE admin ADD UNIQUE KEY admin_username_unique (username)');
$conn->beginTransaction();
try {
    $update = $conn->prepare('UPDATE admin SET password=? WHERE id=?');
    foreach ($conn->query('SELECT id, password FROM admin')->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (empty(password_get_info($row['password'])['algo'])) $update->execute([password_hash($row['password'], PASSWORD_DEFAULT), $row['id']]);
    }
    $conn->commit();
} catch (Throwable $e) { if ($conn->inTransaction()) $conn->rollBack(); throw $e; }
// Cập nhật phần bảng admin trong tệp cài đặt, không in mật khẩu ra màn hình.
$schema = $conn->query('SHOW CREATE TABLE admin')->fetch(PDO::FETCH_NUM)[1];
$block = $schema . ";\n";
foreach ($conn->query('SELECT * FROM admin')->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $block .= 'INSERT INTO `admin` (`' . implode('`,`', array_keys($row)) . '`) VALUES ('
        . implode(',', array_map(fn($v) => $v === null ? 'NULL' : $conn->quote($v), array_values($row))) . ");\n";
}
$dumpPath = __DIR__ . '/../dochoi.sql';
$dump = file_get_contents($dumpPath);
$start = strpos($dump, 'CREATE TABLE `admin`');
$end = strpos($dump, 'CREATE TABLE `binh_luan`');
if ($start === false || $end === false) throw new RuntimeException('Không tìm thấy phần admin trong dochoi.sql.');
if (file_put_contents($dumpPath, substr($dump, 0, $start) . $block . "\n" . substr($dump, $end), LOCK_EX) === false) throw new RuntimeException('Không thể cập nhật dochoi.sql.');
echo "Admin account schema and password hashes updated. Existing passwords still work. Backup saved.\n";
