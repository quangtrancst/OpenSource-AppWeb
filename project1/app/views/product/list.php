<head>
    <link rel="stylesheet" href="/project1/public/css/styles.scss">
</head>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/project1/app/views/shares/header.php'; ?>

<?php if (SessionHelper::isAdmin()): ?>
    <a href="/project1/Product/add" class="btn btn-success">Thêm sản phẩm</a>
<?php endif; ?>

<?php if (empty($products)): ?>
    <div class="alert alert-info">Không có sản phẩm nào.</div>
<?php else: ?>
    <!-- Carousel Slider -->
    <div id="productCarousel" class="carousel slide mb-4" style="margin-top:90px;" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php
            $images = [
                'public/images/nike-just-do-it.avif',
                'public/images/women-s-shoes-clothing-accessories.avif',
            ];
            $first = true;
            foreach ($images as $image): ?>
                <div style="height: 70vh; overflow: hidden;" class="carousel-item <?php echo $first ? 'active' : ''; ?>">
                    <img src="<?php echo $image; ?>" alt="Ảnh sản phẩm" class="d-block w-100" style="height: auto; width: 100%; object-fit: cover; border: none;">
                </div>
                <?php $first = false; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <ul class="list-group">
        <?php foreach ($products as $product): ?>
            <li class="list-group-item">
                <?php if (!empty($product->image_url)): ?>
                    <img src="/project1/<?php echo htmlspecialchars($product->image_url, ENT_QUOTES, 'UTF-8'); ?>" 
                         alt="Ảnh sản phẩm" 
                         style="max-width: 100%; height: auto; margin-bottom: 10px;">
                <?php endif; ?>

                <h2>
                    <a href="/project1/Product/show/<?php echo $product->id; ?>">
                        <?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                </h2>

                <p class="content">
                    <?php echo htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8'); ?>
                </p>

                <p class="price">
                    <?php echo htmlspecialchars($product->price, ENT_QUOTES, 'UTF-8'); ?> VNĐ
                </p>

                <a href="/project1/Product/edit/<?php echo $product->id; ?>" class="btn">
                    Sửa
                </a>

                <a href="/project1/Product/delete/<?php echo $product->id; ?>"
                   class="btn"
                   onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">
                    Xóa
                </a>
                <a href="/project1/Product/addToCart/<?php echo $product->id; ?>" class="btn btn-primary">
                Thêm vào giỏ hàng
            </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/project1/app/views/shares/footer.php'; ?>
