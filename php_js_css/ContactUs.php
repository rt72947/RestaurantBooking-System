<?php
session_start();
include 'Database.php';
include_once 'users.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();
$userModel = new User($conn);

$error = '';
$success = '';
$name = '';
$email = '';
$message = '';

if(isset($_POST['submit'])){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    if(empty($name)){
        $error = "Emri është i detyrueshëm!";
    } elseif(empty($email)){
        $error = "Email është i detyrueshëm!";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = "Email nuk është valid!";
    } elseif(empty($message)){
        $error = "Mesazhi nuk mund të jetë bosh!";
    } elseif(!$userModel->userExists($email)){ 
        $error = "Ky email nuk është regjistruar. Ju lutem përdorni një email ekzistues!";
    } else {
         try {
            $stmt = $conn->prepare("INSERT INTO contacts (name,email,message) VALUES (:name,:email,:message)");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':message' => $message
            ]);
            $success = "Mesazhi u dërgua me sukses!";
            $name = $email = $message = '';
        } catch(PDOException $e){
            $error = "Gabim gjatë dërgimit: " . $e->getMessage();
        }
    }
}

class Contact {
    private $conn;
    private $table_name = 'contacts';

    public function __construct($db){
        $this->conn = $db;
    }

    public function getAllMessages(): array {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table_name} ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteMessage(int $id): bool {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table_name} WHERE id=:id");
        return $stmt->execute([':id'=>$id]);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Us</title>
<link rel="stylesheet" href="ContactUs.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&family=Cinzel:wght@400;600&display=swap" rel="stylesheet"/>
</head>
<body>

<header class="topbar">
  <a class="back" href="Homepage.php">Home Page</a>
</header>

<div class="contact-us">
    <div class="contact-form">
        <h2>Contact Us</h2>
        <p class="contact-text">Na kontaktoni për çdo pyetje apo kërkesë.</p>
        <form id='form' action="" method="post">
             <?php 
                if($success) echo "<p style='color:green;'>$success</p>"; 
                if($error) echo "<p style='color:red;'>$error</p>"; 
            ?>

            <div class="input-box">
                <input id='name' type="text" name="name" placeholder="First Name" value="<?= htmlspecialchars($name) ?>">
            </div>

            <div class="input-box">
                <input id="email" type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($email) ?>">
            </div>

            <div class="input-box message">
                <textarea id='message' name="message" placeholder="Message"><?= htmlspecialchars($message) ?></textarea>
            </div>

            <button type="submit" name="submit" class="button">Submit</button>
        </form>
    </div>
</div>

</body>
</html>