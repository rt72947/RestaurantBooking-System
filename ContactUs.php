<?php
session_start();
include 'Database.php';

$db = new Database();
$conn = $db->getConnection();

if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    if(empty($name) || empty($email) || empty($message)){
        $error = "Ju lutem plotësoni të gjitha fushat!";
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO contacts (name,email,message) VALUES (:name,:email,:message)");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':message' => $message
            ]);
            $success = "Mesazhi u dërgua me sukses!";
        } catch(PDOException $e){
            $error = "Gabim gjatë dërgimit: " . $e->getMessage();
        }
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
    <script defer src="ContactUs.js"></script>
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
                    if(isset($success)) echo "<p style='color:green;'>$success</p>"; 
                    if(isset($error)) echo "<p style='color:red;'>$error</p>"; 
                ?>

                <div class="input-box">
                    <input id='name' type="text" name="name"   placeholder="First Name" >
                    <div class="error"></div>
                </div>
                <div class="input-box">
                    <input id="email" type="text" name="email"  placeholder="Email" >
                    <div class="error"></div>
                </div>
                <div class="input-box message">
                    <textarea id='message' name="message"  placeholder="Message" ></textarea>
                    <div class="error"></div>
                </div>
                <button type="submit" name="submit"  class="button">Submit</button>
            </form>
        </div>
    </div>
</body>
</html>