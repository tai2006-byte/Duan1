<?php
require_once __DIR__ . '/../../../Support/OrderStatus.php';
function adminEscape($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
function adminStatusClass($status) {
    return [OrderStatus::PENDING => 'secondary', OrderStatus::CONFIRMED => 'info', OrderStatus::SHIPPING => 'primary', OrderStatus::COMPLETED => 'success', OrderStatus::CANCELLED => 'danger'][$status] ?? 'secondary';
}
