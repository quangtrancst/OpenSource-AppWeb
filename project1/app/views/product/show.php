<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết sản phẩm</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/project1/public/css/styles.css"> <!-- Đảm bảo bạn đã biên dịch SCSS thành CSS -->
</head>
<body>
    <div class="container mt-5 product-detail">
        <h1 class="text-center mb-4">Chi tiết sản phẩm</h1>

        <?php if (!isset($product)): ?>
            <div class="alert alert-danger">Sản phẩm không tồn tại.</div>
        <?php else: ?>
            <div class="card mb-3 shadow">
                <div class="row no-gutters">
                    <?php if (!empty($product->image_url)): ?>
                        <div class="col-md-4">
                            <img src="/project1/<?php echo htmlspecialchars($product->image_url, ENT_QUOTES, 'UTF-8'); ?>" class="card-img" alt="Ảnh sản phẩm" style="object-fit: cover; height: 100%;">
                        </div>
                    <?php endif; ?>

                    <div class="col-md-8">
                        <div class="card-body">
                            <h3 class="card-title"><?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p class="card-text">
                                <strong>Mô tả:</strong><br>
                                <?php echo nl2br(htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8')); ?>
                            </p>
                            <p class="card-text">
                                <strong>Giá:</strong> <?php echo number_format($product->price, 0, ',', '.'); ?> VNĐ
                            </p>
                            <p class="card-text">
                                <strong>Danh mục:</strong> <?php echo htmlspecialchars($product->category_name ?? 'Không rõ', ENT_QUOTES, 'UTF-8'); ?>
                            </p>

                            <a href="/project1/Product/edit/<?php echo $product->id; ?>" class="btn btn-warning">Sửa</a>
                            <a href="/project1/Product/delete/<?php echo $product->id; ?>" class="btn btn-danger"
                               onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">Xóa</a>
                            <a href="/project1/Product#" class="btn btn-secondary">Quay lại danh sách</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
