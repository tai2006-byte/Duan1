<?php
class AccountRole {
    const ADMIN = 'admin';
    const CUSTOMER = 'customer';
    public static function labels() {
        return [self::ADMIN => 'Quản trị viên', self::CUSTOMER => 'Khách hàng'];
    }
}
