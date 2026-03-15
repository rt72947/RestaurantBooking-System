<?php
session_start();
include_once 'Database.php';
include_once 'users.php';

$db = new Database();
$conn = $db->getConnection();
$user = new User($conn);

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    if($stmt->rowCount() > 0){
        $error = "Ky email ekziston tashmë! Provoni një tjetër.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (name,email,password,role) VALUES (:name,:email,:password,'user')");
        if($stmt->execute([':name'=>$name, ':email'=>$email, ':password'=>$hashedPassword])){
            $_SESSION['success'] = "Regjistrimi u krye me sukses!";
            header('Location: LogIn.php');
            exit;
        } else {
            $error = "Gabim gjatë regjistrimit. Provoni përsëri!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>
    <link rel="stylesheet" href="RegisterPage.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&family=Cinzel:wght@400;600&display=swap" rel="stylesheet"/>
    <script defer src="RegisterPage.js"></script>
</head>
<body>
    <header class="topbar">
      <a class="back" href="Homepage.php">Home Page</a>
    </header>

    <div class="register">
        <div class="register_form">
            <h2>SIGN UP</h2>
            <p class="register-text">Regjistrohu për të vazhduar</p>
            <form id="form" method="POST" action="">
                <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
                <?php if(isset($_SESSION['success'])) echo "<p style='color:green;'>".$_SESSION['success']."</p>"; ?>

                <div class="input-box" >
                    <img src="user.png" class="icons" alt="Username">
                    <input id="username" type="text"  name="username" placeholder="First Name" >
                    <div class="error"></div>
                </div>
                <div class="input-box">
                    <img src="emaill.png" class="icons" alt="Email">
                    <input id="email" type="text"  name="email"  placeholder="Email" >
                    <div class="error"></div>
                </div>
                <div class="input-box">
                    <img src="pass.png" class="icons" alt="password">
                    <input id="password" type="password" name="password" placeholder="Password" >
                    <div class="error"></div>
                </div>
                <div class="input-box">
                    <img src="pass.png" class="icons" alt="password">
                    <input id="confirmPassword" type="password" name="confirmPassword" placeholder="Confirm password" >
                    <div class="error"></div>
                </div>
                <button type="submit" name="submit" class="button">Sign Up</button>
                <p> Tashmë keni një llogari? <a href="LogIn.php"> Kyçu </a> </p>
            </form>
        </div>
    </div>
</body>
</html>