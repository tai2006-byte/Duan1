<?php
// Chạy bằng CLI; có thể chạy lại. Luôn sao lưu trước khi đổi cấu trúc.
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Chỉ chạy migration từ dòng lệnh.');
}
require_once __DIR__ . '/../Model/pdo.php';
$conn = pdo_get_connection();

function exportToyDatabase(PDO $conn) {
    $sql = "-- Database dochoi\nSET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS=0;\n";
    $tables = $conn->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        $identifier = '`' . str_replace('`', '``', $table) . '`';
        $schema = $conn->query('SHOW CREATE TABLE ' . $identifier)->fetch(PDO::FETCH_NUM);
        $sql .= "\n" . $schema[1] . ";\n";
        foreach ($conn->query('SELECT * FROM ' . $identifier, PDO::FETCH_ASSOC) as $row) {
            $columns = array_map(fn($name) => '`' . str_replace('`', '``', $name) . '`', array_keys($row));
            $values = array_map(fn($value) => $value === null ? 'NULL' : $conn->quote($value), array_values($row));
            $sql .= 'INSERT INTO ' . $identifier . ' (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $values) . ");\n";
        }
    }
    return $sql . "\nSET FOREIGN_KEY_CHECKS=1;\n";
}

function toyDataFingerprint(PDO $conn) {
    $queries = [
        'SELECT id_sp, hinh_anh, ten_sp, gia_sp, so_luong, loai, id_danh_muc, Mo_ta, trang_thai, luot_xem FROM san_pham ORDER BY id_sp',
        'SELECT id_cthd, id_hoadon, id_sanpham, gia, so_luong FROM chi_tiet_hoa_don ORDER BY id_cthd',
        'SELECT * FROM hoadon ORDER BY id_hoadon'
    ];
    $rows = [];
    foreach ($queries as $query) $rows[] = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
    return hash('sha256', serialize($rows));
}

$before = toyDataFingerprint($conn);
$backup = __DIR__ . '/../backups/dochoi_before_variants_' . gmdate('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.sql';
if (file_put_contents($backup, exportToyDatabase($conn), LOCK_EX) === false) {
    throw new RuntimeException('Không thể sao lưu, migration đã dừng.');
}
foreach (['san_pham' => ['id_mau_sac', 'id_kich_co'], 'chi_tiet_hoa_don' => ['mau', 'size']] as $table => $columns) {
    foreach ($columns as $column) {
        $check = $conn->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=? AND COLUMN_NAME=?');
        $check->execute([$table, $column]);
        if (!$check->fetchColumn()) continue;
        $constraints = $conn->prepare('SELECT DISTINCT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=? AND COLUMN_NAME=? AND REFERENCED_TABLE_NAME IS NOT NULL');
        $constraints->execute([$table, $column]);
        foreach ($constraints->fetchAll(PDO::FETCH_COLUMN) as $name) {
            $conn->exec('ALTER TABLE `' . $table . '` DROP FOREIGN KEY `' . str_replace('`', '``', $name) . '`');
        }
        $conn->exec('ALTER TABLE `' . $table . '` DROP COLUMN `' . $column . '`');
    }
}
$conn->exec('DROP TABLE IF EXISTS mau_sac, kich_co');
if (toyDataFingerprint($conn) !== $before) throw new RuntimeException('Dữ liệu chính không khớp với bản trước migration. Hãy kiểm tra bản sao lưu.');
if (file_put_contents(__DIR__ . '/../dochoi.sql', exportToyDatabase($conn), LOCK_EX) === false) {
    throw new RuntimeException('Đã đổi cấu trúc nhưng không thể xuất dochoi.sql.');
}
echo "Removed product attributes; product, stock and invoice data verified unchanged.\n";
echo 'Backup: ' . basename($backup) . "\nImport file: dochoi.sql\n";
