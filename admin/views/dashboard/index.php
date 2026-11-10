<?php
include __DIR__ . '/../layouts/header.php';
$previousRevenue = (float)$summary['previous_revenue'];
$revenueChange = $previousRevenue > 0 ? ((float)$summary['revenue'] - $previousRevenue) / $previousRevenue * 100 : null;
$statusStyles = ['Chờ xác nhận' => ['pending', '#3b73ff'], 'Đã xác nhận' => ['confirmed', '#9268d8'], 'Đang giao hàng' => ['shipping', '#f5ad19'], 'Hoàn thành' => ['delivered', '#4dbb59'], 'Hủy đơn' => ['cancelled', '#ed4c4c']];
$totalStatuses = array_sum($statusCounts);
?>
<div class="dashboard-heading"><h1 class="mb-0">Tổng quan</h1></div>
<div class="dashboard-kpis">
    <div class="dashboard-kpi"><div class="kpi-icon kpi-blue"><i class="bi bi-cart3" aria-hidden="true"></i></div><div><div class="kpi-title">Tổng đơn hàng</div><div class="kpi-value"><?= number_format($summary['orders'], 0, ',', '.') ?></div><div class="kpi-note">Toàn bộ đơn hàng</div></div></div>
    <div class="dashboard-kpi"><div class="kpi-icon kpi-green"><i class="bi bi-box-seam" aria-hidden="true"></i></div><div><div class="kpi-title">Tổng sản phẩm</div><div class="kpi-value"><?= number_format($summary['products'], 0, ',', '.') ?></div><div class="kpi-note">Sản phẩm đang bán</div></div></div>
    <div class="dashboard-kpi"><div class="kpi-icon kpi-orange"><i class="bi bi-grid" aria-hidden="true"></i></div><div><div class="kpi-title">Danh mục</div><div class="kpi-value"><?= number_format($summary['categories'], 0, ',', '.') ?></div><div class="kpi-note">Đang hiển thị</div></div></div>
    <div class="dashboard-kpi"><div class="kpi-icon kpi-purple"><i class="bi bi-cash-stack" aria-hidden="true"></i></div><div><div class="kpi-title">Doanh thu <?= $days ?> ngày</div><div class="kpi-value kpi-revenue"><?= number_format($summary['revenue'], 0, ',', '.') ?> đ</div><?php if ($revenueChange !== null): ?><span class="kpi-change <?= $revenueChange < 0 ? 'negative' : '' ?>"><?= $revenueChange < 0 ? '↓' : '↑' ?> <?= number_format(abs($revenueChange), 1, ',', '.') ?>%</span><div class="kpi-note">So với <?= $days ?> ngày trước</div><?php else: ?><div class="kpi-note">Từ các đơn hoàn thành</div><?php endif; ?></div></div>
</div>
<div class="dashboard-upper">
    <section class="dashboard-panel">
        <div class="dashboard-panel-head"><h2>Doanh thu <?= $days ?> ngày gần nhất</h2><form action="index.php" method="get"><input type="hidden" name="action" value="dashboard"><label for="dashboard-days" class="visually-hidden">Khoảng thời gian</label><select id="dashboard-days" name="days" class="form-select dashboard-period" onchange="this.form.submit()"><option value="7" <?= $days === 7 ? 'selected' : '' ?>>7 ngày</option><option value="30" <?= $days === 30 ? 'selected' : '' ?>>30 ngày</option><option value="90" <?= $days === 90 ? 'selected' : '' ?>>90 ngày</option></select><noscript><button type="submit" class="btn btn-sm btn-primary mt-2">Xem</button></noscript></form></div>
        <div class="dashboard-chart"><canvas id="dashboardRevenue" role="img" aria-label="Biểu đồ doanh thu <?= $days ?> ngày gần nhất, chỉ tính đơn hoàn thành"></canvas></div>
        <?php if (!array_sum($chartValues)): ?><p class="small text-muted mb-0 mt-2">Chưa có doanh thu từ đơn hoàn thành trong khoảng thời gian này.</p><?php endif; ?>
    </section>
    <section class="dashboard-panel">
        <div class="dashboard-panel-head"><h2>Đơn hàng gần đây</h2><a class="dashboard-link" href="index.php?action=listhoadon">Xem tất cả <i class="bi bi-arrow-right" aria-hidden="true"></i></a></div>
        <div class="table-responsive"><table class="table dashboard-table"><thead><tr><th scope="col">Mã đơn</th><th scope="col">Khách hàng</th><th scope="col" class="text-end">Tổng tiền</th><th scope="col" class="text-end">Trạng thái</th></tr></thead><tbody>
        <?php foreach ($recentOrders as $row): ?><tr><td><a class="text-dark" href="index.php?action=hoadon_detail&id=<?= (int)$row['id_hoadon'] ?>">#DH<?= str_pad((string)$row['id_hoadon'], 4, '0', STR_PAD_LEFT) ?></a></td><td><?= adminEscape($row['ho_ten']) ?></td><td class="text-end text-nowrap"><?= number_format($row['tongtien'], 0, ',', '.') ?> đ</td><td class="text-end"><span class="badge dashboard-status-<?= $statusStyles[$row['trang_thai']][0] ?? 'pending' ?>"><?= adminEscape($row['trang_thai']) ?></span></td></tr><?php endforeach; ?>
        <?php if (!$recentOrders): ?><tr><td colspan="4" class="dashboard-empty">Chưa có đơn hàng.</td></tr><?php endif; ?>
        </tbody></table></div>
    </section>
