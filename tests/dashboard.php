<?php
// Kiểm tra bằng bảng tạm, không thay đổi dữ liệu thật.
require __DIR__ . '/../admin/Model/Dashboard.php';
$conn = pdo_get_connection();
foreach (['hoadon', 'chi_tiet_hoa_don', 'san_pham', 'danh_muc'] as $table) {
    $conn->exec('CREATE TEMPORARY TABLE review_' . $table . ' LIKE ' . $table);
    if (in_array($table, ['san_pham', 'danh_muc'], true)) {
        $conn->exec('INSERT INTO review_' . $table . ' SELECT * FROM ' . $table);
    }
    $conn->exec('ALTER TABLE review_' . $table . ' RENAME TO ' . $table);
}
$checks = 0;
function verifyDashboard($ok, $message) {
    global $checks;
    if (!$ok) throw new RuntimeException($message);
    $checks++;
}
$model = new Dashboard($conn);
$empty = $model->getSummary('2026-10-08', '2026-11-07', '2026-09-08');
verifyDashboard((int)$empty['orders'] === 0 && (float)$empty['revenue'] === 0.0, 'Empty summary');
verifyDashboard($model->getRecentOrders() === [] && $model->getOrderStatuses() === [], 'Empty orders');
verifyDashboard($model->getBestSellers('2026-10-08', '2026-11-07') === [], 'Empty bestsellers');
$product = $conn->query('SELECT id_sp FROM san_pham LIMIT 1')->fetchColumn();
if ($product === false) throw new RuntimeException('Need one product fixture.');
$conn->exec('UPDATE san_pham SET trang_thai=0 WHERE id_sp=' . (int)$product);
$insert = $conn->prepare('INSERT INTO hoadon (ho_ten, trang_thai, tongtien, ngay_dat) VALUES (?, ?, ?, ?)');
$detail = $conn->prepare('INSERT INTO chi_tiet_hoa_don (id_hoadon, id_sanpham, gia, so_luong) VALUES (?, ?, ?, ?)');
foreach ([
    ['Hoàn thành', 200000, '2026-10-08 00:00:00', 2],
    ['Hoàn thành', 300000, '2026-11-06 23:59:59', 3],
    ['Hoàn thành', 100000, '2026-09-08 00:00:00', 1],
    ['Hủy đơn', 900000, '2026-11-06 12:00:00', 9],
    ['Chờ xác nhận', 700000, '2026-11-06 13:00:00', 7],
    ['Hoàn thành', 800000, '2026-11-07 00:00:00', 8]
] as [$status, $amount, $date, $quantity]) {
    $insert->execute(['Test', $status, $amount, $date]);
    $detail->execute([$conn->lastInsertId(), $product, 100000, $quantity]);
}
$summary = $model->getSummary('2026-10-08', '2026-11-07', '2026-09-08');
verifyDashboard((int)$summary['orders'] === 6, 'All-time order count');
verifyDashboard((float)$summary['revenue'] === 500000.0, 'Current revenue boundaries or statuses');
verifyDashboard((float)$summary['previous_revenue'] === 100000.0, 'Previous revenue boundaries');
verifyDashboard((int)$summary['products'] === (int)$conn->query('SELECT COUNT(*) FROM san_pham WHERE trang_thai=1')->fetchColumn(), 'Active product count');
$daily = $model->getDailyRevenue('2026-10-08', '2026-11-07');
verifyDashboard(count($daily) === 2 && (float)array_sum(array_column($daily, 'revenue')) === 500000.0, 'Daily revenue');
$best = $model->getBestSellers('2026-10-08', '2026-11-07');
verifyDashboard(count($best) === 1 && (int)$best[0]['sold'] === 5, 'Historical hidden-product sales');
verifyDashboard(count($model->getRecentOrders()) === 5, 'Recent order limit');
verifyDashboard((int)array_sum(array_column($model->getOrderStatuses(), 'total')) === 6, 'Status totals');
echo "PASS: $checks dashboard data checks. Temporary tables only.\n";
