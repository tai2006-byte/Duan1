<?php
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
require_once __DIR__ . '/../Model/pdo.php';
$conn = pdo_get_connection();
// Migration lịch sử: đã được thay thế sau khi gộp bảng tài khoản.
$tables = $conn->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
if (!in_array('admin', $tables, true) && in_array('taikhoan', $tables, true)) {
    echo "Database đã dùng bảng taikhoan chung; bỏ qua migration cũ.\n";
    exit;
}

$exists = $conn->query("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='admin' AND COLUMN_NAME='role'")->fetchColumn();
if (!$exists) {
    // Tài khoản cũ đều là quản trị; mọi tài khoản tạo sau mặc định khách hàng.
    $conn->exec("ALTER TABLE admin ADD COLUMN role ENUM('admin','customer') NOT NULL DEFAULT 'customer'");
    $conn->exec("UPDATE admin SET role='admin'");
}
$dump = file_get_contents(__DIR__ . '/../dochoi.sql');
$schema = $conn->query('SHOW CREATE TABLE admin')->fetch(PDO::FETCH_NUM)[1];
$block = $schema . ";\n";
foreach ($conn->query('SELECT * FROM admin')->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $block .= 'INSERT INTO `admin` (`' . implode('`,`', array_keys($row)) . '`) VALUES ('
        . implode(',', array_map(fn($v) => $v === null ? 'NULL' : $conn->quote($v), array_values($row))) . ");\n";
}
$start = strpos($dump, 'CREATE TABLE `admin`'); $end = strpos($dump, 'CREATE TABLE `binh_luan`');
if ($start === false || $end === false) throw new RuntimeException('Không tìm thấy cấu trúc admin trong tệp SQL.');
if (file_put_contents(__DIR__ . '/../dochoi.sql', substr($dump, 0, $start) . $block . "\n" . substr($dump, $end), LOCK_EX) === false) throw new RuntimeException('Không thể cập nhật SQL.');
echo "Roles ready: existing administrators preserved; new accounts default to customer. SQL updated.\n";
