<?php
require_once('app/config/database.php');
require_once('app/models/ProductModel.php');
require_once('app/models/CategoryModel.php');
require_once 'app/helpers/SessionHelper.php';

class ProductController
{
    private $productModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
    }

    private function isAdmin()
    {
        return SessionHelper::isAdmin();
    }

    public function index()
    {
        $products = $this->productModel->getProducts();
        include 'app/views/product/list.php';
    }

    public function show($id)
    {
        $product = $this->productModel->getProductById($id);
        if ($product) {
            include 'app/views/product/show.php';
        } else {
            echo "Không thấy sản phẩm.";
        }
    }

    public function add()
    {
        if (!$this->isAdmin()) {
            echo "Bạn không có quyền truy cập chức năng này!";
            exit;
        }
        $categories = (new CategoryModel($this->db))->getCategories();
        include 'app/views/product/add.php';
    }

    public function save()
    {
        if (!$this->isAdmin()) {
            echo "Bạn không có quyền truy cập chức năng này!";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? '';
            $category_id = $_POST['category_id'] ?? null;

            // Image upload handling
            $image_url = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/project1/public/images/"; // Updated path
                $image_name = basename($_FILES["image"]["name"]);
                $target_file = $target_dir . $image_name;
                $image_url = "public/images/" . $image_name; // Updated URL

                // Validate file type
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
                if (in_array($imageFileType, $allowed_types)) {
                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                        // Image uploaded successfully
                    } else {
                        $errors[] = "Có lỗi xảy ra khi tải lên ảnh.";
                    }
                } else {
                    $errors[] = "Chỉ chấp nhận file ảnh (jpg, jpeg, png, gif).";
                }
            }

            // Save product
            $result = $this->productModel->addProduct($name, $description, $price, $category_id, $image_url);

            if (is_array($result)) {
                $errors = $result;
                $categories = (new CategoryModel($this->db))->getCategories();
                include 'app/views/product/add.php';
            } else {
                $_SESSION['success_message'] = "Sản phẩm đã được thêm thành công!";
                header('Location: /project1/Product');
                exit;
            }
        }
    }

    public function edit($id)
    {
        if (!$this->isAdmin()) {
            echo "Bạn không có quyền truy cập chức năng này!";
            exit;
        }
        $product = $this->productModel->getProductById($id);
        $categories = (new CategoryModel($this->db))->getCategories();
        if ($product) {
            include 'app/views/product/edit.php';
        } else {
            echo "Không thấy sản phẩm.";
        }
    }

    public function update()
    {
        if (!$this->isAdmin()) {
            echo "Bạn không có quyền truy cập chức năng này!";
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $category_id = $_POST['category_id'];
            $errors = [];

            // Lấy thông tin sản phẩm hiện tại để có image_url cũ
            $currentProduct = $this->productModel->getProductById($id);
            $image_url = $currentProduct->image_url; // Giữ ảnh cũ nếu không có ảnh mới

            // Xử lý tải ảnh mới nếu có
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/project1/public/images/";
                $image_name = basename($_FILES["image"]["name"]);
                
                // Tạo tên file duy nhất để tránh ghi đè
                $imageFileType = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
                $unique_image_name = uniqid() . '.' . $imageFileType;
                $target_file = $target_dir . $unique_image_name;
                
                $new_image_url = "public/images/" . $unique_image_name;

                // Kiểm tra loại file
                $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
                if (in_array($imageFileType, $allowed_types)) {
                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                        // Xóa ảnh cũ nếu có và ảnh mới được tải lên thành công
                        if (!empty($currentProduct->image_url) && file_exists($_SERVER['DOCUMENT_ROOT'] . "/project1/" . $currentProduct->image_url)) {
                            unlink($_SERVER['DOCUMENT_ROOT'] . "/project1/" . $currentProduct->image_url);
                        }
                        $image_url = $new_image_url; // Cập nhật image_url mới
                    } else {
                        $errors[] = "Có lỗi xảy ra khi tải lên ảnh mới.";
                    }
                } else {
                    $errors[] = "Chỉ chấp nhận file ảnh (jpg, jpeg, png, gif).";
                }
            }

            if (empty($errors)) {
                $result = $this->productModel->updateProduct($id, $name, $description, $price, $category_id, $image_url);
                if ($result) {
                    header('Location: http://localhost:90/project1/Product#');
                    exit;
                } else {
                    $errors[] = "Đã xảy ra lỗi khi cập nhật sản phẩm.";
                }
            }

            // Nếu có lỗi, tải lại form edit với thông báo lỗi
            $product = $this->productModel->getProductById($id); // Lấy lại product để hiển thị
            $categories = (new CategoryModel($this->db))->getCategories();
            include 'app/views/product/edit.php'; // Truyền $errors và $product, $categories vào view
        }
    }

    public function delete($id)
    {
        if (!$this->isAdmin()) {
            echo "Bạn không có quyền truy cập chức năng này!";
            exit;
        }
        if ($this->productModel->deleteProduct($id)) {
            header('Location: /project1/Product');
            exit;
        } else {
            echo "Đã xảy ra lỗi khi xóa sản phẩm.";
        }
    }

    public function addToCart($id)
    {
        $product = $this->productModel->getProductById($id);
        if (!$product) {
            echo "Không tìm thấy sản phẩm.";
            return;
        }
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity']++;
        } else {
            $_SESSION['cart'][$id] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'image' => $product->image_url ?? null
            ];
        }
        header('Location: /project1/Product/cart');
        exit;
    }

    public function cart()
    {
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        include 'app/views/product/cart.php';
    }

    public function checkout()
    {
        include 'app/views/product/checkout.php';
    }

    public function processCheckout()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'];
            $phone = $_POST['phone'];
            $address = $_POST['address'];

            // Kiểm tra giỏ hàng
            if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
                echo "Giỏ hàng trống.";
                return;
            }

            // Bắt đầu giao dịch
            $this->db->beginTransaction();
            try {
                // Thêm thông tin đơn hàng vào bảng orders
                $query = "INSERT INTO orders (name, phone, address) VALUES (:name, :phone, :address)";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':phone', $phone);
                $stmt->bindParam(':address', $address);
                $stmt->execute();
                $order_id = $this->db->lastInsertId();

                // Thêm chi tiết đơn hàng vào bảng order_details
                $cart = $_SESSION['cart'];
                foreach ($cart as $product_id => $item) {
                    $query = "INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':order_id', $order_id);
                    $stmt->bindParam(':product_id', $product_id);
                    $stmt->bindParam(':quantity', $item['quantity']);
                    $stmt->bindParam(':price', $item['price']);
                    $stmt->execute();
                }

                // Xóa giỏ hàng sau khi đặt hàng thành công
                unset($_SESSION['cart']);

                // Hoàn tất giao dịch
                $this->db->commit();

                // Chuyển hướng đến trang xác nhận đơn hàng
                header('Location: /project1/Product/orderConfirmation');
                exit;
            } catch (Exception $e) {
                // Rollback giao dịch nếu có lỗi
                $this->db->rollBack();
                echo "Đã xảy ra lỗi khi xử lý đơn hàng: " . $e->getMessage();
            }
        }
    }

    public function orderConfirmation()
    {
        include 'app/views/product/orderConfirmation.php';
    }
}
?>

