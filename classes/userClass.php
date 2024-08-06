<?php
class User
{
    private $conn;
    private $table_name = "users";

    public $user_id;
    public $username;
    public $password;
    public $email;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function register()
    {
        $query = "INSERT INTO " . $this->table_name . " SET username=:username, password=:password, email=:email";
        $stmt = $this->conn->prepare($query);

        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->email = htmlspecialchars(strip_tags($this->email));

        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":email", $this->email);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function login()
    {
        // Check for admin credentials
        if ($this->email === 'admin@speaker.com' && $this->password === 'speaker@123') {
            $this->user_id = 1; // Set a fixed ID for admin or retrieve from database if needed
            $this->username = 'Admin';
            return true;
        }

        // Check for regular user credentials
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email LIMIT 0,1";
        $stmt = $this->conn->prepare($query);

        $this->email = htmlspecialchars(strip_tags($this->email));
        $stmt->bindParam(":email", $this->email);

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($this->password, $row['password'])) {
            $this->user_id = $row['user_id'];
            $this->username = $row['username'];
            return true;
        }
        return false;
    }
    public function read()
    {
        $query = "SELECT user_id, username, email, created_at FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }
}
?>