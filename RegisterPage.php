<?php
session_start();
include 'Database.php';

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

        <form method="POST">
            <?php if(!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <?php if(isset($_SESSION['success'])) {
                echo "<p style='color:green;'>".$_SESSION['success']."</p>";
                unset($_SESSION['success']);
            } ?>

            <div class="input-box">
                <img src="user.png" class="icons" alt="Username">
                <input type="text" name="username" placeholder="First Name" value="<?= htmlspecialchars($username) ?>" required>
            </div>

            <div class="input-box">
                <img src="emaill.png" class="icons" alt="Email">
                <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($email) ?>" required>
            </div>

            <div class="input-box">
                <img src="pass.png" class="icons" alt="Password">
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <div class="input-box">
                <img src="pass.png" class="icons" alt="Confirm Password">
                <input type="password" name="confirmPassword" placeholder="Confirm Password" required>
            </div>

            <button type="submit" name='submit' class="button">Sign Up</button>
            <p>Tashmë keni një llogari? <a href="LogIn.php">Kyçu</a></p>
        </form>
    </div>
</div>
</body>
</html>