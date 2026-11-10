<?php
require_once __DIR__ . '/pdo.php';

class Dashboard {
    private $conn;

    public function __construct(?PDO $conn = null) {
        $this->conn = $conn ?? pdo_get_connection();
    }

    public function getSummary($from, $until, $previousFrom) {
        $counts = $this->conn->query("SELECT
            (SELECT COUNT(*) FROM hoadon) AS orders,
            (SELECT COUNT(*) FROM san_pham WHERE trang_thai = 1) AS products,
            (SELECT COUNT(*) FROM danh_muc WHERE Trangthai = 1) AS categories")->fetch(PDO::FETCH_ASSOC);
        $stmt = $this->conn->prepare("SELECT
            COALESCE(SUM(CASE WHEN ngay_dat >= ? THEN tongtien ELSE 0 END), 0) AS revenue,
            COALESCE(SUM(CASE WHEN ngay_dat < ? THEN tongtien ELSE 0 END), 0) AS previous_revenue
            FROM hoadon WHERE trang_thai = 'Hoàn thành' AND ngay_dat >= ? AND ngay_dat < ?");
        $stmt->execute([$from, $from, $previousFrom, $until]);
        return array_merge($counts, $stmt->fetch(PDO::FETCH_ASSOC));
    }

    public function getDailyRevenue($from, $until) {
        $stmt = $this->conn->prepare("SELECT DATE(ngay_dat) AS day, SUM(tongtien) AS revenue
            FROM hoadon WHERE trang_thai = 'Hoàn thành' AND ngay_dat >= ? AND ngay_dat < ?
            GROUP BY DATE(ngay_dat) ORDER BY day");
        $stmt->execute([$from, $until]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecentOrders() {
        return $this->conn->query('SELECT id_hoadon, ho_ten, tongtien, trang_thai FROM hoadon ORDER BY ngay_dat DESC, id_hoadon DESC LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBestSellers($from, $until) {
        $stmt = $this->conn->prepare("SELECT sp.id_sp, sp.ten_sp, sp.hinh_anh, sp.gia_sp, SUM(ct.so_luong) AS sold
            FROM chi_tiet_hoa_don ct JOIN hoadon h ON ct.id_hoadon = h.id_hoadon
            JOIN san_pham sp ON sp.id_sp = ct.id_sanpham
            WHERE h.trang_thai = 'Hoàn thành' AND h.ngay_dat >= ? AND h.ngay_dat < ?
            GROUP BY sp.id_sp, sp.ten_sp, sp.hinh_anh, sp.gia_sp
            ORDER BY sold DESC, sp.id_sp DESC LIMIT 3");
        $stmt->execute([$from, $until]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderStatuses() {
        return $this->conn->query('SELECT trang_thai, COUNT(*) AS total FROM hoadon GROUP BY trang_thai')->fetchAll(PDO::FETCH_ASSOC);
    }
}
