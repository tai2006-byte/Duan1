<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="mb-4"><h1>Quản lý hóa đơn</h1><p class="text-muted mb-0">Theo dõi đơn hàng và cập nhật trạng thái giao hàng.</p></div>
<div class="card"><div class="card-body"><div class="table-responsive">
<table class="table table-striped table-hover align-middle" id="table1">
<thead class="table-light"><tr><th scope="col">Mã đơn</th><th scope="col">Người nhận</th><th scope="col">Ngày đặt</th><th scope="col">Tổng tiền</th><th scope="col">Trạng thái</th><th scope="col">Thao tác</th></tr></thead>
<tbody><?php foreach ($listHoaDon as $order): ?>
<tr><td>#<?= (int)$order['id_hoadon'] ?></td><td><?= adminEscape($order['ho_ten']) ?><div class="small text-muted"><?= adminEscape($order['sdt']) ?></div></td><td class="text-nowrap" data-sort="<?= strtotime($order['ngay_dat']) ?>"><?= date('d/m/Y H:i', strtotime($order['ngay_dat'])) ?></td><td class="text-nowrap"><?= number_format($order['tongtien'], 0, ',', '.') ?> đ</td><td><span class="badge bg-<?= adminStatusClass($order['trang_thai']) ?>"><?= adminEscape($order['trang_thai']) ?></span></td><td><a class="btn btn-sm btn-outline-primary" href="index.php?action=hoadon_detail&id=<?= (int)$order['id_hoadon'] ?>">Chi tiết</a></td></tr>
<?php endforeach; ?></tbody>
</table>
<?php if (!$listHoaDon): ?><p class="text-muted text-center py-4 mb-0">Chưa có đơn hàng.</p><?php endif; ?>
</div></div></div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
