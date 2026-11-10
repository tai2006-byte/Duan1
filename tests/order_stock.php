<?php
// Chạy từ CLI. Chỉ ghi vào bảng TEMPORARY, không sửa sản phẩm hay đơn thật.
chdir(dirname(__DIR__) . '/admin');
require 'Model/HoaDon.php';
require '../Model/CartModel.php';

$checks = 0;
function verifyStock($ok, $message) {
    global $checks;
    if (!$ok) throw new RuntimeException($message);
    $checks++;
}
function expectStockFailure(callable $action, $class = DomainException::class) {
    try { $action(); } catch (Throwable $e) {
        if ($e instanceof $class) return;
        throw $e;
    }
    throw new RuntimeException('Expected failure was not raised.');
}

$conn = pdo_get_connection();
$conn->exec("SET SESSION sql_mode = 'STRICT_ALL_TABLES'");
foreach (['hoadon', 'chi_tiet_hoa_don', 'san_pham'] as $table) {
    $conn->exec('CREATE TEMPORARY TABLE review_' . $table . ' LIKE ' . $table);
    if ($table === 'san_pham') {
        $conn->exec('INSERT INTO review_san_pham SELECT * FROM san_pham ORDER BY id_sp LIMIT 2');
    }
    $conn->exec('ALTER TABLE review_' . $table . ' RENAME TO ' . $table);
}
$ids = $conn->query('SELECT id_sp FROM san_pham ORDER BY id_sp')->fetchAll(PDO::FETCH_COLUMN);
if (count($ids) !== 2) throw new RuntimeException('Need two existing products for temporary fixtures.');
$conn->exec('UPDATE san_pham SET so_luong = 5, trang_thai = 1');
$cart = new CartModel($conn);
$admin = new HoaDon($conn);
function stockItem($id, $quantity) {
    return ['id' => $id, 'gia' => 100000, 'so_luong' => $quantity];
}
function remainingStock($conn, $id) {
    $s = $conn->prepare('SELECT so_luong FROM san_pham WHERE id_sp = ?');
    $s->execute([$id]);
    return (int)$s->fetchColumn();
}
function orderState($conn, $id) {
    $s = $conn->prepare('SELECT trang_thai, da_tru_kho FROM hoadon WHERE id_hoadon = ?');
    $s->execute([$id]);
    return $s->fetch(PDO::FETCH_ASSOC);
}
function placeStockOrder($cart, $items) {
    return $cart->createOrderWithDetails('Test', '0900000000', 'Test', 500000, 'COD', $items);
}

