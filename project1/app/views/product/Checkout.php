
<?php include 'app/views/shares/header.php'; ?>
<h1 class="text-center my-4">Thanh toán</h1>
<div class="container mb-5" style="max-width: 500px;">
    <form method="POST" action="/project1/Product/processCheckout">
        <div class="form-group">
            <label for="name">Họ tên:</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="phone">Số điện thoại:</label>
            <input type="text" id="phone" name="phone" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="address">Địa chỉ:</label>
            <textarea id="address" name="address" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Thanh toán</button>
    </form>
    <a href="/project1/Product/cart" class="btn btn-secondary mt-3">Quay lại giỏ hàng</a>
</div>