</div>
<div class="dashboard-lower">
    <section class="dashboard-panel">
        <div class="dashboard-panel-head"><h2>Sản phẩm bán chạy</h2><a class="dashboard-link" href="index.php?action=listsanpham">Xem tất cả <i class="bi bi-arrow-right" aria-hidden="true"></i></a></div>
        <div class="small text-muted mb-1">Theo đơn hoàn thành trong <?= $days ?> ngày gần nhất</div>
        <?php foreach ($bestSellers as $rank => $row): ?><div class="bestseller-row"><span class="bestseller-rank"><?= $rank + 1 ?></span><?php if ($row['hinh_anh']): ?><img class="admin-thumbnail" src="image/<?= adminEscape($row['hinh_anh']) ?>" alt=""><?php else: ?><span class="admin-thumbnail d-inline-flex align-items-center justify-content-center text-muted"><i class="bi bi-box" aria-hidden="true"></i></span><?php endif; ?><div><a class="bestseller-name" href="index.php?action=editsanpham&id=<?= (int)$row['id_sp'] ?>"><?= adminEscape($row['ten_sp']) ?></a><div class="small text-muted">Đã bán: <?= (int)$row['sold'] ?></div></div><span class="bestseller-price"><?= number_format($row['gia_sp'], 0, ',', '.') ?> đ</span></div><?php endforeach; ?>
        <?php if (!$bestSellers): ?><div class="dashboard-empty">Chưa có sản phẩm bán ra trong khoảng thời gian này.</div><?php endif; ?>
    </section>
    <section class="dashboard-panel">
        <div class="dashboard-panel-head"><h2>Tình trạng đơn hàng</h2><span class="small text-muted">Tất cả đơn hàng</span></div>
        <div class="dashboard-status-layout"><div class="dashboard-doughnut"><canvas id="dashboardStatuses" role="img" aria-label="Biểu đồ tỷ lệ trạng thái đơn hàng"></canvas><div class="doughnut-label"><strong><?= number_format($totalStatuses, 0, ',', '.') ?></strong><span>đơn hàng</span></div></div>
            <ul class="status-legend"><?php foreach ($statusCounts as $status => $count): ?><li><span class="status-dot" style="--status-color: <?= $statusStyles[$status][1] ?>"></span><span><?= $status ?></span><strong><?= $count ?> <span class="fw-normal">(<?= $totalStatuses ? round($count / $totalStatuses * 100) : 0 ?>%)</span></strong></li><?php endforeach; ?></ul>
        </div>
    </section>
</div>
<script type="application/json" id="dashboardChartData"><?= json_encode(['labels' => $chartLabels, 'revenue' => $chartValues, 'statuses' => array_values($statusCounts), 'statusLabels' => array_keys($statusCounts)], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?></script>
<script src="views/assets/vendors/chartjs/Chart.min.js"></script>
<script src="views/assets/js/admin-dashboard.js?v=4"></script>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
