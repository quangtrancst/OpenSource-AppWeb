<?php 
// Giả sử controller của bạn truyền ID sản phẩm vào view này qua $data['product_id']
// Hoặc nếu bạn giữ nguyên biến $product từ controller cũ, có thể lấy $product->id
$product_id_to_edit = $data['product_id'] ?? ($product->id ?? null);

include $_SERVER['DOCUMENT_ROOT'] . '/project1/app/views/shares/header.php';
?>

<div class="container mt-5 form-container" style="padding-top: 80px;">
    <div class="form-content bg-white p-4 p-md-5 rounded shadow-lg">
        <h1 class="text-center mb-4">Sửa sản phẩm</h1>

        <div id="form-message-edit" class="mb-3"></div>

        <?php if (!$product_id_to_edit) : ?>
            <div class="alert alert-danger">Lỗi: Không tìm thấy ID sản phẩm để chỉnh sửa.</div>
        <?php else : ?>
            <form id="edit-product-form"> <input type="hidden" id="id" name="id" value="<?php echo htmlspecialchars($product_id_to_edit); ?>">

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
                        </select>
                </div>

                <div class="form-group mb-3">
                    <label>Hình ảnh hiện tại:</label><br>
                    <?php if (!empty($existing_image_url_value)): ?>
                        <img src="/project1/<?php echo htmlspecialchars($existing_image_url_value); ?>" alt="Ảnh sản phẩm hiện tại" style="max-width: 150px; max-height: 150px; margin-bottom: 10px; border-radius: .25rem;">
                    <?php else: ?>
                        <p class="text-muted">Không có ảnh.</p>
                    <?php endif; ?>
                </div>

                <div class="form-group mb-4">
                    <label for="image">Chọn ảnh mới (để trống nếu không muốn thay đổi):</label>
                    <input type="file" id="image" name="image" class="form-control <?php echo isset($errors['image']) ? 'is-invalid' : ''; ?>" accept="image/png, image/jpeg, image/gif">
                    <?php if (isset($errors['image'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['image']); ?></div><?php endif; ?>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 btn-custom">
                    <i class="fas fa-save mr-2"></i>Lưu thay đổi
                </button>
            </form>
        <?php endif; ?>

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
    const productId = document.getElementById('id').value; // Lấy ID từ input hidden
    const editProductForm = document.getElementById('edit-product-form');
    const nameInput = document.getElementById('name');
    const descriptionInput = document.getElementById('description');
    const priceInput = document.getElementById('price');
    const categorySelect = document.getElementById('category_id');
    // const imageUrlInput = document.getElementById('image_url_edit'); // Nếu dùng
    // const currentImagePreview = document.getElementById('current-image-preview-edit'); // Nếu dùng
    const formMessageContainer = document.getElementById('form-message-edit');
    const baseUrl = '/project1';

    if (!productId) {
        formMessageContainer.innerHTML = '<div class="alert alert-danger">Không thể tải dữ liệu: ID sản phẩm không hợp lệ.</div>';
        if(editProductForm) editProductForm.style.display = 'none'; // Ẩn form nếu không có ID
        return;
    }

    // Hàm fetch và điền danh mục, sau đó chọn danh mục hiện tại của sản phẩm
    function loadCategoriesAndSelect(currentCategoryId) {
        fetch(`${baseUrl}/api/category`)
            .then(response => {
                if (!response.ok) throw new Error(`Lỗi tải danh mục: ${response.status}`);
                return response.json();
            })
            .then(categories => {
                categorySelect.innerHTML = '<option value="">-- Chọn danh mục --</option>';
                if (categories && categories.length > 0) {
                    categories.forEach(category => {
                        const option = document.createElement('option');
                        option.value = category.id;
                        option.textContent = category.name;
                        if (category.id == currentCategoryId) {
                            option.selected = true;
                        }
                        categorySelect.appendChild(option);
                    });
                } else {
                     categorySelect.innerHTML = '<option value="">Không có danh mục nào</option>';
                }
            })
            .catch(error => {
                console.error('Lỗi tải danh mục:', error);
                categorySelect.innerHTML = `<option value="">Lỗi tải danh mục</option>`;
                formMessageContainer.insertAdjacentHTML('beforeend', `<div class="alert alert-warning">${error.message}</div>`);
            });
    }

    // Tải dữ liệu sản phẩm hiện tại để điền vào form
    fetch(`${baseUrl}/api/product/${productId}`)
        .then(response => {
            if (!response.ok) {
                 return response.json().then(errData => {
                    throw new Error(errData.message || `Lỗi HTTP ${response.status} khi tải sản phẩm.`);
                });
            }
            return response.json();
        })
        .then(product => {
            if (!product) {
                throw new Error('Không tìm thấy dữ liệu sản phẩm.');
            }
            nameInput.value = product.name || '';
            descriptionInput.value = product.description || '';
            priceInput.value = parseFloat(product.price || 0);
            // imageUrlInput.value = product.image_url || ''; // Nếu có
            // if (product.image_url) { // Nếu có
            //     currentImagePreview.innerHTML = `<img src="${baseUrl}/${product.image_url}" alt="Ảnh hiện tại" style="max-width: 150px; border-radius: .2em;" onerror="this.style.display='none';" >`;
            // }
            loadCategoriesAndSelect(product.category_id); // Tải danh mục và chọn
        })
        .catch(error => {
            console.error('Lỗi tải chi tiết sản phẩm:', error);
            formMessageContainer.innerHTML = `<div class="alert alert-danger">Không thể tải dữ liệu sản phẩm: ${error.message}</div>`;
            if(editProductForm) editProductForm.style.display = 'none'; // Ẩn form nếu lỗi
            loadCategoriesAndSelect(null); // Vẫn cố tải danh mục
        });

    // Xử lý submit form
    if(editProductForm){
        editProductForm.addEventListener('submit', function(event) {
            event.preventDefault();
            formMessageContainer.innerHTML = '';

            // Client-side validation (tương tự add form)
            // ...

            const formData = new FormData(this);
            const jsonData = {};
            formData.forEach((value, key) => {
                if (key === 'price') jsonData[key] = parseFloat(value);
                else if (key === 'category_id' || key === 'id') jsonData[key] = parseInt(value);
                else jsonData[key] = value;
            });
            // jsonData['image_url'] = imageUrlInput.value || null; // Nếu có trường image_url text


            fetch(`${baseUrl}/api/product/${productId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(jsonData)
            })
            .then(response => {
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.indexOf("application/json") !== -1) {
                    return response.json().then(data => ({ status: response.status, ok: response.ok, body: data }));
                } else {
                    return response.text().then(text => { throw new Error("Server response was not JSON: " + text) });
                }
            })
            .then(res => {
                if (res.ok && res.body.message === 'Product updated successfully') {
                    formMessageContainer.innerHTML = '<div class="alert alert-success">Cập nhật sản phẩm thành công! Đang chuyển hướng...</div>';
                    setTimeout(() => { window.location.href = `${baseUrl}/Product/`; }, 500);
                } else {
                    let errorMsg = 'Cập nhật sản phẩm thất bại.';
                    if (res.body && res.body.message) errorMsg += ` ${res.body.message}`;
                    if (res.body && res.body.errors) { // Xử lý nếu API trả về errors cụ thể
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
                console.error('Lỗi cập nhật sản phẩm:', error);
                formMessageContainer.innerHTML = `<div class="alert alert-danger">Đã có lỗi xảy ra: ${error.message}. Vui lòng thử lại.</div>`;
            });
        });
    }
});
</script>