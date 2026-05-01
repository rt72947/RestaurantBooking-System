<?php
session_start();

require_once 'Database.php';

$db = new Database();
$conn = $db->getConnection();

$error = '';
$username = '';
$email = '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $username = trim($_POST['username']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if(empty($username)){
        $error = "Username është i detyrueshëm!";
    }
    elseif(empty($email)){
        $error = "Email është i detyrueshëm!";
    }
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = "Email nuk është valid!";
    }
    elseif(empty($password)){
        $error = "Password është i detyrueshëm!";
    }
    elseif(!preg_match("/^(?=.*[A-Z])(?=.*[0-9]).{8,}$/", $password)){
        $error = "Password duhet të ketë minimum 8 karaktere, 1 shkronjë të madhe dhe 1 numër!";
    }
    elseif(empty($confirmPassword)){
        $error = "Ju lutem konfirmoni password-in!";
    }
    elseif($password !== $confirmPassword){
        $error = "Password-at nuk përputhen!";
    }
    else{
        $stmt = $conn->prepare("SELECT id FROM users WHERE LOWER(email) = LOWER(:email)");
        $stmt->execute([':email' => $email]);

        if($stmt->rowCount() > 0){
            $error = "Ky email ekziston tashmë!";
        } 
        else{
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (name,email,password,role) 
                                    VALUES (:name,:email,:password,'user')");

            if($stmt->execute([
                ':name'=>$username,
                ':email'=>$email,
                ':password'=>$hashedPassword
            ])){
                $_SESSION['success'] = "Regjistrimi u krye me sukses!";
                header("Location: LogIn.php");
                exit();
            }
            else{
                $error = "Gabim gjatë regjistrimit. Provoni përsëri!";
            }
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
</head>

<body>

<div class="register">
    <div class="register_form">
        <h2>SIGN UP</h2>
        <p class="register-text">Regjistrohu për të vazhduar</p>

        <form method="POST" id="registerForm">

            <?php if(!empty($error)) echo "<p style='color:red; font-style:italic;'>$error</p>"; ?>
            <p id="jsError" style="color:red;"></p>

            <?php 
            if(isset($_SESSION['success'])) {
                echo "<p style='color:green;'>".$_SESSION['success']."</p>";
                unset($_SESSION['success']);
            } 
            ?>

            <div class="input-box">
                <img src="images/user.png" class="icons" alt="Username">
                <input type="text" name="username" placeholder="First Name" value="<?= htmlspecialchars($username) ?>" >
            </div>

            <div class="input-box">
                <img src="images/emaill.png" class="icons" alt="Email">
                <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($email) ?>" >
            </div>

            <div class="input-box">
                <img src="images/pass.png" class="icons" alt="Password">
                <input type="password" name="password" placeholder="Password" >
            </div>

            <div class="input-box">
                <img src="images/pass.png" class="icons" alt="Confirm Password">
                <input type="password" name="confirmPassword" placeholder="Confirm Password" >
            </div>

            <button type="submit" name="submit" class="button">Sign Up</button>

            <p>Tashmë keni një llogari? <a href="LogIn.php">Kyçu</a></p>
        </form>
    </div>
</div>
<script>
document.getElementById("registerForm").addEventListener("submit", function(e){

    let username = document.querySelector("input[name='username']").value.trim();
    let email = document.querySelector("input[name='email']").value.trim();
    let password = document.querySelector("input[name='password']").value;
    let confirmPassword = document.querySelector("input[name='confirmPassword']").value;

    let errorBox = document.getElementById("jsError");
    errorBox.innerText = "";

    if(username === ""){
        e.preventDefault();
        errorBox.innerText = "Username është i detyrueshëm!";
        return;
    }

    let emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
    if(email === ""){
        e.preventDefault();
        errorBox.innerText = "Email është i detyrueshëm!";
        return;
    }
    if(!email.match(emailPattern)){
        e.preventDefault();
        errorBox.innerText = "Email nuk është valid!";
        return;
    }

    let passwordPattern = /^(?=.*[A-Z])(?=.*[0-9]).{8,}$/;
    if(password === ""){
        e.preventDefault();
        errorBox.innerText = "Password është i detyrueshëm!";
        return;
    }
    if(!password.match(passwordPattern)){
        e.preventDefault();
        errorBox.innerText = "Password duhet të ketë minimum 8 karaktere, 1 shkronjë të madhe dhe 1 numër!";
        return;
    }

    if(confirmPassword === ""){
        e.preventDefault();
        errorBox.innerText = "Ju lutem konfirmoni password-in!";
        return;
    }
    if(password !== confirmPassword){
        e.preventDefault();
        errorBox.innerText = "Password-at nuk përputhen!";
        return;
    }
});
</script>
</body>
</html>