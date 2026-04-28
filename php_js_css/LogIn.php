<?php
session_start();
require_once 'database.php';

$db = new Database();
$conn = $db->getConnection();

$errors = [];
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if ($email === "") {
        $errors["email"] = "Email është i detyrueshëm.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Email nuk është valid.";
    }

    if ($password === "") {
        $errors["password"] = "Password është i detyrueshëm.";
    } elseif (strlen($password) < 6) {
        $errors["password"] = "Password duhet të ketë minimum 6 karaktere.";
    }

    if (empty($errors)) {
        $sql = "SELECT id, name, email, password, role FROM users WHERE email = :email LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ":email" => $email
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            $_SESSION["user_email"] = $user["email"];
            $_SESSION["user_role"] = $user["role"];

            header("Location: Homepage.php");
            exit();
        } else {
            $errors["general"] = "Email ose password gabim.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&family=Cinzel:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="LogIn.css">

    <style>
        .error-message {
            color: #ff6b6b;
            font-size: 13px;
            margin-top: 6px;
            padding-left: 5px;
        }

        .general-error {
            color: #ff6b6b;
            text-align: center;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .input-control.error input {
            border: 1px solid #ff6b6b;
        }
    </style>
</head>

<body>
    <main class="auth">
        <div class="auth-card">
            <h2>Log In</h2>
            <p class="auth-sub">Kyçu për të vazhduar</p>

            <form action="" method="POST" novalidate>

                <?php if (!empty($errors["general"])): ?>
                    <div class="general-error">
                        <?php echo htmlspecialchars($errors["general"]); ?>
                    </div>
                <?php endif; ?>

                <div class="input-control <?php echo !empty($errors["email"]) ? 'error' : ''; ?>">
                    <div class="input-box">
                        <img src="images/user.png" class="icons" alt="Email">
                        <input
                            type="email"
                            name="email"
                            placeholder="Email"
                            value="<?php echo htmlspecialchars($email); ?>"
                        >
                    </div>

                    <?php if (!empty($errors["email"])): ?>
                        <div class="error-message">
                            <?php echo htmlspecialchars($errors["email"]); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="input-control <?php echo !empty($errors["password"]) ? 'error' : ''; ?>">
                    <div class="input-box">
                        <img src="images/pass.png" class="icons" alt="Password">
                        <input
                            type="password"
                            name="password"
                            placeholder="Password"
                        >
                    </div>

                    <?php if (!empty($errors["password"])): ?>
                        <div class="error-message">
                            <?php echo htmlspecialchars($errors["password"]); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn-gold">Log In</button>

                <p class="auth-foot">
                    Nuk ke llogari?
                    <a href="RegisterPage.php">Regjistrohu</a>
                </p>

            </form>
        </div>
    </main>
</body>
</html>