<?php
include __DIR__ . '/../layouts/header.php';
$current = $order['trang_thai'];
$nextStatuses = OrderStatus::next($current);
$options = $nextStatuses ? array_merge([$current], $nextStatuses) : [];
$selectedStatus = isset($selectedStatus) && in_array($selectedStatus, $options, true) ? $selectedStatus : $current;
?>
<div class="d-flex justify-content-between align-items-center gap-3 mb-4"><h1 class="mb-0">Hóa đơn #<?= (int)$order['id_hoadon'] ?></h1><a href="index.php?action=listhoadon" class="btn btn-outline-secondary">Quay lại</a></div>
<div class="row g-4">
    <div class="col-lg-8">
        <section class="card"><div class="card-header"><h2 class="mb-0">Sản phẩm đã mua</h2></div><div class="table-responsive">
            <table class="table align-middle"><thead class="table-light"><tr><th scope="col" class="ps-3">Sản phẩm</th><th scope="col">Đơn giá</th><th scope="col">Số lượng</th><th scope="col" class="text-end pe-3">Thành tiền</th></tr></thead><tbody>
            <?php foreach ($orderDetails as $item): ?><tr><td class="ps-3"><div class="d-flex align-items-center gap-2"><?php if ($item['hinh_anh']): ?><img src="image/<?= adminEscape($item['hinh_anh']) ?>" class="admin-thumbnail" alt=""><?php endif; ?><span><?= adminEscape($item['ten_sp']) ?></span></div></td><td class="text-nowrap"><?= number_format($item['gia'], 0, ',', '.') ?> đ</td><td><?= (int)$item['so_luong'] ?></td><td class="text-end pe-3 text-nowrap"><?= number_format($item['gia'] * $item['so_luong'], 0, ',', '.') ?> đ</td></tr><?php endforeach; ?>
            <?php if (!$orderDetails): ?><tr><td colspan="4" class="text-center text-muted py-4">Không có chi tiết sản phẩm.</td></tr><?php endif; ?>
            </tbody><tfoot><tr><th colspan="3" class="ps-3">Tổng thanh toán</th><th class="text-end pe-3 text-nowrap"><?= number_format($order['tongtien'], 0, ',', '.') ?> đ</th></tr></tfoot></table>
        </div></section>
        <section class="card"><div class="card-header"><h2 class="mb-0">Thông tin giao hàng</h2></div><div class="card-body"><dl class="row mb-0">
            <dt class="col-sm-4">Người nhận</dt><dd class="col-sm-8"><?= adminEscape($order['ho_ten']) ?></dd>
            <dt class="col-sm-4">Số điện thoại</dt><dd class="col-sm-8"><?= adminEscape($order['sdt']) ?></dd>
            <dt class="col-sm-4">Địa chỉ</dt><dd class="col-sm-8"><?= adminEscape($order['dia_chi']) ?></dd>
            <dt class="col-sm-4">Ngày đặt</dt><dd class="col-sm-8"><?= date('d/m/Y H:i', strtotime($order['ngay_dat'])) ?></dd>
            <dt class="col-sm-4">Thanh toán</dt><dd class="col-sm-8 mb-0"><?= adminEscape($order['hinh_thuc_thanh_toan']) ?></dd>
        </dl></div></section>
    </div>
    <div class="col-lg-4"><section class="card"><div class="card-header"><h2 class="mb-0">Trạng thái đơn hàng</h2></div><div class="card-body">
        <p><span class="badge bg-<?= adminStatusClass($current) ?>"><?= adminEscape($current) ?></span></p>
        <?php if (isset($statusError)): ?><div class="alert alert-danger" role="alert"><?= adminEscape($statusError) ?></div><?php endif; ?>
        <?php if ($current === OrderStatus::CANCELLED): ?><div class="mb-3"><strong>Lý do hủy đơn</strong><p class="mt-2 mb-0 text-break"><?= nl2br(adminEscape($order['ly_do_huy'] ?? 'Đơn cũ chưa ghi nhận lý do hủy.')) ?></p></div><?php endif; ?>
        <?php if ($options): ?>
        <form action="index.php?action=update_status" method="post">
            <input type="hidden" name="id" value="<?= (int)$order['id_hoadon'] ?>">
            <label for="order-status" class="form-label">Cập nhật trạng thái</label><select id="order-status" name="trang_thai" class="form-select mb-3"><?php foreach ($options as $status): ?><option value="<?= adminEscape($status) ?>" <?= $selectedStatus === $status ? 'selected' : '' ?>><?= adminEscape($status) ?></option><?php endforeach; ?></select>
            <div id="cancel-reason-field" class="mb-3" <?= $selectedStatus !== OrderStatus::CANCELLED ? 'hidden' : '' ?>>
                <label for="cancel-reason" class="form-label">Lý do hủy đơn <span class="text-danger">*</span></label>
                <textarea id="cancel-reason" name="ly_do_huy" class="form-control" rows="3" maxlength="1000" <?= $selectedStatus === OrderStatus::CANCELLED ? 'required' : '' ?> placeholder="Ví dụ: Không liên hệ được khách để giao hàng."><?= adminEscape($cancelReason ?? '') ?></textarea>
                <div class="form-text">Bắt buộc khi hủy đơn, tối đa 1000 ký tự.</div>
            </div>
            <p class="small text-muted">Chỉ đơn Chờ xác nhận hoặc Đang giao hàng được Hủy đơn, bắt buộc có lý do và được hoàn kho. Đơn Hoàn thành hoặc Hủy đơn không thể mở lại.</p>
            <button type="submit" class="btn btn-primary w-100">Lưu trạng thái</button>
        </form>
        <?php else: ?><p class="text-muted mb-0">Đơn hàng đã kết thúc, không thể thay đổi trạng thái.</p><?php endif; ?>
    </div></section></div>
</div>
<script src="views/assets/js/admin-order-status.js?v=1"></script>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
