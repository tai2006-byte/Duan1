<?php
function storeEscape($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function storeMoney($value) { return number_format((float)$value, 0, ',', '.') . ' đ'; }
function storeProductImage($name) {
    $name = is_string($name) ? basename($name) : '';
    return $name !== '' && is_file(__DIR__ . '/../../admin/image/' . $name) ? 'admin/image/' . $name : 'img/toy-placeholder.svg';
}
function storeStatusClass($status) {
    return ['Chờ xác nhận'=>'bg-secondary', 'Đã xác nhận'=>'bg-info text-dark', 'Đang giao hàng'=>'bg-warning text-dark', 'Hoàn thành'=>'bg-success', 'Hủy đơn'=>'bg-danger'][$status] ?? 'bg-secondary';
}
