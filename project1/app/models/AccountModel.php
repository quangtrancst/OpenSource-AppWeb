<?php
class AccountModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function register($username, $fullname, $password)
    {
        if (!$password) {
            $password = 'default_password';
        }
        
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO account (username, fullname, password) VALUES (:username, :fullname, :password)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':password', $hashedPassword);
        return $stmt->execute();
    }

    public function login($username)
    {
        $query = "SELECT * FROM account WHERE username = :username";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    public function isUsernameExists($username)
    {
        $query = "SELECT COUNT(*) as count FROM account WHERE username = :username";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->count > 0;
    }
    public function findByEmail($email)
    {
        $query = "SELECT * FROM account WHERE username = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}