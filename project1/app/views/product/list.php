<?php 
// File: app/views/product/list.php (Đã cập nhật cho Bài 6)
include $_SERVER['DOCUMENT_ROOT'] . '/project1/app/views/shares/header.php'; 
?>

<div class="container" style="margin-top: 100px; margin-bottom: 30px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="Top-item" style="font-size: 1.8rem; font-weight: bold;">Danh sách sản phẩm</h1>
    </div>

    <!-- Carousel Slider -->
    <div id="productCarousel" class="carousel slide mb-5" data-ride="carousel">
        <ol class="carousel-indicators">
            <?php
            $images_carousel = [
                '/project1/public/images/nike-just-do-it.avif',
                '/project1/public/images/women-s-shoes-clothing-accessories.avif',
            ];
            foreach ($images_carousel as $key => $image_c): ?>
                <li data-target="#productCarousel" data-slide-to="<?php echo $key; ?>" class="<?php echo ($key == 0) ? 'active' : ''; ?>"></li>
            <?php endforeach; ?>
        </ol>
        <div class="carousel-inner" style="border-radius: 0.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <?php
            $first_carousel = true;
            foreach ($images_carousel as $image_c): ?>
                <div style="height: 60vh; max-height: 450px; overflow: hidden;" class="carousel-item <?php echo $first_carousel ? 'active' : ''; ?>">
                    <img src="<?php echo htmlspecialchars($image_c); ?>" alt="Ảnh banner" class="d-block w-100" style="object-fit: cover; height: 100%;">
                </div>
                <?php $first_carousel = false; ?>
            <?php endforeach; ?>
        </div>
        <a class="carousel-control-prev" href="#productCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#productCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
    <!-- Kết thúc Carousel -->

    <div id="product-list-container" class="row">
        <div class="col-12 text-center">
            <div id="loading-message" class="lead py-5">
                <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                <p class="mt-2">Đang tải sản phẩm...</p>
            </div>
            <div id="error-message-container" class="alert alert-danger" style="display: none;"></div>
        </div>
    </div>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/project1/app/views/shares/footer.php'; ?>

<script>
// Bỏ biến isAdmin được tạo từ PHP, chúng ta sẽ lấy từ JWT
// const isAdmin = <?php echo json_encode(SessionHelper::isAdmin()); ?>; 
const baseUrl = '/project1'; 

// Hàm helper để giải mã JWT payload (không kiểm tra chữ ký, chỉ để lấy thông tin)
function parseJwt(token) {
    try {
        const base64Url = token.split('.')[1];
        const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
        const jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c) {
            return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
        }).join(''));
        return JSON.parse(jsonPayload);
    } catch (e) {
        return null;
    }
}


