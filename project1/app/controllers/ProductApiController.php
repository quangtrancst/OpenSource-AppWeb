<?php
// File: app/controllers/ProductApiController.php

require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
require_once 'app/utils/JWTHandler.php';

class ProductApiController
{
    private $productModel;
    private $db;
    private $jwtHandler;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
        $this->jwtHandler = new JWTHandler();
    }

    // Hàm helper để xác thực JWT token
    private function authenticate()
    {
        $authHeader = null;
        $headers = apache_request_headers();
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
        } elseif (isset($headers['authorization'])) { // Một số client gửi header viết thường
            $authHeader = $headers['authorization'];
        }

        if (!$authHeader) {
            http_response_code(401);
            echo json_encode(['message' => 'Unauthorized: Missing Authorization header']);
            return false;
        }

        list($jwt) = sscanf($authHeader, 'Bearer %s');

        if (!$jwt) {
            http_response_code(401);
            echo json_encode(['message' => 'Unauthorized: Malformed token']);
            return false;
        }

        try {
            $decoded = $this->jwtHandler->decode($jwt);
            return $decoded; // Trả về payload nếu thành công
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(['message' => 'Unauthorized: ' . $e->getMessage()]);
            return false;
        }
    }
    
    // Hàm helper để kiểm tra quyền admin từ payload của token
    private function isAdmin($decodedPayload) {
        return isset($decodedPayload['data']['role']) && $decodedPayload['data']['role'] === 'admin';
    }


    // GET /api/product (Bảo vệ)
    public function index()
    {
        if (!$this->authenticate()) { return; }
        
        header('Content-Type: application/json');
        $products = $this->productModel->getProducts();
        echo json_encode($products);
    }

    // GET /api/product/{id} (Bảo vệ)
    public function show($id)
    {
        if (!$this->authenticate()) { return; }

        header('Content-Type: application/json');
        $product = $this->productModel->getProductById($id);
        if ($product) {
            echo json_encode($product);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Product not found']);
        }
    }

    // POST /api/product (Bảo vệ và yêu cầu quyền Admin)
    public function store()
    {
        $decoded = $this->authenticate();
        if (!$decoded) { return; }
        
        // Kiểm tra quyền admin
        if (!$this->isAdmin($decoded)) {
            http_response_code(403); // Forbidden
            echo json_encode(['message' => 'Forbidden: You do not have permission to perform this action.']);
            return;
        }

        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        
        $name = $data['name'] ?? '';
        $description = $data['description'] ?? '';
        $price = $data['price'] ?? '';
        $category_id = $data['category_id'] ?? null;
        // API không xử lý upload ảnh, image_url có thể được gửi dạng text (tùy chọn)
        $image_url = $data['image_url'] ?? null;

        $result = $this->productModel->addProduct($name, $description, $price, $category_id, $image_url);

        if ($result === true) {
            http_response_code(201); // Created
            echo json_encode(['message' => 'Product created successfully']);
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(['message' => 'Product creation failed', 'errors' => $result]);
        }
    }

    // PUT /api/product/{id} (Bảo vệ và yêu cầu quyền Admin)
    public function update($id)
    {
        $decoded = $this->authenticate();
        if (!$decoded) { return; }

        if (!$this->isAdmin($decoded)) {
            http_response_code(403);
            echo json_encode(['message' => 'Forbidden: You do not have permission to perform this action.']);
            return;
        }

        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        // ... logic lấy dữ liệu từ $data và gọi updateProduct ...
        $name = $data['name'] ?? null;
        $description = $data['description'] ?? null;
        $price = $data['price'] ?? null;
        $category_id = $data['category_id'] ?? null;
        $image_url = $data['image_url'] ?? null;

        $result = $this->productModel->updateProduct($id, $name, $description, $price, $category_id, $image_url);

        if ($result) {
            echo json_encode(['message' => 'Product updated successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Product update failed']);
        }
    }

    // DELETE /api/product/{id} (Bảo vệ và yêu cầu quyền Admin)
    public function destroy($id)
    {
        $decoded = $this->authenticate();
        if (!$decoded) { return; }

        if (!$this->isAdmin($decoded)) {
            http_response_code(403);
            echo json_encode(['message' => 'Forbidden: You do not have permission to perform this action.']);
            return;
        }

        header('Content-Type: application/json');
        $result = $this->productModel->deleteProduct($id);
        if ($result) {
            echo json_encode(['message' => 'Product deleted successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Product deletion failed']);
        }
    }
}
?>
