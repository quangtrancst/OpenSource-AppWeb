<?php include 'app/views/shares/header.php'; ?>
<div class="container mt-5">
    <h1 class="text-center">Đăng ký</h1>
    <form method="POST" action="/project1/Account/register" class="mt-4">
        <div class="form-group">
            <label for="username">Tên đăng nhập:</label>
            <input type="text" id="username" name="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="fullname">Họ và tên:</label>
            <input type="text" id="fullname" name="fullname" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Mật khẩu:</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Đăng ký</button>
    </form>
</div>
<?php include 'app/views/shares/footer.php'; ?>