<?php
class CategoryModel
{
    private $conn;
    private $table_name = "category";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getCategories()
    {
        $query = "SELECT DISTINCT id, name, description FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }

    public function getCategoryById($id)
    {
        $query = "SELECT id, name, description FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function updateCategory($id, $name, $description)
    {
        // Basic validation
        if (empty($name) || strlen($name) < 3 || strlen($name) > 100) {
            return ['errors' => ["Category name must be between 3 and 100 characters."]];
        }

        $query = "UPDATE " . $this->table_name . " SET name = :name, description = :description WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);

        if ($stmt->execute()) {
            return true;
        }
        return ['errors' => $stmt->errorInfo()];
    }

    public function deleteCategory($id)
    {
        // Trước khi xóa, bạn có thể muốn kiểm tra xem có sản phẩm nào thuộc danh mục này không
        // và xử lý tương ứng (ví dụ: không cho xóa, hoặc cập nhật category_id của sản phẩm thành null)
        // Ví dụ kiểm tra (cần có ProductModel và phương thức tương ứng):
        $productModel = new ProductModel($this->conn); // Cần điều chỉnh cách khởi tạo ProductModel
        if ($productModel->hasProductsInCategory($id)) {
            return ['errors' => ["Cannot delete category: It still contains products."]];
        }

        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                return true;
            }
            return ['errors' => $stmt->errorInfo()];
    }
}
?>
