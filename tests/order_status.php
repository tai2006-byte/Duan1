<?php
// Kiểm tra 25 cặp trạng thái và các lựa chọn trên form bằng bảng tạm.
chdir(dirname(__DIR__) . '/admin');
require 'Model/HoaDon.php';
$conn = pdo_get_connection();
foreach (['hoadon', 'chi_tiet_hoa_don', 'san_pham'] as $table) {
    $conn->exec('CREATE TEMPORARY TABLE review_' . $table . ' LIKE ' . $table);
    if ($table === 'san_pham') $conn->exec('INSERT INTO review_san_pham SELECT * FROM san_pham LIMIT 1');
    $conn->exec('ALTER TABLE review_' . $table . ' RENAME TO ' . $table);
}
$product = $conn->query('SELECT id_sp FROM san_pham LIMIT 1')->fetchColumn();
if ($product === false) throw new RuntimeException('Need a product fixture.');
$conn->exec("INSERT INTO hoadon (ho_ten, sdt, dia_chi, ngay_dat, tongtien, hinh_thuc_thanh_toan, trang_thai, da_tru_kho) VALUES ('Test', '0900000000', 'Test', NOW(), 200000, 'COD', 'Chờ xác nhận', 1)");
$id = $conn->lastInsertId();
$stmt = $conn->prepare('INSERT INTO chi_tiet_hoa_don (id_hoadon, id_sanpham, gia, so_luong) VALUES (?, ?, 100000, 2)');
$stmt->execute([$id, $product]);
$model = new HoaDon($conn);
$reset = $conn->prepare('UPDATE hoadon SET trang_thai=?, da_tru_kho=1, ly_do_huy=NULL WHERE id_hoadon=?');
$checks = 0;
foreach (OrderStatus::all() as $from) {
    foreach (OrderStatus::all() as $to) {
        $reset->execute([$from, $id]);
        $conn->exec('UPDATE san_pham SET so_luong=10');
        $expected = $from === $to || in_array($to, OrderStatus::next($from), true);
        $accepted = true;
        try { $model->updateStatus($id, $to, 'Test cancellation reason'); } catch (DomainException $e) { $accepted = false; }
        if ($accepted !== $expected) throw new RuntimeException('Invalid transition: ' . $from . ' -> ' . $to);
        $row = $conn->query('SELECT trang_thai, da_tru_kho, ly_do_huy FROM hoadon')->fetch(PDO::FETCH_ASSOC);
        $stock = (int)$conn->query('SELECT so_luong FROM san_pham')->fetchColumn();
        $cancelled = in_array($from, [OrderStatus::PENDING, OrderStatus::SHIPPING], true) && $to === OrderStatus::CANCELLED;
        if ($row['trang_thai'] !== ($expected ? $to : $from) || $stock !== ($cancelled ? 12 : 10)
            || (int)$row['da_tru_kho'] !== ($cancelled ? 0 : 1) || $conn->inTransaction()) {
            throw new RuntimeException('State or stock mismatch: ' . $from . ' -> ' . $to);
        }
        if ($cancelled && $row['ly_do_huy'] !== 'Test cancellation reason') throw new RuntimeException('Cancellation reason not saved');
        $checks++;
    }
}
// Lý do bắt buộc: không được lưu trạng thái hoặc hoàn kho khi dữ liệu sai.
foreach ([OrderStatus::PENDING, OrderStatus::SHIPPING] as $state) {
    foreach (['', " \n\t ", [], str_repeat('a', 1001)] as $reason) {
        $reset->execute([$state, $id]);
        $conn->exec('UPDATE san_pham SET so_luong=10');
        $rejected = false;
        try { $model->updateStatus($id, OrderStatus::CANCELLED, $reason); } catch (DomainException $e) { $rejected = true; }
        $row = $conn->query('SELECT trang_thai, ly_do_huy FROM hoadon')->fetch(PDO::FETCH_ASSOC);
        if (!$rejected || $row['trang_thai'] !== $state || $row['ly_do_huy'] !== null
            || (int)$conn->query('SELECT so_luong FROM san_pham')->fetchColumn() !== 10) {
            throw new RuntimeException('Invalid cancellation reason changed data.');
        }
        $checks++;
    }
}
$reset->execute([OrderStatus::SHIPPING, $id]);
$model->updateStatus($id, OrderStatus::CANCELLED, 'Không giao được hàng cho khách');
$model->updateStatus($id, OrderStatus::CANCELLED, 'Không được ghi đè lý do lần đầu');
$row = $conn->query('SELECT ly_do_huy FROM hoadon')->fetch(PDO::FETCH_ASSOC);
if ($row['ly_do_huy'] !== 'Không giao được hàng cho khách' || (int)$conn->query('SELECT so_luong FROM san_pham')->fetchColumn() !== 12) {
    throw new RuntimeException('Repeated cancellation changed reason or restored twice.');
}
$checks++;
$_SESSION = [];
$_GET = ['action' => 'hoadon_detail'];
$orderDetails = [];
$baseOrder = $conn->query('SELECT * FROM hoadon')->fetch(PDO::FETCH_ASSOC);
set_error_handler(function($severity, $message, $file, $line) { throw new ErrorException($message, 0, $severity, $file, $line); });
foreach (OrderStatus::all() as $state) {
    $order = array_merge($baseOrder, ['trang_thai' => $state]);
    ob_start(); include 'views/hoadon/detail.php'; $html = ob_get_clean();
    $dom = new DOMDocument();
    libxml_use_internal_errors(true); $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html); libxml_clear_errors();
    $xpath = new DOMXPath($dom);
    $actual = [];
    foreach ($xpath->query('//select[@name="trang_thai"]/option') as $option) $actual[] = $option->getAttribute('value');
    $next = OrderStatus::next($state);
    if ($actual !== ($next ? array_merge([$state], $next) : [])) throw new RuntimeException('UI transition mismatch: ' . $state);
    $checks++;
}
restore_error_handler();
echo "PASS: $checks transition, stock and UI checks. Temporary tables only.\n";
