<?php
// File: project1/index.php

// Bắt đầu session ở đầu tiên của script
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Các file require_once cần thiết cho cả web và API (nếu có)
// Database config thường cần cho việc tạo connection trong các controller
require_once 'app/config/database.php'; 
require_once 'app/helpers/SessionHelper.php'; // Session helper có thể dùng chung

// Phân tích URL để lấy các segments
$url_path = $_GET['url'] ?? ''; // Lấy tham số 'url' từ .htaccess
$url_path = rtrim($url_path, '/'); // Loại bỏ dấu / cuối cùng
$url_path = filter_var($url_path, FILTER_SANITIZE_URL); // Lọc để bảo mật
$url_segments = explode('/', $url_path); // Tách thành mảng

// Khởi tạo biến cho controller, action và params
$controllerName = ''; 
$action = 'index'; // Action mặc định
$params = [];

// Xác định controller và action dựa trên URL segments
if (isset($url_segments[0]) && !empty($url_segments[0])) {
    // KIỂM TRA XEM PHẦN TỬ ĐẦU TIÊN CÓ PHẢI LÀ 'api' KHÔNG
    if (strtolower($url_segments[0]) === 'api') {
        // Đây là một yêu cầu API
        if (isset($url_segments[1]) && !empty($url_segments[1])) {
            // Segment thứ hai là tên resource (ví dụ: 'product', 'category')
            $apiResource = ucfirst(strtolower($url_segments[1]));
            $controllerName = $apiResource . 'ApiController'; // Tạo tên API Controller (ví dụ: ProductApiController)
            
            $request_method = $_SERVER['REQUEST_METHOD']; // Lấy phương thức HTTP (GET, POST, PUT, DELETE)
            
            // Xác định action cho API dựa trên phương thức HTTP và ID (nếu có)
            switch ($request_method) {
                case 'GET':
                    if (isset($url_segments[2]) && !empty($url_segments[2])) { // Nếu có segment thứ 3, đó là ID
                        $action = 'show'; // Ví dụ: GET /api/product/123 -> ProductApiController->show(123)
                        $params[] = $url_segments[2]; // ID là tham số
                    } else { // Không có ID
                        $action = 'index'; // Ví dụ: GET /api/product -> ProductApiController->index()
                    }
                    break;
                case 'POST': // Ví dụ: POST /api/product -> ProductApiController->store()
                    $action = 'store';
                    // Dữ liệu POST sẽ được đọc từ php://input trong controller
                    break;
                case 'PUT': // Ví dụ: PUT /api/product/123 -> ProductApiController->update(123)
                    if (isset($url_segments[2]) && !empty($url_segments[2])) {
                        $action = 'update';
                        $params[] = $url_segments[2]; // ID là tham số
                        // Dữ liệu PUT sẽ được đọc từ php://input trong controller
                    } else {
                        http_response_code(400); 
                        echo json_encode(['message' => 'Resource ID missing for PUT request. Use /api/{resource}/{id}']);
                        exit;
                    }
                    break;
                case 'DELETE': // Ví dụ: DELETE /api/product/123 -> ProductApiController->destroy(123)
                    if (isset($url_segments[2]) && !empty($url_segments[2])) {
                        $action = 'destroy';
                        $params[] = $url_segments[2]; // ID là tham số
                    } else {
                        http_response_code(400); 
                        echo json_encode(['message' => 'Resource ID missing for DELETE request. Use /api/{resource}/{id}']);
                        exit;
                    }
                    break;
                default:
                    http_response_code(405); // Method Not Allowed
                    echo json_encode(['message' => 'HTTP Method Not Allowed for API endpoint.']);
                    exit;
            }
        } else {
            // URL là /api/ mà không có resource cụ thể (ví dụ: /api/product)
            http_response_code(400); // Bad Request
            echo json_encode(['message' => 'API resource not specified. Use /api/{resource_name}']);
            exit;
        }
    } else {
        // Đây là yêu cầu trang web thông thường (không phải API)
        $controllerName = ucfirst(strtolower($url_segments[0])) . 'Controller'; // Ví dụ: ProductController
        if (isset($url_segments[1]) && !empty($url_segments[1])) {
            $action = strtolower($url_segments[1]); // Action cho web controller
        }
        // Các segments còn lại là tham số cho action
        $params = array_slice($url_segments, 2); 
    }
} else {
    // Nếu URL trống (ví dụ: truy cập vào /project1/), gọi controller mặc định cho trang web
    $controllerName = 'ProductController'; // Controller mặc định cho trang chủ web (hoặc DefaultController)
    $action = 'index'; // Action mặc định cho trang chủ web
}

// Nạp file controller và gọi action
$controllerFile = 'app/controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    if (class_exists($controllerName)) {
        $controllerInstance = new $controllerName(); // Khởi tạo controller
        if (method_exists($controllerInstance, $action)) {
            // Gọi action với các tham số đã được chuẩn bị
            call_user_func_array([$controllerInstance, $action], $params);
        } else {
            // Lỗi: Action không tồn tại trong controller
            if (strtolower($url_segments[0] ?? '') === 'api') { // Kiểm tra lại nếu là API request
                http_response_code(404); // Not Found
                echo json_encode(['message' => "API action '$action' not found in controller '$controllerName'."]);
            } else {
                // Với web, có thể chuyển hướng đến trang lỗi 404 thân thiện
                // Hoặc gọi DefaultController->notFound()
                require_once 'app/controllers/DefaultController.php';
                $defaultCtrl = new DefaultController();
                $defaultCtrl->notFound();
            }
        }
    } else {
        // Lỗi: Class controller không tồn tại
        if (strtolower($url_segments[0] ?? '') === 'api') {
            http_response_code(404);
            echo json_encode(['message' => "API Controller class '$controllerName' not found."]);
        } else {
            require_once 'app/controllers/DefaultController.php';
            $defaultCtrl = new DefaultController();
            $defaultCtrl->notFound();
        }
    }
} else {
    // Lỗi: File controller không tồn tại
    if (isset($url_segments[0]) && strtolower($url_segments[0]) === 'api') {
        http_response_code(404);
        echo json_encode(['message' => "API Controller file '$controllerFile' not found."]);
    } else {
        // Trang web: thử gọi DefaultController->notFound()
        $defaultControllerFile = 'app/controllers/DefaultController.php';
        if (file_exists($defaultControllerFile)) {
            require_once $defaultControllerFile;
            if (class_exists('DefaultController')) {
                $defaultController = new DefaultController();
                if (method_exists($defaultController, 'notFound')) {
                    $defaultController->notFound();
                    exit;
                }
            }
        }
        // Lỗi cuối cùng nếu không xử lý được
        http_response_code(404);
        die("Page not found. Controller file '$controllerFile' does not exist.");
    }
}
?>
