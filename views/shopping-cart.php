<?php
include __DIR__ . '/layout/header.php';
$pageTitle='Giỏ hàng của bạn'; $pageDescription='Những món quà nhỏ đang chờ được mang về nhà.';
include __DIR__ . '/partials/page-heading.php';
$total=0; foreach($_SESSION['cart']??[] as $item) $total+=$item['gia']*$item['so_luong'];
?>
<section class="container pb-4" data-cart-root data-endpoint="index.php?act=cart_update" data-csrf="<?= storeEscape($_SESSION['cart_csrf']) ?>">
    <div id="cart-message" class="alert py-2" role="status" aria-live="polite" hidden><span data-cart-message-text></span><button type="button" class="btn btn-sm btn-outline-secondary ms-2" data-cart-retry hidden>Thử lại</button></div>
    <div id="cart-filled" <?= empty($_SESSION['cart'])?'hidden':'' ?>>
        <div class="checkout-step"><span class="active">1. Giỏ hàng</span><i class="bi bi-chevron-right" aria-hidden="true"></i><span>2. Thanh toán</span><span id="cart-saving" class="ms-auto small text-muted" hidden>Đang cập nhật…</span></div>
        <div class="row g-4"><div class="col-lg-8"><div class="card overflow-hidden"><div class="table-responsive"><table class="table mb-0"><thead class="table-light"><tr><th scope="col">Sản phẩm</th><th scope="col">Số lượng</th><th scope="col">Thành tiền</th><th scope="col"><span class="visually-hidden">Xóa</span></th></tr></thead>
        <tbody><?php foreach($_SESSION['cart'] as $key=>$item): ?><tr data-cart-row data-key="<?= storeEscape($key) ?>" data-quantity="<?= (int)$item['so_luong'] ?>">
            <td><div class="cart-product"><img src="<?= storeEscape(storeProductImage($item['img'])) ?>" alt=""><div><h2><a class="text-dark" data-cart-name href="index.php?act=detail&id=<?= (int)$item['id'] ?>"><?= storeEscape($item['ten']) ?></a></h2><div class="small text-muted" data-unit-price><?= storeMoney($item['gia']) ?></div></div></div></td>
            <td><form action="index.php?act=update_cart" method="post" data-quantity-form><input type="hidden" name="csrf_token" value="<?= storeEscape($_SESSION['cart_csrf']) ?>"><input type="hidden" name="key" value="<?= storeEscape($key) ?>">
                <div class="quantity-control"><button type="button" data-quantity-step="-1" aria-label="Giảm số lượng <?= storeEscape($item['ten']) ?>" <?= (int)$item['so_luong']<=1?'disabled':'' ?>>−</button><input type="number" name="quantity" value="<?= (int)$item['so_luong'] ?>" min="1" max="2147483647" step="1" inputmode="numeric" autocomplete="off" required aria-label="Số lượng <?= storeEscape($item['ten']) ?>" data-cart-quantity><button type="button" data-quantity-step="1" aria-label="Tăng số lượng <?= storeEscape($item['ten']) ?>">+</button></div>
                <noscript><button class="btn btn-sm btn-outline-primary mt-2" type="submit">Lưu số lượng</button></noscript>
            </form></td>
            <td class="text-nowrap fw-bold" data-line-total><?= storeMoney($item['gia']*$item['so_luong']) ?></td>
            <td><button type="button" class="btn btn-sm text-muted" data-cart-remove aria-label="Xóa <?= storeEscape($item['ten']) ?> khỏi giỏ"><i class="bi bi-trash" aria-hidden="true"></i></button><noscript><a href="<?= storeEscape('index.php?'.http_build_query(['act'=>'delete_cart','key'=>$key])) ?>">Xóa</a></noscript></td>
        </tr><?php endforeach; ?></tbody></table></div></div><a class="d-inline-block mt-3 small" href="index.php?act=shop"><i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Tiếp tục mua sắm</a></div>
        <div class="col-lg-4"><div class="store-panel order-summary"><h2>Tóm tắt giỏ hàng</h2><div class="d-flex justify-content-between mb-3"><span class="text-muted">Số lượng sản phẩm</span><strong data-cart-count><?= (int)$cartCount ?></strong></div><div class="d-flex justify-content-between align-items-center border-top pt-3 mb-4"><strong>Tổng tiền</strong><span class="summary-total" data-cart-total><?= storeMoney($total) ?></span></div><a class="btn btn-primary w-100" data-cart-checkout href="index.php?act=checkout">Tiến hành thanh toán <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i></a><p class="small text-muted mt-3 mb-0">Bạn có thể đặt hàng mà không cần đăng ký tài khoản.</p></div></div></div>
    </div>
    <div id="cart-empty" class="store-empty" <?= !empty($_SESSION['cart'])?'hidden':'' ?>><i class="bi bi-cart3" aria-hidden="true"></i><h2>Giỏ hàng vẫn đang trống</h2><p>Khám phá cửa hàng và chọn món đồ chơi bé yêu thích nhé!</p><a class="btn btn-primary" href="index.php?act=shop">Khám phá đồ chơi</a></div>
</section>
<script src="js/cart.js?v=1" defer></script>
<?php include __DIR__ . '/layout/footer.php'; ?>
