<?php
require_once('app/config/database.php');
require_once('app/models/AccountModel.php');
require_once('app/helpers/SessionHelper.php');
require_once('app/utils/JWTHandler.php');

$envPath = __DIR__ . '/../../../env/.env';
if (file_exists($envPath)) {
    $envVariables = parse_ini_file($envPath);
    foreach ($envVariables as $key => $value) {
        putenv("$key=$value"); // Load environment variables
    }
} else {
    die("File .env không tồn tại tại đường dẫn: $envPath");
}

class AccountController
{
    private $accountModel;
    private $jwtHandler;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->accountModel = new AccountModel($this->db);
        $this->jwtHandler = new JWTHandler();
    }

    public function register()
    {
        include 'app/views/account/register.php';
    }

    public function processRegistration()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $fullname = $_POST['fullname'] ?? '';
            $password = $_POST['password'] ?? '';
            $errors = [];

            // Validation cơ bản
            if (empty($username)) { $errors['username'] = "Tên đăng nhập không được để trống."; }
            if ($this->accountModel->isUsernameExists($username)) {
                $errors['username_exists'] = "Tên đăng nhập đã tồn tại!";
            }
            if (empty($fullname)) { $errors['fullname'] = "Họ và tên không được để trống."; }
            if (empty($password) || strlen($password) < 6) { $errors['password'] = "Mật khẩu phải có ít nhất 6 ký tự."; }

            if (!empty($errors)) {
                $_SESSION['register_errors'] = $errors;
                $_SESSION['register_old_input'] = $_POST;
                header('Location: /project1/Account/register');
                exit;
            }

            // Gọi phương thức register đã được cập nhật trong model, gán role 'user' mặc định
            if ($this->accountModel->register($username, $fullname, $password, 'user')) { 
                $_SESSION['success_message'] = "Đăng ký thành công! Vui lòng đăng nhập.";
                header('Location: /project1/Account/login');
                exit;
            } else {
                $_SESSION['register_errors'] = ['db' => "Đăng ký thất bại do lỗi hệ thống."];
                $_SESSION['register_old_input'] = $_POST;
                header('Location: /project1/Account/register');
                exit;
            }
        }
        // Nếu không phải POST, chuyển hướng về trang đăng ký
        header('Location: /project1/Account/register');
        exit;
    }

    public function login()
    {
        include 'app/views/account/login.php';
    }

    public function logout()
    {
        SessionHelper::logout();
        header('Location: /project1/Account/login');
        exit;
    }

    public function checkLogin()
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST');

        $data = json_decode(file_get_contents("php://input"), true);
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        $user = $this->accountModel->login($username); // Phương thức này lấy tất cả các cột, bao gồm cả 'role'

        if ($user && password_verify($password, $user->password)) {
            // Đăng nhập thành công
            
            // **SỬA LỖI PHÂN QUYỀN: THÊM 'ROLE' VÀO PAYLOAD CỦA JWT**
            $payload = [
                'id' => $user->id,
                'username' => $user->username,
                'role' => $user->role // Thêm dòng này!
            ];

            $token = $this->jwtHandler->encode($payload);

            // **SỬA LỖI PHÂN QUYỀN: ĐỒNG THỜI THIẾT LẬP SESSION CHO PHP**
            // Điều này giúp các controller web (như ProductController) có thể dùng SessionHelper::isAdmin()
            SessionHelper::start(); // Đảm bảo session đã bắt đầu
            $_SESSION['user_id'] = $user->id;
            $_SESSION['username'] = $user->username;
            $_SESSION['role'] = $user->role;


            // Trả về token cho client
            http_response_code(200);
            echo json_encode(['token' => $token]);
        } else {
            // Đăng nhập thất bại
            http_response_code(401); // Unauthorized
            echo json_encode(['message' => 'Tên đăng nhập hoặc mật khẩu không chính xác.']);
        }
    }

    public function googleCallback()
    {
        $client_id = getenv('GOOGLE_CLIENT_ID');
        $client_secret = getenv('GOOGLE_CLIENT_SECRET');
        $redirect_uri = getenv('REDIRECT_URI');

        if (!$client_id || !$client_secret || !$redirect_uri) {
            die('Error: Google OAuth credentials are not set.');
        }

        if (isset($_GET['code'])) {
            $tokenUrl = "https://oauth2.googleapis.com/token";
            $data = [
                'code' => $_GET['code'],
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'redirect_uri' => $redirect_uri,
                'grant_type' => 'authorization_code',
            ];

            $ch = curl_init($tokenUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);

            $token = json_decode($response, true);
            if (!isset($token['access_token'])) {
                die('Error: Unable to retrieve access token. Response: ' . $response);
            }

            $userInfoUrl = "https://www.googleapis.com/oauth2/v1/userinfo?access_token=" . $token['access_token'];
            $userInfo = json_decode(file_get_contents($userInfoUrl), true);

            if (!isset($userInfo['email']) || !isset($userInfo['name'])) {
                die('Error: Missing email or name from Google API response.');
            }

            $this->handleSocialLogin($userInfo['email'], $userInfo['name']);
        } else {
            die('Error: Authorization code is missing.');
        }
    }

    private function handleSocialLogin($email, $name)
    {
        if (!$email || !$name) {
            die('Error: Missing email or name from social login.');
        }

        $user = $this->accountModel->findByEmail($email);
        if (!$user) {
            $this->accountModel->register($email, $name, 'default_password');
        }

        $_SESSION['username'] = $email;
        $_SESSION['role'] = 'user';
        header('Location: /project1/Product');
        exit;
    }
}
?>