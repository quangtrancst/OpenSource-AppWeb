// app/controllers/DefaultController.php (Ví dụ)
<?php
class DefaultController {
    public function index() {
        // Chuyển hướng đến trang sản phẩm hoặc hiển thị trang chủ mặc định
        header('Location: /project1/product/list'); 
        // hoặc include 'app/views/home/index.php';
    }

    public function notFound() {
        http_response_code(404);
        // include 'app/views/error/404.php'; // Tạo view 404 đẹp hơn
        echo "<h1>404 - Page Not Found</h1><p>The page you are looking for does not exist.</p>";
    }
}
?>