<?php
require_once __DIR__ . '/../Model/Dashboard.php';
require_once __DIR__ . '/../../Support/OrderStatus.php';

class DashboardController {
    public function index() {
        $days = filter_var($_GET['days'] ?? 30, FILTER_VALIDATE_INT);
        if (!in_array($days, [7, 30, 90], true)) $days = 30;
        $today = new DateTimeImmutable('today', new DateTimeZone('Asia/Ho_Chi_Minh'));
        $from = $today->modify('-' . ($days - 1) . ' days');
        $until = $today->modify('+1 day');
        $previousFrom = $from->modify('-' . $days . ' days');
        $model = new Dashboard();
        $summary = $model->getSummary($from->format('Y-m-d'), $until->format('Y-m-d'), $previousFrom->format('Y-m-d'));
        $recentOrders = $model->getRecentOrders();
        $bestSellers = $model->getBestSellers($from->format('Y-m-d'), $until->format('Y-m-d'));
        $statusCounts = array_fill_keys(OrderStatus::all(), 0);
        foreach ($model->getOrderStatuses() as $row) {
            if (array_key_exists($row['trang_thai'], $statusCounts)) $statusCounts[$row['trang_thai']] = (int)$row['total'];
        }
        $daily = array_column($model->getDailyRevenue($from->format('Y-m-d'), $until->format('Y-m-d')), 'revenue', 'day');
        $chartLabels = [];
        $chartValues = [];
        for ($day = $from; $day < $until; $day = $day->modify('+1 day')) {
            $chartLabels[] = $day->format('d/m');
            $chartValues[] = (float)($daily[$day->format('Y-m-d')] ?? 0);
        }
        $pendingOrders = $statusCounts['Chờ xác nhận'];
        include __DIR__ . '/../views/dashboard/index.php';
    }
}
