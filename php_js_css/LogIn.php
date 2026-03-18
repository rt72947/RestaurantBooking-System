<?php
session_start();
require_once 'database.php';

$db = new Database();
$conn = $db->getConnection();

$errors = [];
$success = "";

$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if ($email === "") {
        $errors['email'] = "Email është i detyrueshëm.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Email nuk është valid.";
    }

    if ($password === "") {
        $errors['password'] = "Password është i detyrueshëm.";
    } elseif (strlen($password) < 6) {
        $errors['password'] = "Password duhet të ketë minimum 6 karaktere.";
    }

    if (empty($errors)) {
        $sql = "SELECT id, name, email, password, role FROM users WHERE email = :email LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':email' => $email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];

                if ($user['role'] === 'admin') {
                    header("Location: admin/dashboard.php");
                    exit;
                } else {
                    header("Location: Homepage.php");
                    exit;
                }
            } else {
                $errors['general'] = "Email ose password gabim.";
            }
        } else {
            $errors['general'] = "Email ose password gabim.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sq">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Log In</title>

    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&family=Cinzel:wght@400;600&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="LogIn.css" />

    <style>
      .error {
        color: #ff6b6b;
        font-size: 13px;
        margin-top: 6px;
        padding-left: 4px;
      }

      .general-error {
        color: #ff6b6b;
        font-size: 14px;
        margin-bottom: 14px;
        text-align: center;
      }

      .input-control.success input {
        border: 1px solid #c9a86a;
      }

      .input-control.error input {
        border: 1px solid #ff6b6b;
      }
    </style>
  </head>

  <body>
    <header class="topbar">
      <a class="back" href="Homepage.php">Home Page</a>
    </header>

    <main class="auth">
      <div class="auth-card">
        <h2>Log In</h2>
        <p class="auth-sub">Kyçu për të vazhduar</p>

        <form action="" method="POST" id="form" novalidate>
          <?php if (!empty($errors['general'])): ?>
            <div class="general-error"><?php echo htmlspecialchars($errors['general']); ?></div>
          <?php endif; ?>

          <div class="input-control <?php echo !empty($errors['email']) ? 'error' : ''; ?>">
            <div class="input-box">
              <img src="images/user.png" class="icons" alt="Email" />
              <input
                type="email"
                id="email"
                name="email"
                placeholder="Email"
                value="<?php echo htmlspecialchars($email); ?>"
              />
            </div>
            <div class="error">
              <?php echo !empty($errors['email']) ? htmlspecialchars($errors['email']) : ''; ?>
            </div>
          </div>

          <div class="input-control <?php echo !empty($errors['password']) ? 'error' : ''; ?>">
            <div class="input-box">
              <img src="images/pass.png" class="icons" alt="Password" />
              <input
                type="password"
                id="password"
                name="password"
                placeholder="Password"
              />
            </div>
            <div class="error">
              <?php echo !empty($errors['password']) ? htmlspecialchars($errors['password']) : ''; ?>
            </div>
          </div>

          <button type="submit" class="btn-gold">Log In</button>

          <p class="auth-foot">
            Nuk ke llogari?
            <a href="RegisterPage.php">Regjistrohu</a>
          </p>
        </form>
      </div>
    </main>

    <script>
      document.addEventListener("DOMContentLoaded", () => {
        const form = document.getElementById("form");
        const email = document.getElementById("email");
        const password = document.getElementById("password");

        form.addEventListener("submit", function (e) {
          let valid = true;

          clearState(email);
          clearState(password);

          const emailValue = email.value.trim();
          const passwordValue = password.value.trim();

          if (emailValue === "") {
            setError(email, "Email është i detyrueshëm");
            valid = false;
          } else if (!isValidEmail(emailValue)) {
            setError(email, "Email nuk është valid");
            valid = false;
          } else {
            setSuccess(email);
          }

          if (passwordValue === "") {
            setError(password, "Password është i detyrueshëm");
            valid = false;
          } else if (passwordValue.length < 6) {
            setError(password, "Password duhet të ketë minimum 6 karaktere");
            valid = false;
          } else {
            setSuccess(password);
          }

          if (!valid) {
            e.preventDefault();
          }
        });

        function setError(input, message) {
          const control = input.closest(".input-control");
          const error = control.querySelector(".error");

          error.innerText = message;
          control.classList.add("error");
          control.classList.remove("success");
        }

        function setSuccess(input) {
          const control = input.closest(".input-control");
          const error = control.querySelector(".error");

          if (error) error.innerText = "";
          control.classList.add("success");
          control.classList.remove("error");
        }

        function clearState(input) {
          const control = input.closest(".input-control");
          const error = control.querySelector(".error");

          if (error) error.innerText = "";
          control.classList.remove("error");
          control.classList.remove("success");
        }

        function isValidEmail(email) {
          return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }
      });
    </script>
  </body>
</html>