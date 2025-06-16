<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NIKE Store</title>
    <!-- Bootstrap 4.5.2 CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome cho các icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- QUAN TRỌNG: Link đến file CSS đã được biên dịch, không phải file .scss -->
    <link rel="stylesheet" href="/project1/public/css/styles.css"> 
    
    <!-- Thêm một số style nhỏ để header đẹp hơn -->
    <style>
        .navbar-brand img {
            height: 40px; /* Điều chỉnh logo */
        }
        .navbar .nav-item {
            margin-left: 0.5rem;
        }
        .navbar .nav-link {
            font-weight: 500;
        }
        /* Style cho user dropdown (tùy chọn) */
        .dropdown-menu {
            border-radius: 0.5rem;
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top header">
    <div class="container">
        <a class="navbar-brand logo" href="/project1/Product">
            <!-- Thay bằng logo Nike SVG để đẹp hơn nếu có thể -->
            <img src="/project1/public/images/OIP.jpg" alt="Nike Logo">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Các link điều hướng chính -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="/project1/Product">Trang chủ</a>
                </li>
                <?php if (SessionHelper::isAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/project1/Product/add">Thêm sản phẩm</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/project1/Category/list">Quản lý Danh mục</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="#">Liên hệ</a>
                </li>
            </ul>

            <!-- Các link đăng nhập/đăng xuất ở bên phải -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item" id="nav-login">
                    <a class="nav-link" href="/project1/Account/login">Đăng nhập</a>
                </li>
                <li class="nav-item" id="nav-register">
                    <a class="nav-link" href="/project1/Account/register">Đăng ký</a>
                </li>
                <!-- Sẽ được hiển thị bằng JavaScript nếu đã đăng nhập -->
                <li class="nav-item dropdown" id="nav-user-info" style="display: none;">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-user-circle mr-1"></i>
                        <span id="username-display"></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="#">Thông tin tài khoản</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#" onclick="logout(event)">Đăng xuất</a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Một div trống để nội dung không bị header che mất -->
<div style="height: 70px;"></div>

<!-- Bắt đầu nội dung chính của trang (sẽ được include view con vào đây) -->
<main class="container mt-4">

<!-- Chú ý: Thẻ main, body và html sẽ được đóng trong file footer.php -->
