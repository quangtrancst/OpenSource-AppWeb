<!-- Trong file footer.php, trước thẻ </body> -->

<!-- jQuery, Popper.js, và Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- Script xử lý hiển thị đăng nhập/đăng xuất bằng JWT -->
<script>
    // Hàm đăng xuất
    function logout(event) {
        event.preventDefault(); // Ngăn link chuyển trang mặc định
        console.log("Logging out...");
        localStorage.removeItem('jwtToken'); // Xóa token
        // Chuyển hướng về trang đăng nhập
        window.location.href = '/project1/Account/login';
    }

    // Hàm giải mã JWT để lấy username (đơn giản, không kiểm tra chữ ký)
    function parseJwt(token) {
        try {
            return JSON.parse(atob(token.split('.')[1]));
        } catch (e) {
            return null;
        }
    }

    // Chạy khi toàn bộ DOM đã được tải
    document.addEventListener("DOMContentLoaded", function() {
        const token = localStorage.getItem('jwtToken');
        const navLogin = document.getElementById('nav-login');
        const navRegister = document.getElementById('nav-register');
        const navUserInfo = document.getElementById('nav-user-info');
        const usernameDisplay = document.getElementById('username-display');

        if (token) {
            // Nếu có token -> người dùng đã đăng nhập
            if (navLogin) navLogin.style.display = 'none';
            if (navRegister) navRegister.style.display = 'none';
            if (navUserInfo) navUserInfo.style.display = 'block';
            
            // Giải mã token để lấy username và hiển thị
            const decodedToken = parseJwt(token);
            if (usernameDisplay && decodedToken && decodedToken.data && decodedToken.data.username) {
                usernameDisplay.textContent = decodedToken.data.username;
            } else {
                 usernameDisplay.textContent = 'User';
            }

        } else {
            // Nếu không có token -> người dùng chưa đăng nhập
            if (navLogin) navLogin.style.display = 'block';
            if (navRegister) navRegister.style.display = 'block';
            if (navUserInfo) navUserInfo.style.display = 'none';
        }
    });
</script>