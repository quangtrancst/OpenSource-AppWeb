<?php
session_start();
require_once 'app/models/ProductModel.php';
require_once 'app/helpers/SessionHelper.php';

// Lấy URL từ query string và xử lý
$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');  // Loại bỏ dấu '/' cuối cùng (nếu có)
$url = filter_var($url, FILTER_SANITIZE_URL);  // Lọc URL để bảo mật
$url = explode('/', $url);  // Tách URL thành các phần

// Kiểm tra phần đầu tiên của URL để xác định controller
$controllerName = isset($url[0]) && $url[0] != '' ? ucfirst($url[0]) . 'Controller' : 'ProductController';

// Kiểm tra phần thứ hai của URL để xác định action
$action = isset($url[1]) && $url[1] != '' ? $url[1] : 'index';

// Kiểm tra xem controller có tồn tại không
if (!file_exists('app/controllers/' . $controllerName . '.php')) {
    // Xử lý khi không tìm thấy controller
    die('Controller not found');
}

// Nạp controller
require_once 'app/controllers/' . $controllerName . '.php';
$controller = new $controllerName();

// Kiểm tra xem action có tồn tại trong controller không
if (!method_exists($controller, $action)) {
    // Xử lý khi không tìm thấy action
    die('Action not found');
}

// Gọi action với các tham số còn lại (nếu có)
call_user_func_array([$controller, $action], array_slice($url, 2));
