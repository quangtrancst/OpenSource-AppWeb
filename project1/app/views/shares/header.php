a<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản lý sản phẩm</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/project1/public/css/styles.scss">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-light header">
    <a class="navbar-brand logo" href="#"><img src="/project1/public/images/OIP.jpg" style="height: 50px" alt=""></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a class="nav-link" href="/project1/Product#">Trang chủ</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/project1/Product/add">Thêm sản phẩm</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/project1/Category/list">Quản lý Danh mục</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Liên hệ</a>
        </li>
      </ul>
    </div>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <?php if (SessionHelper::isLoggedIn()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="/project1/Account/logout">Đăng xuất</a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="/project1/Account/login">Đăng nhập</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/project1/Account/register">Đăng ký</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
  </nav>
    
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
