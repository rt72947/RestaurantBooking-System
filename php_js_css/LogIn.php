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
      <a class="back" href="Homepage.html">Home Page</a>
    </header>

    <main class="auth">
      <div class="auth-card">
        <h2>Log In</h2>
        <p class="auth-sub">Kyçu për të vazhduar</p>

        <form action="#" method="post" id="form">
          <div class="input-control">
            <div class="input-box">
              <img src="images/user.png" class="icons" alt="Username" />
              <input
                type="text"
                id="username"
                placeholder="Username"
              />
            </div>
            <div class="error"></div>
          </div>

          <div class="input-control">
            <div class="input-box">
              <img src="images/pass.png" class="icons" alt="Password" />
              <input
                type="password"
                id="password"
                placeholder="Password"
              />
            </div>
            <div class="error"></div>
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
        const username = document.getElementById("username");
        const password = document.getElementById("password");

        form.addEventListener("submit", function (e) {
          e.preventDefault();

          let valid = true;

          const usernameValue = username.value.trim();
          const passwordValue = password.value.trim();

          if (usernameValue === "") {
            setError(username, "Username është i detyrueshëm");
            valid = false;
          } else {
            setSuccess(username);
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

          if (valid) {
            alert("Log in me sukses!");
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

          error.innerText = "";
          control.classList.add("success");
          control.classList.remove("error");
        }

      });
    </script>
  </body>
</html>