document.addEventListener("DOMContentLoaded", function() {
    const productListContainer = document.getElementById('product-list-container');
    const loadingMessageEl = document.getElementById('loading-message');
    const errorMessageContainerEl = document.getElementById('error-message-container');
    const addProductBtn = document.getElementById('add-product-btn');

    function displayError(message) {
        console.error("Displaying Error:", message);
        if (loadingMessageEl) loadingMessageEl.style.display = 'none';
        if (errorMessageContainerEl) {
            errorMessageContainerEl.textContent = message;
            errorMessageContainerEl.style.display = 'block';
        }
        if (productListContainer) productListContainer.innerHTML = '';
    }

    // ===================================================================
    // LOGIC CẬP NHẬT THEO BÀI 6 (BẢO MẬT BẰNG JWT)
    // ===================================================================

    // 1. Lấy token từ localStorage
    const token = localStorage.getItem('jwtToken');

    // 2. Kiểm tra nếu không có token, chuyển hướng về trang đăng nhập
    if (!token) {
        alert('Vui lòng đăng nhập để xem trang này.');
        window.location.href = `${baseUrl}/Account/login`;
        return; // Dừng việc thực thi script
    }

    // 3. Giải mã token để lấy role và quyết định quyền admin phía client
    const decodedToken = parseJwt(token);
    // payload của token có dạng {iat, exp, data: {id, username, role}}
    const userRole = decodedToken && decodedToken.data ? decodedToken.data.role : 'user';
    const isAdmin = (userRole === 'admin');

    // 4. Hiển thị nút "Thêm sản phẩm" nếu là admin
    if (isAdmin && addProductBtn) {
        addProductBtn.style.display = 'inline-block';
    }


    // 5. Nếu có token, thực hiện fetch với Authorization header
    fetch(`${baseUrl}/api/product`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + token // Gửi token trong header
        }
    })
    .then(response => {
        if (response.status === 401) { // Lỗi Unauthorized (token sai hoặc hết hạn)
            localStorage.removeItem('jwtToken'); // Xóa token cũ
            throw new Error('Phiên đăng nhập không hợp lệ hoặc đã hết hạn. Vui lòng đăng nhập lại.');
        }
        if (!response.ok) {
            return response.json().then(err => { throw new Error(err.message || `Lỗi HTTP ${response.status}`) });
        }
        return response.json();
    })
    .then(products => {
        if (loadingMessageEl) loadingMessageEl.style.display = 'none';
        
        productListContainer.innerHTML = ''; 

        if (products && Array.isArray(products) && products.length > 0) {
            products.forEach(product => {
                const productId = product.id;
                const productName = product.name || 'Sản phẩm không tên';
                const productDescription = product.description || 'Không có mô tả.';
                const productPrice = parseFloat(product.price || 0).toLocaleString('vi-VN');
                const productCategory = product.category_name || 'Chưa phân loại';
                let imageUrl = product.image_url ? `${baseUrl}/${product.image_url}` : `${baseUrl}/public/images/default-placeholder.png`;

                const productCard = `
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="list-group-item h-100 d-flex flex-column p-3 shadow-sm">
                            <a href="${baseUrl}/Product/show/${productId}" class="text-center">
                                <img src="${imageUrl}" alt="${productName}" style="width: 100%; height: 180px; object-fit: contain; margin-bottom: 15px; border-radius: .25rem;" onerror="this.src='${baseUrl}/public/images/default-placeholder.png';">
                            </a>
                            <div class="mt-2 flex-grow-1 d-flex flex-column">
                                <h5 style="font-size: 1.1rem; font-weight: 600; min-height: 44px;"><a href="${baseUrl}/Product/show/${productId}" class="text-dark text-decoration-none">${productName}</a></h5>
                                <p class="content small text-muted flex-grow-1">${productDescription.substring(0, 70)}...</p>
                                <p class="price" style="font-size: 1.2rem; color: #dc3545; font-weight: bold;">${productPrice} VNĐ</p>
                            </div>
                            <div class="mt-auto pt-2 border-top">
                                <a href="${baseUrl}/Product/addToCart/${productId}" class="btn btn-primary btn-sm w-100 mb-2"><i class="fas fa-cart-plus mr-1"></i> Thêm vào giỏ</a>
                                <!-- Hiển thị nút Sửa/Xóa dựa trên biến isAdmin của JavaScript -->
                                ${isAdmin ? `
                                <div class="d-flex justify-content-between">
                                    <a href="${baseUrl}/Product/edit/${productId}" class="btn btn-outline-warning btn-sm" style="flex: 1; margin-right: 5px;"><i class="fas fa-edit"></i> Sửa</a>
                                    <button class="btn btn-outline-danger btn-sm delete-product-btn" data-id="${productId}" style="flex: 1; margin-left: 5px;"><i class="fas fa-trash"></i> Xóa</button>
                                </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>`;
                productListContainer.insertAdjacentHTML('beforeend', productCard);
            });

            document.querySelectorAll('.delete-product-btn').forEach(button => {
                button.addEventListener('click', function() {
                    deleteProduct(this.getAttribute('data-id'));
                });
            });

        } else {
            productListContainer.innerHTML = '<div class="col-12"><p class="text-center text-muted lead py-5">Không có sản phẩm nào để hiển thị.</p></div>';
        }
    })
    .catch(error => {
        // Nếu có lỗi, chuyển hướng về trang đăng nhập
        if (error.message.includes('hết hạn') || error.message.includes('hợp lệ')) {
             alert(error.message);
             window.location.href = `${baseUrl}/Account/login`;
        } else {
            displayError(`Không thể tải danh sách sản phẩm. ${error.message}.`);
        }
    });
});

// Cập nhật hàm deleteProduct để gửi token
function deleteProduct(id) {
    const token = localStorage.getItem('jwtToken');
    if (!token) {
        alert('Vui lòng đăng nhập để thực hiện hành động này.');
        window.location.href = `${baseUrl}/Account/login`;
        return;
    }

    // Lấy quyền từ token để kiểm tra phía client (tùy chọn nhưng nên có)
    const decodedToken = parseJwt(token);
    const isAdmin = decodedToken && decodedToken.data && decodedToken.data.role === 'admin';
    if (!isAdmin) {
        alert('Bạn không có quyền thực hiện hành động này.');
        return;
    }

    if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')) {
        fetch(`${baseUrl}/api/product/${id}`, {
            method: 'DELETE',
            headers: {
                'Authorization': 'Bearer ' + token // Gửi token để xác thực quyền
            }
        })
        .then(response => {
            if(response.status === 401) { throw new Error('Phiên đăng nhập không hợp lệ hoặc đã hết hạn.'); }
            // Nếu API của bạn có thể trả về lỗi khác 401 (ví dụ 403 Forbidden), bạn cũng nên xử lý ở đây
            if(!response.ok) { return response.json().then(err => { throw new Error(err.message || `Lỗi HTTP ${response.status}`) }); }
            return response.json();
        })
        .then(data => {
            if (data.message && data.message.toLowerCase().includes('successfully')) {
                alert('Sản phẩm đã được xóa thành công!');
                document.querySelector(`.delete-product-btn[data-id="${id}"]`)?.closest('.col-lg-3')?.remove();
            } else {
                alert('Xóa sản phẩm thất bại: ' + (data.message || 'Lỗi không xác định.'));
            }
        })
        .catch(error => {
            console.error('Lỗi khi xóa sản phẩm:', error);
            alert('Đã xảy ra lỗi: ' + error.message);
            if (error.message.includes('hết hạn')) {
                 window.location.href = `${baseUrl}/Account/login`;
            }
        });
    }
}
</script>
