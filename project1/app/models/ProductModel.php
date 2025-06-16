<?php

class ProductModel
{
    private $db;
    private $table_name = "product";

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getProducts()
    {
        $query = "SELECT p.id, p.name, p.description, p.price, p.image_url, c.name as category_name 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN category c ON p.category_id = c.id 
                  ORDER BY p.id DESC"; // Thêm ORDER BY để dễ theo dõi nếu muốn

        $stmt = $this->db->prepare($query);
        if ($stmt->execute()) {
            $results = $stmt->fetchAll(PDO::FETCH_OBJ);
            return is_array($results) ? $results : []; // Đảm bảo luôn trả về một mảng
        }
        // Ghi log lỗi nếu cần thiết cho việc debug phía server
        // error_log("Lỗi DB trong ProductModel::getProducts(): " . implode(" | ", $stmt->errorInfo()));
        return []; // Trả về mảng rỗng nếu có lỗi
    }

    public function getProductById($id)
    {
        $query = "SELECT * FROM product WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function hasProductsInCategory($categoryId)

    {

        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE category_id = :category_id";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':category_id', $categoryId);

        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_OBJ);

        return $result->count > 0;

    }

    public function addProduct($name, $description, $price, $category_id, $image_url)
    {
        $query = "INSERT INTO product (name, description, price, category_id, image_url) VALUES (:name, :description, :price, :category_id, :image_url)";
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':image_url', $image_url);

        if ($stmt->execute()) {
            return true;
        } else {
            return $stmt->errorInfo();
        }
    }

    public function updateProduct($id, $name, $description, $price, $category_id, $image_url= null)
    {
        $query = "UPDATE product SET name = :name, description = :description, price = :price, category_id = :category_id";
        if ($image_url !== null) {
            $query .= ", image_url = :image_url";
        }
        $query .= " WHERE id = :id";

        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':category_id', $category_id);
        
        if ($image_url !== null) {
            $stmt->bindParam(':image_url', $image_url);
        }
        
        return $stmt->execute();
    }

    public function deleteProduct($id)
    {
        $query = "DELETE FROM product WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
