<?php
session_start();
include_once 'Database.php';

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
        return $stmt->execute([':name'=>$name, ':email'=>$email, ':password'=>$hashed_password, ':role'=>$role]);
    }

    public function getAllUsers(): array {
        $stmt = $this->conn->prepare("SELECT id,name,email,role,created_at FROM {$this->table_name} ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById(int $id): ?array {
        $stmt = $this->conn->prepare("SELECT id,name,email,role FROM {$this->table_name} WHERE id=:id LIMIT 1");
        $stmt->execute([':id'=>$id]);
        return $stmt->rowCount() > 0 ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
    }

    public function updateUser(int $id, string $name, string $email, ?string $password=null, string $role='user'): bool {
        $stmt = $this->conn->prepare("SELECT id FROM {$this->table_name} WHERE email=:email AND id!=:id");
        $stmt->execute([':email'=>$email, ':id'=>$id]);
        if($stmt->rowCount() > 0) return false;

        if($password){
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("UPDATE {$this->table_name} 
                                          SET name=:name,email=:email,password=:password,role=:role 
                                          WHERE id=:id");
            return $stmt->execute([':name'=>$name, ':email'=>$email, ':password'=>$hashed_password, ':role'=>$role, ':id'=>$id]);
        } else {
            $stmt = $this->conn->prepare("UPDATE {$this->table_name} 
                                          SET name=:name,email=:email,role=:role 
                                          WHERE id=:id");
            return $stmt->execute([':name'=>$name, ':email'=>$email, ':role'=>$role, ':id'=>$id]);
        }
    }

    public function deleteUser(int $id): bool {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table_name} WHERE id=:id");
        return $stmt->execute([':id'=>$id]);
    }

    public function userExists(string $email): bool {
        $stmt = $this->conn->prepare("SELECT id FROM {$this->table_name} WHERE email=:email");
        $stmt->execute([':email'=>$email]);
        return $stmt->rowCount() > 0;
    }
}

$db = new Database();
$conn = $db->getConnection();
$userModel = new User($conn);

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD']=='POST'){
    $action = $_POST['action'] ?? '';

    if($action=='create'){
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirm = $_POST['confirmPassword'];

        if(empty($name)||empty($email)||empty($password)||empty($confirm)){
            $error="Plotëso të gjitha fushat!";
        } elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)){
            $error="Email nuk është valid!";
        } elseif($password!==$confirm){
            $error="Password-at nuk përputhen!";
        } else {
            if($userModel->register($name,$email,$password)) {
                $success="User u krijua me sukses!";
                header("Location: dashboard.php"); exit;
            } else $error="Ky email ekziston tashmë!";
        }
    }

    if($action=='update'){
        $id = $_POST['id'];
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'] ?: null;
        $role = $_POST['role'];

        if($userModel->updateUser($id,$name,$email,$password,$role)) {
            $success="User u përditësua me sukses!";
            header("Location: dashboard.php"); exit;
        } else $error="Ky email ekziston tek një user tjetër ose gabim gjatë përditësimit!";
    }
}

if(isset($_GET['delete'])){
    $id=$_GET['delete'];
    if($userModel->deleteUser($id)) {
        $success="User u fshi me sukses!";
        header("Location: dashboard.php"); exit;
    } else $error="Gabim gjatë fshirjes!";
}

$users = $userModel->getAllUsers();
?>