$id = placeStockOrder($cart, [stockItem($ids[0], 2), stockItem($ids[0], 3)]);
verifyStock(remainingStock($conn, $ids[0]) === 0, 'Repeated product lines did not share stock.');
verifyStock((int)orderState($conn, $id)['da_tru_kho'] === 1, 'Missing stock marker.');
verifyStock(orderState($conn, $id)['trang_thai'] === 'Chờ xác nhận', 'Wrong initial order status.');
expectStockFailure(fn() => $admin->updateStatus($id, 'Mới'));
expectStockFailure(fn() => $admin->updateStatus($id, 'Hoàn tất'));
expectStockFailure(fn() => $admin->updateStatus($id, 'unknown'));
expectStockFailure(fn() => placeStockOrder($cart, [stockItem($ids[0], 1)]));
verifyStock((int)$conn->query('SELECT COUNT(*) FROM hoadon')->fetchColumn() === 1, 'Oversold order persisted.');
expectStockFailure(fn() => $admin->updateStatus($id, 'Đang giao hàng'));
expectStockFailure(fn() => $admin->updateStatus($id, 'Hoàn thành'));
$admin->updateStatus($id, 'Hủy đơn', 'Test cancellation reason');
verifyStock(remainingStock($conn, $ids[0]) === 5, 'Cancellation did not restore stock.');
verifyStock((int)orderState($conn, $id)['da_tru_kho'] === 0, 'Cancellation left stock marker.');
$admin->updateStatus($id, 'Hủy đơn', 'Test cancellation reason');
verifyStock(remainingStock($conn, $ids[0]) === 5, 'Repeated cancellation restored twice.');
expectStockFailure(fn() => $admin->updateStatus($id, 'Đang giao hàng'));
verifyStock(orderState($conn, $id)['trang_thai'] === 'Hủy đơn', 'Canceled order reopened.');
expectStockFailure(fn() => placeStockOrder($cart, [stockItem($ids[0], 2), stockItem($ids[1], 6)]));
verifyStock(remainingStock($conn, $ids[0]) === 5 && remainingStock($conn, $ids[1]) === 5, 'Partial stock deduction remained.');
$broken = stockItem($ids[0], 2);
$broken['gia'] = 'not-a-price';
expectStockFailure(fn() => placeStockOrder($cart, [$broken]), PDOException::class);
verifyStock(remainingStock($conn, $ids[0]) === 5, 'Detail failure did not restore stock.');
verifyStock((int)$conn->query('SELECT COUNT(*) FROM hoadon')->fetchColumn() === 1, 'Failed invoice remained.');
expectStockFailure(fn() => placeStockOrder($cart, [stockItem($ids[0], -1)]));
expectStockFailure(fn() => placeStockOrder($cart, [stockItem($ids[0], 0)]));
$conn->exec('UPDATE san_pham SET trang_thai = 0');
expectStockFailure(fn() => placeStockOrder($cart, [stockItem($ids[0], 1)]));
$conn->exec('UPDATE san_pham SET trang_thai = 1');
$complete = placeStockOrder($cart, [stockItem($ids[0], 1)]);
$admin->updateStatus($complete, 'Đã xác nhận');
expectStockFailure(fn() => $admin->updateStatus($complete, 'Chờ xác nhận'));
expectStockFailure(fn() => $admin->updateStatus($complete, 'Hủy đơn', 'Test cancellation reason'));
expectStockFailure(fn() => $admin->updateStatus($complete, 'Hoàn thành'));
$admin->updateStatus($complete, 'Đang giao hàng');
expectStockFailure(fn() => $admin->updateStatus($complete, 'Đã xác nhận'));
verifyStock(remainingStock($conn, $ids[0]) === 4, 'Confirmation or shipping deducted stock twice.');
$admin->updateStatus($complete, 'Hoàn thành');
$admin->updateStatus($complete, 'Hoàn thành');
expectStockFailure(fn() => $admin->updateStatus($complete, 'Hủy đơn', 'Test cancellation reason'));
verifyStock(remainingStock($conn, $ids[0]) === 4, 'Delivered order changed stock unexpectedly.');
$legacy = placeStockOrder($cart, [stockItem($ids[1], 1)]);
$conn->exec('UPDATE hoadon SET da_tru_kho = 0 WHERE id_hoadon = ' . (int)$legacy);
$before = remainingStock($conn, $ids[1]);
$admin->updateStatus($legacy, 'Hủy đơn', 'Test cancellation reason');
verifyStock(remainingStock($conn, $ids[1]) === $before, 'Legacy cancellation added unreserved stock.');
$overflow = placeStockOrder($cart, [stockItem($ids[1], 1)]);
$conn->exec('UPDATE san_pham SET so_luong = 2147483647 WHERE id_sp = ' . (int)$ids[1]);
expectStockFailure(fn() => $admin->updateStatus($overflow, 'Hủy đơn', 'Test cancellation reason'));
verifyStock(orderState($conn, $overflow)['trang_thai'] === 'Chờ xác nhận', 'Failed restoration changed order.');
verifyStock((int)orderState($conn, $overflow)['da_tru_kho'] === 1, 'Failed restoration lost stock marker.');
verifyStock(!$conn->inTransaction(), 'Transaction was left open.');
echo "PASS: $checks assertions plus expected rejection cases. Temporary tables only.\n";
