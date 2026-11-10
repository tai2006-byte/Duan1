<?php
class DatabaseExport {
    public static function write(PDO $conn, $path) {
        $sql = "-- Database dochoi\nSET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS=0;\n";
        foreach ($conn->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN) as $table) {
            $identifier = '`' . str_replace('`', '``', $table) . '`';
            $sql .= "\n" . $conn->query('SHOW CREATE TABLE ' . $identifier)->fetch(PDO::FETCH_NUM)[1] . ";\n";
            foreach ($conn->query('SELECT * FROM ' . $identifier)->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $columns = array_map(fn($name) => '`' . str_replace('`', '``', $name) . '`', array_keys($row));
                $values = array_map(fn($value) => $value === null ? 'NULL' : $conn->quote($value), array_values($row));
                $sql .= 'INSERT INTO ' . $identifier . ' (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $values) . ");\n";
            }
        }
        $sql .= "\nSET FOREIGN_KEY_CHECKS=1;\n";
        if (file_put_contents($path, $sql, LOCK_EX) === false) throw new RuntimeException('Không thể ghi tệp sao lưu SQL.');
    }
}
