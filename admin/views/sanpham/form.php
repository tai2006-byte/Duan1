<?php include __DIR__ . '/../layouts/header.php'; $product = $editing ? $sanPham : []; ?>
<div class="d-flex justify-content-between align-items-center gap-3 mb-4"><h1 class="mb-0"><?= $editing ? 'Sửa sản phẩm' : 'Thêm sản phẩm' ?></h1><a href="index.php?action=listsanpham" class="btn btn-outline-secondary">Quay lại</a></div>
<div class="card admin-form"><div class="card-body">
    <?php if (!$allDanhMuc): ?><div class="alert alert-warning">Bạn cần <a href="index.php?action=createdanhmuc">thêm danh mục</a> trước khi lưu sản phẩm.</div><?php endif; ?>
    <form action="index.php?action=<?= $editing ? 'updatesanpham' : 'storesanpham' ?>" method="post" enctype="multipart/form-data">
        <?php if ($editing): ?><input type="hidden" name="id" value="<?= (int)$product['id_sp'] ?>"><?php endif; ?>
        <div class="row g-3">
            <div class="col-12"><label for="product-name" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label><input id="product-name" name="ten" class="form-control" required value="<?= adminEscape($product['ten_sp'] ?? '') ?>"></div>
            <div class="col-md-6"><label for="product-category" class="form-label">Danh mục <span class="text-danger">*</span></label><select id="product-category" name="danhmuc" class="form-select" required>
                <?php foreach ($allDanhMuc as $item): ?><option value="<?= (int)$item['id_danh_muc'] ?>" <?= (int)($product['id_danh_muc'] ?? 0) === (int)$item['id_danh_muc'] ? 'selected' : '' ?>><?= adminEscape($item['name_danh_muc']) ?><?= (int)$item['Trangthai'] === 0 ? ' (đã ẩn)' : '' ?></option><?php endforeach; ?>
            </select></div>
            <div class="col-md-6"><label for="product-price" class="form-label">Giá bán (đồng) <span class="text-danger">*</span></label><input id="product-price" name="gia" type="number" min="0" step="0.01" class="form-control" required value="<?= adminEscape($product['gia_sp'] ?? '') ?>"></div>
            <div class="col-md-6"><label for="product-stock" class="form-label">Số lượng tồn kho</label><input id="product-stock" name="so_luong" type="number" min="0" step="1" class="form-control" required value="<?= (int)($product['so_luong'] ?? 0) ?>"></div>
            <div class="col-md-6"><label for="product-type" class="form-label">Nhãn sản phẩm</label><input id="product-type" name="loai" class="form-control" placeholder="Ví dụ: Mới, nổi bật" value="<?= adminEscape($product['loai'] ?? '') ?>"></div>
            <div class="col-12"><label for="product-image" class="form-label">Ảnh sản phẩm<?= !$editing ? ' *' : '' ?></label><input id="product-image" name="anh" type="file" class="form-control" accept="image/jpeg,image/png,image/webp" <?= !$editing ? 'required' : '' ?> aria-describedby="image-help"><div id="image-help" class="form-text">JPEG, PNG, WebP; tối đa 5 MB hoặc giới hạn thấp hơn của máy chủ.<?= $editing ? ' Để trống để giữ ảnh hiện tại.' : '' ?></div>
                <?php if (!empty($product['hinh_anh'])): ?><img class="admin-image-preview border rounded mt-3 p-2" src="image/<?= adminEscape($product['hinh_anh']) ?>" alt="Ảnh sản phẩm hiện tại"><?php endif; ?>
            </div>
            <div class="col-12"><label for="product-description" class="form-label">Mô tả</label><textarea id="product-description" name="mota" rows="5" class="form-control"><?= adminEscape($product['Mo_ta'] ?? '') ?></textarea></div>
        </div>
        <div class="d-flex gap-2 mt-4"><button class="btn btn-primary" type="submit" <?= !$allDanhMuc ? 'disabled' : '' ?>><?= $editing ? 'Lưu thay đổi' : 'Thêm sản phẩm' ?></button><a class="btn btn-outline-secondary" href="index.php?action=listsanpham">Hủy</a></div>
    </form>
</div></div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
