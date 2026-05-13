<?php
class User {
    private $conn;
    private $table_name = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($name, $email, $password, $role='user'): bool {
        if($this->userExists($email)) return false;
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare("INSERT INTO {$this->table_name} (name,email,password,role)
                                      VALUES(:name,:email,:password,:role)");

        return $stmt->execute([
            ':name'=>$name,
            ':email'=>$email,
            ':password'=>$hashed_password,
            ':role'=>$role
        ]);
    }

    public function userExists(string $email): bool {
        $stmt = $this->conn->prepare("SELECT id FROM {$this->table_name} WHERE email=:email");
        $stmt->execute([':email'=>$email]);
        return $stmt->rowCount() > 0;
    }

    public function getAllUsers(): array {
        $stmt = $this->conn->prepare("SELECT id,name,email,role FROM {$this->table_name}");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteUser(int $id): bool {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table_name} WHERE id=:id");
        return $stmt->execute([':id'=>$id]);
    }

     public function updateUser(int $id, string $name, string $email, string $role): bool {
        $stmt = $this->conn->prepare("UPDATE {$this->table_name} SET name = :name,email = :email,role = :role WHERE id = :id");

        return $stmt->execute([
            ':id' => $id,
            ':name' => $name,
            ':email' => $email,
            ':role' => $role
        ]);
    }
}