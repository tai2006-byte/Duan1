<?php
include __DIR__ . '/../layouts/header.php';
$totalRevenue = array_sum(array_column($listDoanhThu, 'doanh_thu'));
$totalOrders = array_sum(array_column($listDoanhThu, 'so_luong_don'));
$totalProducts = array_sum(array_column($listDanhMuc, 'countSp'));
$maxRevenue = max(array_merge([1], array_column($listDoanhThu, 'doanh_thu')));
?>
<div class="mb-4"><h1>Thống kê kinh doanh</h1><p class="text-muted mb-0">Doanh thu được tính từ các đơn hàng hoàn thành.</p></div>
<div class="card"><div class="card-body">
    <form action="index.php" method="get" class="row g-3 align-items-end">
        <input type="hidden" name="action" value="thongke">
        <div class="col-sm-5 col-lg-4"><label for="date-from" class="form-label">Từ ngày</label><input id="date-from" type="date" name="date_from" class="form-control" value="<?= adminEscape($dateFrom) ?>" required></div>
        <div class="col-sm-5 col-lg-4"><label for="date-to" class="form-label">Đến ngày</label><input id="date-to" type="date" name="date_to" class="form-control" value="<?= adminEscape($dateTo) ?>" required></div>
        <div class="col-sm-2 col-lg-4"><button type="submit" class="btn btn-primary">Lọc</button></div>
    </form>
</div></div>
<div class="row g-3">
    <div class="col-md-4"><div class="card"><div class="card-body"><div class="text-muted mb-2">Doanh thu trong kỳ</div><div class="fs-4 fw-bold"><?= number_format($totalRevenue, 0, ',', '.') ?> đ</div></div></div></div>
    <div class="col-md-4"><div class="card"><div class="card-body"><div class="text-muted mb-2">Đơn hoàn thành trong kỳ</div><div class="fs-4 fw-bold"><?= (int)$totalOrders ?></div></div></div></div>
    <div class="col-md-4"><div class="card"><div class="card-body"><div class="text-muted mb-2">Sản phẩm đang bán</div><div class="fs-4 fw-bold"><?= (int)$totalProducts ?></div></div></div></div>
</div>
<div class="row g-4">
    <div class="col-lg-8"><section class="card"><div class="card-header"><h2 class="mb-0">Doanh thu theo ngày</h2></div><div class="table-responsive">
        <table class="table"><thead class="table-light"><tr><th scope="col" class="ps-3">Ngày</th><th scope="col">Đơn hoàn thành</th><th scope="col" class="text-end pe-3">Doanh thu</th></tr></thead><tbody>
        <?php foreach ($listDoanhThu as $row): ?><tr><td class="ps-3 text-nowrap"><?= date('d/m/Y', strtotime($row['ngay'])) ?></td><td><?= (int)$row['so_luong_don'] ?></td><td class="text-end pe-3"><?= number_format($row['doanh_thu'], 0, ',', '.') ?> đ<div class="progress mt-2" aria-hidden="true"><div class="progress-bar" style="width: <?= max(0, min(100, (float)$row['doanh_thu'] / $maxRevenue * 100)) ?>%"></div></div></td></tr><?php endforeach; ?>
        <?php if (!$listDoanhThu): ?><tr><td colspan="3" class="text-center text-muted py-4">Không có đơn hoàn thành trong khoảng ngày này.</td></tr><?php endif; ?>
        </tbody></table>
    </div></section></div>
    <div class="col-lg-4"><section class="card"><div class="card-header"><h2 class="mb-0">Doanh thu gần đây</h2></div><ul class="list-group list-group-flush">
        <?php foreach ($listGanDay as $row): ?><li class="list-group-item d-flex flex-wrap justify-content-between gap-2 py-3"><span><?= date('d/m/Y', strtotime($row['ngay'])) ?></span><strong><?= number_format($row['doanh_thu'], 0, ',', '.') ?> đ</strong></li><?php endforeach; ?>
        <?php if (!$listGanDay): ?><li class="list-group-item text-muted py-4">Chưa có doanh thu.</li><?php endif; ?>
    </ul></section></div>
</div>
<section class="card"><div class="card-header"><h2 class="mb-0">Thống kê theo danh mục</h2></div><div class="table-responsive">
    <table class="table table-striped"><thead class="table-light"><tr><th scope="col" class="ps-3">Danh mục</th><th scope="col">Sản phẩm đang bán</th><th scope="col" class="text-end pe-3">Doanh thu trong kỳ</th></tr></thead><tbody>
    <?php foreach ($listDanhMuc as $row): ?><tr><td class="ps-3"><?= adminEscape($row['name']) ?></td><td><?= (int)$row['countSp'] ?></td><td class="text-end pe-3"><?= number_format($row['totalRevenue'], 0, ',', '.') ?> đ</td></tr><?php endforeach; ?>
    <?php if (!$listDanhMuc): ?><tr><td colspan="3" class="text-center text-muted py-4">Chưa có danh mục.</td></tr><?php endif; ?>
    </tbody></table>
</div></section>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
