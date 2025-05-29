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
        $query = "SELECT * FROM product";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
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
