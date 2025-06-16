<?php
require_once 'app/config/database.php';
require_once 'app/models/CategoryModel.php';

class CategoryApiController
{
    private $categoryModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->categoryModel = new CategoryModel($this->db);
    }

    // GET /api/category
    public function index()
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *'); // Cho phép truy cập từ mọi nguồn
        header('Access-Control-Allow-Methods: GET');

        $categories = $this->categoryModel->getCategories();
        echo json_encode($categories);
    }

    // Các phương thức khác (store, update, destroy) cho Category có thể thêm tương tự nếu cần
    // Ví dụ GET /api/category/{id}
    public function show($id)
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET');

        $category = $this->categoryModel->getCategoryById($id);
        if ($category) {
            echo json_encode($category);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Category not found']);
        }
    }
}
?>