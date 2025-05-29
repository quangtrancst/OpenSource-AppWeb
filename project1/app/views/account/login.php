<?php include 'app/views/shares/header.php'; ?>
<div class="container mt-5">
    <h1 class="text-center">Đăng nhập</h1>
    <form method="POST" action="/project1/Account/login" class="mt-4">
        <div class="form-group">
            <label for="username">Tên đăng nhập:</label>
            <input type="text" id="username" name="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Mật khẩu:</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Đăng nhập</button>
        <a href="https://accounts.google.com/o/oauth2/auth?client_id=
733224391042-iv5g0j33003f4coq8mv8qvduvgqo85u1.apps.googleusercontent.com
&redirect_uri=http://localhost:90/project1/Account/googleCallback&response_type=code&scope=email%20profile" class="btn btn-danger">Đăng nhập bằng Google</a>
    </form>
</div>
<?php include 'app/views/shares/footer.php'; ?>