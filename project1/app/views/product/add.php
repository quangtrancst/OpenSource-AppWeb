<?php 
// File: app/views/product/add.php
// Đảm bảo header.php đã được include và chứa các link CSS, JS Bootstrap cần thiết
include $_SERVER['DOCUMENT_ROOT'] . '/project1/app/views/shares/header.php'; 
$errors = $data['errors'] ?? [];
$old_input = $data['old_input'] ?? [];
?>

<div class="container mt-5 form-container" style="padding-top: 80px;">
    <div class="form-content bg-white p-4 p-md-5 rounded shadow-lg">
        <h1 class="text-center mb-4">Thêm sản phẩm mới</h1>

        <div id="form-message" class="mb-3"></div> 

        <form id="add-product-form">
            <div class="form-group mb-3">
                <label for="name">Tên sản phẩm:</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>

            <div class="form-group mb-3">
                <label for="description">Mô tả:</label>
                <textarea id="description" name="description" class="form-control" rows="3" required></textarea>
            </div>

            <div class="form-group mb-3">
                <label for="price">Giá (VNĐ):</label>
                <input type="number" id="price" name="price" class="form-control" min="0" required>
            </div>

            <div class="form-group mb-3">
                <label for="category_id">Danh mục:</label>
                <select id="category_id" name="category_id" class="form-control" required>
                    <option value="">Đang tải danh mục...</option>
                    <!-- Danh mục sẽ được tải từ API -->
                </select>
            </div>

            <div class="form-group mb-4">
                <label for="image">Hình ảnh sản phẩm:</label>
                <input type="file" id="image" name="image" class="form-control <?php echo isset($errors['image']) ? 'is-invalid' : ''; ?>" accept="image/png, image/jpeg, image/gif">
                <?php if (isset($errors['image'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['image']); ?></div><?php endif; ?>
            </div>
            
            <!-- API theo Bài 5 không xử lý upload file ảnh trực tiếp từ form này -->
            <!-- Nếu bạn muốn cho phép nhập URL ảnh, thêm input text:
            <div class="form-group mb-4">
                <label for="image_url">URL Hình ảnh (tùy chọn):</label>
                <input type="text" id="image_url" name="image_url" class="form-control" placeholder="https://example.com/image.jpg">
            </div>
            -->

            <button type="submit" class="btn btn-primary w-100 btn-custom">
                <i class="fas fa-plus-circle mr-2"></i>Thêm sản phẩm
            </button>
        </form>

        <div class="mt-3 text-center">
            <a href="/project1/Product/list" class="backto text-decoration-none">
                <i class="fas fa-arrow-left mr-1"></i>Quay lại danh sách
            </a>
        </div>
    </div>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/project1/app/views/shares/footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const categorySelect = document.getElementById('category_id');
    const addProductForm = document.getElementById('add-product-form');
    const formMessageContainer = document.getElementById('form-message');
    const baseUrl = '/project1';

    // Tải danh mục cho dropdown
    console.log("Fetching categories for add form from:", `${baseUrl}/api/category`);
    fetch(`${baseUrl}/api/category`)
        .then(response => {
            if (!response.ok) throw new Error(`Lỗi tải danh mục: ${response.status}`);
            return response.json();
        })
        .then(categories => {
            console.log("Categories received for add form:", categories);
            categorySelect.innerHTML = '<option value="">-- Chọn danh mục --</option>';
            if (categories && Array.isArray(categories) && categories.length > 0) {
                categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.id;
                    option.textContent = category.name;
                    categorySelect.appendChild(option);
                });
            } else {
                categorySelect.innerHTML = '<option value="">Không có danh mục nào</option>';
            }
        })
        .catch(error => {
            console.error('Lỗi khi tải danh mục cho form thêm:', error);
            categorySelect.innerHTML = `<option value="">Lỗi tải danh mục</option>`;
            formMessageContainer.innerHTML = `<div class="alert alert-warning">Không thể tải danh mục: ${error.message}</div>`;
        });

    // Xử lý submit form
    addProductForm.addEventListener('submit', function(event) {
        event.preventDefault();
        formMessageContainer.innerHTML = ''; 

        let name = document.getElementById('name').value.trim();
        let price = document.getElementById('price').value;
        let categoryId = document.getElementById('category_id').value;
        let description = document.getElementById('description').value.trim();
        let clientErrors = [];

        if (name.length < 3 || name.length > 100) {
            clientErrors.push('Tên sản phẩm phải từ 3 đến 100 ký tự.');
        }
        if (description.length === 0) {
            clientErrors.push('Mô tả không được để trống.');
        }
        if (price <= 0 || isNaN(parseFloat(price))) {
            clientErrors.push('Giá phải là một số dương.');
        }
        if (!categoryId) {
            clientErrors.push('Vui lòng chọn danh mục.');
        }

        if (clientErrors.length > 0) {
            formMessageContainer.innerHTML = `<div class="alert alert-danger"><ul>${clientErrors.map(e => `<li>${e}</li>`).join('')}</ul></div>`;
            return;
        }

        const formData = new FormData(this);
        const jsonData = {};
        formData.forEach((value, key) => {
            if (key === 'price') jsonData[key] = parseFloat(value);
            else if (key === 'category_id') jsonData[key] = parseInt(value);
            else jsonData[key] = value.trim();
        });
        // Nếu có trường image_url dạng text:
        // if (document.getElementById('image_url')) {
        //     jsonData['image_url'] = document.getElementById('image_url').value.trim() || null;
        // }


        console.log("Submitting new product (JSON):", JSON.stringify(jsonData));
        fetch(`${baseUrl}/api/product`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(jsonData)
        })
        .then(response => {
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.indexOf("application/json") !== -1) {
                return response.json().then(data => ({ status: response.status, ok: response.ok, body: data }));
            } else {
                // Nếu không phải JSON, đọc text để debug
                return response.text().then(text => {
                    console.error("Add product API response was not JSON:", text.substring(0,500));
                    throw new Error("Phản hồi từ server không phải JSON khi thêm sản phẩm.");
                });
            }
        })
        .then(res => {
            console.log("Add product API response object:", res);
            if (res.ok && res.body.message && res.body.message.toLowerCase().includes('successfully')) {
                formMessageContainer.innerHTML = '<div class="alert alert-success">Thêm sản phẩm thành công! Đang chuyển hướng...</div>';
                addProductForm.reset();
                categorySelect.value = ""; // Reset dropdown
                setTimeout(() => { window.location.href = `http://localhost:90/project1/Product#`; }, 500);
            } else {
                let errorMsg = 'Thêm sản phẩm thất bại.';
                if (res.body && res.body.message) errorMsg += ` ${res.body.message}`;
                if (res.body && res.body.errors) { 
                    errorMsg += '<ul>';
                    for (const field in res.body.errors) {
                        errorMsg += `<li>${field}: ${res.body.errors[field]}</li>`;
                    }
                    errorMsg += '</ul>';
                }
                formMessageContainer.innerHTML = `<div class="alert alert-danger">${errorMsg}</div>`;
            }
        })
        .catch(error => {
            console.error('Lỗi khi thêm sản phẩm:', error);
            formMessageContainer.innerHTML = `<div class="alert alert-danger">Đã có lỗi xảy ra: ${error.message}. Vui lòng thử lại.</div>`;
        });
    });
});
</script>
