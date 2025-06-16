<?php 
// File: app/views/account/register.php
include 'app/views/shares/header.php'; 
// Lấy lỗi và input cũ từ session (nếu có)
$errors = $_SESSION['register_errors'] ?? [];
$old_input = $_SESSION['register_old_input'] ?? [];
unset($_SESSION['register_errors']); // Xóa đi sau khi đã lấy
unset($_SESSION['register_old_input']);
?>

<div class="container mt-5" style="padding-top: 80px; max-width: 600px;">
    <h1 class="text-center mb-4">Đăng ký tài khoản</h1>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <strong>Vui lòng sửa các lỗi sau:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Form sẽ submit đến action mới là processRegistration -->
    <form method="POST" action="/project1/Account/processRegistration" class="mt-4 card p-4 shadow-sm">
        <div class="form-group">
            <label for="username">Tên đăng nhập:</label>
            <input type="text" id="username" name="username" class="form-control <?php echo isset($errors['username']) || isset($errors['username_exists']) ? 'is-invalid' : ''; ?>" 
                   value="<?php echo htmlspecialchars($old_input['username'] ?? ''); ?>" required>
            <?php if (isset($errors['username'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['username']); ?></div><?php endif; ?>
            <?php if (isset($errors['username_exists'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['username_exists']); ?></div><?php endif; ?>
        </div>
        <div class="form-group">
            <label for="fullname">Họ và tên:</label>
            <input type="text" id="fullname" name="fullname" class="form-control <?php echo isset($errors['fullname']) ? 'is-invalid' : ''; ?>" 
                   value="<?php echo htmlspecialchars($old_input['fullname'] ?? ''); ?>" required>
            <?php if (isset($errors['fullname'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['fullname']); ?></div><?php endif; ?>
        </div>
        <div class="form-group">
            <label for="password">Mật khẩu (ít nhất 6 ký tự):</label>
            <input type="password" id="password" name="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" required>
            <?php if (isset($errors['password'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['password']); ?></div><?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Đăng ký</button>
        <p class="text-center mt-3">Đã có tài khoản? <a href="/project1/Account/login">Đăng nhập ngay</a></p>
    </form>
</div>
<?php include 'app/views/shares/footer.php'; ?>
