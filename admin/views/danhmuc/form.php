<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-4 gap-3"><h1 class="mb-0"><?= $editing ? 'Sửa danh mục' : 'Thêm danh mục' ?></h1><a href="index.php?action=listdanhmuc" class="btn btn-outline-secondary">Quay lại</a></div>
<div class="card admin-form"><div class="card-body">
    <form action="index.php?action=<?= $editing ? 'updatedanhmuc' : 'storedanhmuc' ?>" method="post">
        <?php if ($editing): ?><input type="hidden" name="id" value="<?= (int)$danhMuc['id_danh_muc'] ?>"><?php endif; ?>
        <div class="mb-4"><label class="form-label" for="category-name">Tên danh mục <span class="text-danger">*</span></label><input id="category-name" class="form-control" name="ten" required value="<?= adminEscape($editing ? $danhMuc['name_danh_muc'] : '') ?>"></div>
        <div class="d-flex gap-2"><button type="submit" class="btn btn-primary"><?= $editing ? 'Lưu thay đổi' : 'Thêm danh mục' ?></button><a href="index.php?action=listdanhmuc" class="btn btn-outline-secondary">Hủy</a></div>
    </form>
</div></div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
