<?php
// Require necessary files
require_once('app/config/database.php');
require_once('app/models/CategoryModel.php');
require_once 'app/helpers/SessionHelper.php';

class CategoryController
{
    private $categoryModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->categoryModel = new CategoryModel($this->db);
    }

    private function isAdmin()
    {
        return SessionHelper::isAdmin();
    }

    public function list()
    {
        if (!$this->isAdmin()) {
            echo "Bạn không có quyền truy cập chức năng này!";
            exit;
        }
        $categories = $this->categoryModel->getCategories();
        include 'app/views/category/list.php';
    }

    public function edit($id)
    {
        $category = $this->categoryModel->getCategoryById($id);
        if ($category) {
            include 'app/views/category/edit.php';
        } else {
            // Optionally, set a session message and redirect
            $_SESSION['error_message'] = "Category not found.";
            header('Location: /project1/Category/list');
            exit;
        }
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $errors = [];

            if (!$id) {
                $errors[] = "Category ID is missing.";
            }
            // Basic server-side validation (can be more extensive)
            if (empty($name) || strlen($name) < 3 || strlen($name) > 100) {
                $errors[] = "Category name must be between 3 and 100 characters.";
            }
            // Description can be optional or have its own rules
            if (strlen($description) > 255) { // Example max length
                $errors[] = "Description cannot exceed 255 characters.";
            }


            if (empty($errors)) {
                $result = $this->categoryModel->updateCategory($id, $name, $description);
                if ($result === true) {
                    $_SESSION['success_message'] = "Category updated successfully!";
                    header('Location: /project1/Category/list');
                    exit;
                } else {
                    // $result contains error array from model
                    $errors = array_merge($errors, $result['errors'] ?? ["An unknown error occurred during update."]);
                }
            }

            // If there are errors, reload the edit form with errors and original data
            // Fetch the category again to pass to the view, or use submitted data
            $category = (object)$_POST; // Re-populate form with submitted data
            // It's better to fetch the original category if only some fields failed validation
            // $category = $this->categoryModel->getCategoryById($id);
            // if ($category) {
            //    $category->name = $name; // Keep submitted name if it was valid or for re-display
            //    $category->description = $description; // Keep submitted desc
            // }

            include 'app/views/category/edit.php'; // Pass $errors and $category
        } else {
            // Not a POST request, redirect or show error
            header('Location: /project1/Category/list');
            exit;
        }
    }

    public function delete($id)
    {
        if (!$id) {
            $_SESSION['error_message'] = "Category ID is missing for deletion.";
            header('Location: /project1/Category/list');
            exit;
        }

        $result = $this->categoryModel->deleteCategory($id);

        if ($result === true) {
            $_SESSION['success_message'] = "Category deleted successfully!";
        } else {
            $errorMessage = "Could not delete category.";
            if (isset($result['errors']) && is_array($result['errors'])) {
                // Nếu model trả về lỗi cụ thể (ví dụ: do ràng buộc khóa ngoại)
                $errorMessage .= " Reason: " . implode(", ", $result['errors']);
            }
            $_SESSION['error_message'] = $errorMessage;
        }
        header('Location: /project1/Category/list');
        exit;
    }
}
?>
