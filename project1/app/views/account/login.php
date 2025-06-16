<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <!-- Bootstrap 4.5.2 CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome cho icon Google -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- CSS tùy chỉnh cho giao diện -->
    <style>
        body, html {
            height: 100%;
        }
        .gradient-custom {
            height: 100%;
            /* fallback for old browsers */
            background: #6a11cb;
            /* Chrome 10-25, Safari 5.1-6 */
            background: -webkit-linear-gradient(to right, rgba(106, 17, 203, 1), rgba(37, 117, 252, 1));
            /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
            background: linear-gradient(to right, rgba(106, 17, 203, 1), rgba(37, 117, 252, 1));
        }
        .card.bg-dark {
            background-color: #212529 !important; /* Đảm bảo nền tối */
        }
        /* Style để đảm bảo icon nằm bên trong nút Google */
        .btn-danger .fab {
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>
    <?php 
        // Chúng ta vẫn có thể include header của bạn để lấy thanh điều hướng
        // nhưng sẽ bỏ qua các thẻ <html>, <head>, <body> trong file header đó
        // Giả sử header.php chỉ chứa phần <nav>
        // include 'app/views/shares/header.php'; 
    ?>

    <section class="vh-100 gradient-custom">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card bg-dark text-white" style="border-radius: 1rem;">
                        <div class="card-body p-5 text-center">

                            <!-- Form sẽ được xử lý bằng JavaScript, không có action/method ở đây -->
                            <form id="login-form">
                                <div class="mb-md-5 mt-md-4 pb-4">

                                    <h2 class="font-weight-bold mb-2 text-uppercase">Đăng nhập</h2>
                                    <p class="text-white-50 mb-4">Vui lòng nhập tên đăng nhập và mật khẩu!</p>
                                    
                                    <!-- Vùng để hiển thị thông báo lỗi từ JavaScript -->
                                    <div id="login-error-message" class="alert alert-danger" style="display: none; text-align: left;"></div>

                                    <div class="form-group text-left">
                                        <label class="form-label" for="username">Tên đăng nhập</label>
                                        <input type="text" id="username" name="username" class="form-control form-control-lg" required />
                                    </div>

                                    <div class="form-group text-left">
                                        <label class="form-label" for="password">Mật khẩu</label>
                                        <input type="password" id="password" name="password" class="form-control form-control-lg" required />
                                    </div>

                                    <button class="btn btn-outline-light btn-lg px-5 mb-4 mt-3" type="submit">Đăng nhập</button>
                                    
                                    <!-- Nút Đăng nhập bằng Google của bạn được tích hợp vào đây -->
                                    <div class="d-flex justify-content-center text-center">
                                        <a href="https://accounts.google.com/o/oauth2/auth?client_id=733224391042-iv5g0j33003f4coq8mv8qvduvgqo85u1.apps.googleusercontent.com&redirect_uri=http://localhost:90/project1/Account/googleCallback&response_type=code&scope=email%20profile" 
                                           class="btn btn-danger btn-lg" style="width: 100%;">
                                           <i class="fab fa-google"></i> Đăng nhập bằng Google
                                        </a>
                                    </div>
                                </div>

                                <div>
                                    <p class="mb-0">Chưa có tài khoản? <a href="/project1/Account/register" class="text-white-50 font-weight-bold">Đăng ký</a></p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Các script cần thiết cho Bootstrap và logic của bạn -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Script xử lý đăng nhập JWT (đã được điều chỉnh cho project của bạn) -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const loginForm = document.getElementById('login-form');
        const errorMessageContainer = document.getElementById('login-error-message');
        const baseUrl = '/project1'; // Đường dẫn gốc project của bạn

        if(loginForm) {
            loginForm.addEventListener('submit', function(event) {
                event.preventDefault(); // Ngăn form submit theo cách truyền thống
                errorMessageContainer.style.display = 'none'; // Ẩn lỗi cũ
                errorMessageContainer.textContent = '';

                const formData = new FormData(this);
                const jsonData = {};
                formData.forEach((value, key) => {
                    jsonData[key] = value;
                });
                
                // Endpoint API để kiểm tra đăng nhập và nhận token JWT
                fetch(`${baseUrl}/account/checkLogin`, { 
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(jsonData)
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errData => {
                            throw new Error(errData.message || 'Lỗi không xác định từ máy chủ.');
                        }).catch(() => {
                            throw new Error(`Lỗi HTTP ${response.status}. Vui lòng thử lại.`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.token) {
                        localStorage.setItem('jwtToken', data.token);
                        // Chuyển hướng đến trang sản phẩm chính
                        window.location.href = `${baseUrl}/Product`; 
                    } else {
                        throw new Error(data.message || 'Phản hồi không hợp lệ từ máy chủ.');
                    }
                })
                .catch(error => {
                    console.error('Lỗi Đăng nhập:', error);
                    errorMessageContainer.textContent = error.message || 'Đăng nhập thất bại. Vui lòng kiểm tra lại thông tin.';
                    errorMessageContainer.style.display = 'block';
                });
            });
        }
    });
    </script>
</body>
</html>
