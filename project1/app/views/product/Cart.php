<?php include 'app/views/shares/header.php'; ?>
<h1 class="text-center my-4">Giỏ hàng</h1>
<div class="container mb-5">
    <?php if (!empty($cart)): ?>
        <ul class="list-group">
            <?php foreach ($cart as $id => $item): ?>
                <li class="list-group-item d-flex align-items-center">
                    <?php if ($item['image']): ?>
                        <img src="/project1/<?php echo htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="Product Image" style="max-width: 100px; margin-right: 24px; border-radius: 8px;">
                    <?php endif; ?>
                    <div>
                        <h5 class="mb-1"><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></h5>
                        <p class="mb-1">Giá: <strong><?php echo number_format($item['price'], 0, ',', '.'); ?> VND</strong></p>
                        <p class="mb-1">Số lượng: <?php echo htmlspecialchars($item['quantity'], ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="mt-4 d-flex justify-content-between">
            <a href="/project1/Product" class="btn btn-secondary">Tiếp tục mua sắm</a>
            <a href="/project1/Product/checkout" class="btn btn-primary">Thanh Toán</a>
        </div>
    <?php else: ?>
        <p class="text-center">Giỏ hàng của bạn đang trống.</p>
        <div class="text-center">
            <a href="/project1/Product" class="btn btn-secondary mt-2">Tiếp tục mua sắm</a>
        </div>
    <?php endif; ?>
</div>
