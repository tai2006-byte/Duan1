<?php

class OrderStatus {
    const PENDING = 'Chờ xác nhận';
    const CONFIRMED = 'Đã xác nhận';
    const SHIPPING = 'Đang giao hàng';
    const COMPLETED = 'Hoàn thành';
    const CANCELLED = 'Hủy đơn';

    public static function all() {
        return [self::PENDING, self::CONFIRMED, self::SHIPPING, self::COMPLETED, self::CANCELLED];
    }

    public static function next($current) {
        return [
            self::PENDING => [self::CONFIRMED, self::CANCELLED],
            self::CONFIRMED => [self::SHIPPING],
            self::SHIPPING => [self::COMPLETED, self::CANCELLED]
        ][$current] ?? [];
    }
}
