<?php
require_once('app/config/database.php');
require_once('app/models/AccountModel.php');
require_once('app/helpers/SessionHelper.php');


$envPath = __DIR__ . '/../../../env/.env';
if (file_exists($envPath)) {
    $envVariables = parse_ini_file($envPath);
    foreach ($envVariables as $key => $value) {
        putenv("$key=$value"); // Đưa biến vào môi trường
    }
} else {
    die("File .env không tồn tại tại đường dẫn: $envPath");
}

class AccountController
{
    private $accountModel;

    public function __construct()
    {
        $db = (new Database())->getConnection();
        $this->accountModel = new AccountModel($db);
    }

    private function handleSocialLogin($email, $name)
{
    if (!isset($userInfo['email']) || !isset($userInfo['name'])) {
        die('Error: Missing email or name from Google API response.');
    }
    $this->handleSocialLogin($userInfo['email'], $userInfo['name']);

    if (!$email || !$name) {
        die('Error: Missing email or name from social login.');
    }
    $user = $this->accountModel->findByEmail($email);

    if (!$user) {
        // Đăng ký tài khoản mới
        $this->accountModel->register($email, $name, 'default_password', 'user');
    }

    // Đăng nhập
    $_SESSION['username'] = $email;
    $_SESSION['role'] = 'user'; // Hoặc vai trò khác nếu cần
    header('Location: /project1/Product');
    exit;
}

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $fullname = $_POST['fullname'] ;
            $password = $_POST['password'];

            if ($this->accountModel->isUsernameExists($username)) {
                $_SESSION['error_message'] = "Tên đăng nhập đã tồn tại!";
                include 'app/views/account/register.php';
                return;
            }

            if ($this->accountModel->register($username, $fullname, $password)) {
                $_SESSION['success_message'] = "Đăng ký thành công!";
                header('Location: /project1/Account/login');
                exit;
            } else {
                $_SESSION['error_message'] = "Đăng ký thất bại!";
            }
        }
        include 'app/views/account/register.php';
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $user = $this->accountModel->login($username);
            if ($user && password_verify($password, $user->password)) {
                SessionHelper::login($user);
                $_SESSION['role'] = $user->role;
                header('Location: /project1/Product');
                exit;
            } else {
                $_SESSION['error_message'] = "Tên đăng nhập hoặc mật khẩu không đúng!";
            }
        }
        include 'app/views/account/login.php';
    }

    public function logout()
    {
        SessionHelper::logout();
        header('Location: /project1/Account/login');
        exit;
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

            // Gửi yêu cầu POST để lấy access token
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

            // Lấy thông tin người dùng
            $userInfoUrl = "https://www.googleapis.com/oauth2/v1/userinfo?access_token=" . $token['access_token'];
            $userInfo = json_decode(file_get_contents($userInfoUrl), true);

            if (!isset($userInfo['email']) || !isset($userInfo['name'])) {
                die('Error: Missing email or name from Google API response.');
            }

            // Xử lý đăng nhập hoặc đăng ký
            $this->handleSocialLogin($userInfo['email'], $userInfo['name']);
        }else {
            die('Error: Authorization code is missing.');
    }
}
}