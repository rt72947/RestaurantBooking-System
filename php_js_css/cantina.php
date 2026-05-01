<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: LogIn.php");
    exit();
}
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

require_once 'database.php';

$db = new Database();
$conn = $db->getConnection();

$errors = [];
$success = "";

$restaurantId = 2;

$fullName = "";
$phone = "";
$email = "";
$date = "";
$time = "";
$guests = "";
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullName = trim($_POST["fullName"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $date = trim($_POST["date"] ?? "");
    $time = trim($_POST["time"] ?? "");
    $guests = trim($_POST["guests"] ?? "");
    $message = trim($_POST["message"] ?? "");

    if ($fullName === "") {
        $errors["fullName"] = "Emri është i detyrueshëm.";
    } elseif (!preg_match("/^[a-zA-ZÀ-ž\s]{2,60}$/u", $fullName)) {
        $errors["fullName"] = "Emri duhet të përmbajë vetëm shkronja.";
    }

    if ($phone === "") {
        $errors["phone"] = "Telefoni është i detyrueshëm.";
    } elseif (!preg_match("/^\+?[0-9\s\-]{8,20}$/", $phone)) {
        $errors["phone"] = "Numri i telefonit nuk është valid.";
    }

    if ($email === "") {
        $errors["email"] = "Email është i detyrueshëm.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Email nuk është valid.";
    }

    if ($date === "") {
        $errors["date"] = "Data është e detyrueshme.";
    } elseif (strtotime($date) < strtotime(date("Y-m-d"))) {
        $errors["date"] = "Data nuk mund të jetë në të kaluarën.";
    }

    if ($time === "") {
        $errors["time"] = "Ora është e detyrueshme.";
    }

    if ($guests === "") {
        $errors["guests"] = "Numri i personave është i detyrueshëm.";
    } elseif (!filter_var($guests, FILTER_VALIDATE_INT)) {
        $errors["guests"] = "Numri i personave duhet të jetë numër i plotë.";
    } elseif ((int)$guests < 1 || (int)$guests > 20) {
        $errors["guests"] = "Numri i personave duhet të jetë nga 1 deri në 20.";
    }

    if ($message !== "" && strlen($message) > 500) {
        $errors["message"] = "Mesazhi nuk duhet të ketë më shumë se 500 karaktere.";
    }

    if (empty($errors)) {
        try {
            $sql = "INSERT INTO reservations 
                    (restaurant_id, full_name, phone, email, date, time, guests, message)
                    VALUES 
                    (:restaurant_id, :full_name, :phone, :email, :date, :time, :guests, :message)";

            $stmt = $conn->prepare($sql);

            $stmt->execute([
                ':restaurant_id' => $restaurantId,
                ':full_name' => $fullName,
                ':phone' => $phone,
                ':email' => $email,
                ':date' => $date,
                ':time' => $time,
                ':guests' => $guests,
                ':message' => $message
            ]);

            $success = "Rezervimi u ruajt me sukses, ju mirëpresim!";

            $fullName = "";
            $phone = "";
            $email = "";
            $date = "";
            $time = "";
            $guests = "";
            $message = "";
        } catch (PDOException $e) {
            $errors["general"] = "Ndodhi një gabim gjatë ruajtjes së rezervimit. Ju lutem provoni përsëri.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sq">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cantina De Juan</title>

    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&family=Cinzel:wght@400;600&display=swap"
      rel="stylesheet"
    />

    <link rel="stylesheet" href="restaurant.css" />
  </head>

  <body>
    <header class="topbar">
      <a class="back" href="Homepage.php">Home Page</a>
    </header>

    <main class="hero">
      <video autoplay muted loop playsinline class="hero-video">
        <source src="videos/cantinavideo.mp4" type="video/mp4" />
      </video>

      <div class="overlay"></div>

      <div class="hero-content">
        <h1>CANTINA DE JUAN<br />RESTAURANT</h1>
        <a class="cta" href="#menu">FIND OUT MORE</a>
      </div>
    </main>

    <section class="menu-section" id="menu">
      <h2>We Offer Top Notch</h2>

      <p class="menu-subtitle">
        Restoranti ynë meksikan ofron shije autentike me ngjyra, erëza dhe
        receta tradicionale. Tacos, burritos dhe specialitetet pikante
        përgatiten me përbërës të freskët dhe shërbehen në një ambient të
        ngrohtë me atmosferë latine.
      </p>

      <div class="menu-slider">
        <button class="slider-btn prev" aria-label="Previous">&#10094;</button>

        <div class="menu-viewport">
          <div class="menu-container slider-track">
            <div class="menu-card">
              <img src="images/breakfastCantina.jpg" alt="Breakfast" />
              <h3>Breakfast</h3>
              <a href="menuCantina.php" class="menu-link">View Menu</a>
            </div>

            <div class="menu-card">
              <img src="images/cantinaDREKE.jpg" alt="Main Course" />
              <h3>Main Course</h3>
              <a href="menuCantina.php" class="menu-link">View Menu</a>
            </div>

            <div class="menu-card">
              <img src="images/DrinksCantina.jpg" alt="Drinks" />
              <h3>Drinks</h3>
              <a href="menuCantina.php" class="menu-link">View Menu</a>
            </div>
          </div>
        </div>

        <button class="slider-btn next" aria-label="Next">&#10095;</button>
      </div>
    </section>

    <section class="contact-section">
      <h2>Contact Us</h2>
      <p>
        Na kontaktoni për rezervime, pyetje ose sugjerime. Ne jemi gjithmonë të
        gatshëm t'ju ndihmojmë.
      </p>

      <div class="contact-container">
        <div class="contact-card">
          <h3>Address</h3>
          <p>
            Skenderbeu, Ferizaj 70000 <br />
            08:00 - 24:00
          </p>
        </div>

        <div class="contact-card">
          <h3>Phone</h3>
          <p><a href="tel:+38348666640">+383 48 666 640</a></p>
        </div>

        <div class="contact-card">
          <h3>Email</h3>
          <p class="email">
            <a href="mailto:cantina123@gmail.com">
              cantina123@gmail.com
            </a>
          </p>
        </div>

        <div class="contact-card">
          <h3>Follow Us</h3>
          <p>
            <a href="#" target="_blank">Facebook</a> |
            <a href="https://www.instagram.com/cantinadejuan/" target="_blank">Instagram</a> |
            <a href="https://www.tiktok.com/@cantina.de.juan" target="_blank">TikTok</a>
          </p>
        </div>
      </div>

      <div class="reservation-wrap" id="reservation">
        <div class="reservation-form">
          <div class="res-head">
            <h3>Rezervo Tavolinën</h3>
            <p>Plotëso të dhënat dhe ne të konfirmojmë sa më shpejt.</p>
            <div class="res-divider"></div>
          </div>

          <form action="#reservation" method="POST" class="res-grid" id="reservationForm" novalidate>
            <?php if (!empty($errors["general"])): ?>
              <div id="formMessage" style="color: red; margin-top: 15px;">
                <p><?php echo htmlspecialchars($errors["general"]); ?></p>
              </div>
            <?php endif; ?>

            <?php if ($success): ?>
              <div id="formMessage" style="color: green; margin-top: 15px;">
                <p><?php echo htmlspecialchars($success); ?></p>
              </div>
            <?php endif; ?>

            <label for="fullName">Emri juaj</label>
            <input
              type="text"
              id="fullName"
              name="fullName"
              placeholder="Shkruani emrin"
              value="<?php echo htmlspecialchars($fullName); ?>"
            />
            <?php if (!empty($errors["fullName"])): ?>
              <div style="color: red; font-size: 13px;"><?php echo htmlspecialchars($errors["fullName"]); ?></div>
            <?php endif; ?>

            <div class="res-row">
              <div class="col">
                <label for="phone">Telefoni</label>
                <input
                  type="text"
                  id="phone"
                  name="phone"
                  placeholder="Shkruani numrin"
                  value="<?php echo htmlspecialchars($phone); ?>"
                />
                <?php if (!empty($errors["phone"])): ?>
                  <div style="color: red; font-size: 13px;"><?php echo htmlspecialchars($errors["phone"]); ?></div>
                <?php endif; ?>
              </div>

              <div class="col">
                <label for="email">Email</label>
                <input
                  type="email"
                  id="email"
                  name="email"
                  placeholder="Shkruani email"
                  value="<?php echo htmlspecialchars($email); ?>"
                />
                <?php if (!empty($errors["email"])): ?>
                  <div style="color: red; font-size: 13px;"><?php echo htmlspecialchars($errors["email"]); ?></div>
                <?php endif; ?>
              </div>
            </div>

            <div class="res-row">
              <div class="col">
                <label for="date">Data</label>
                <input
                  type="date"
                  id="date"
                  name="date"
                  value="<?php echo htmlspecialchars($date); ?>"
                />
                <?php if (!empty($errors["date"])): ?>
                  <div style="color: red; font-size: 13px;"><?php echo htmlspecialchars($errors["date"]); ?></div>
                <?php endif; ?>
              </div>

              <div class="col">
                <label for="time">Ora</label>
                <input
                  type="time"
                  id="time"
                  name="time"
                  value="<?php echo htmlspecialchars($time); ?>"
                />
                <?php if (!empty($errors["time"])): ?>
                  <div style="color: red; font-size: 13px;"><?php echo htmlspecialchars($errors["time"]); ?></div>
                <?php endif; ?>
              </div>
            </div>

            <label for="guests">Numri i personave</label>
            <input
              type="number"
              id="guests"
              name="guests"
              min="1"
              max="20"
              placeholder="p.sh. 4"
              value="<?php echo htmlspecialchars($guests); ?>"
            />
            <?php if (!empty($errors["guests"])): ?>
              <div style="color: red; font-size: 13px;"><?php echo htmlspecialchars($errors["guests"]); ?></div>
            <?php endif; ?>

            <label for="message">Mesazh / Shënim</label>
            <textarea
              id="message"
              name="message"
              placeholder="Opsionale"
            ><?php echo htmlspecialchars($message); ?></textarea>
            <?php if (!empty($errors["message"])): ?>
              <div style="color: red; font-size: 13px;"><?php echo htmlspecialchars($errors["message"]); ?></div>
            <?php endif; ?>

            <button type="submit">Rezervo Tani</button>
          </form>
        </div>
      </div>
    </section>

    <script>
      document.addEventListener("DOMContentLoaded", () => {
        const ctaBtn = document.querySelector(".cta");
        const menuSection = document.querySelector("#menu");
        const track = document.querySelector(".slider-track");
        const prevBtn = document.querySelector(".prev");
        const nextBtn = document.querySelector(".next");
        const slider = document.querySelector(".menu-slider");

        if (ctaBtn && menuSection) {
          ctaBtn.addEventListener("click", (e) => {
            e.preventDefault();
            menuSection.scrollIntoView({ behavior: "smooth" });
          });
        }

        function getMoveAmount() {
          if (!track) return 0;
          const firstCard = track.querySelector(".menu-card");
          if (!firstCard) return 0;
          const gap = parseFloat(getComputedStyle(track).gap) || 0;
          return firstCard.offsetWidth + gap;
        }

        function nextSlide() {
          if (!track || !track.firstElementChild) return;
          const firstCard = track.firstElementChild;
          const moveAmount = getMoveAmount();

          track.style.transition = "transform 0.45s ease";
          track.style.transform = `translateX(-${moveAmount}px)`;

          track.addEventListener(
            "transitionend",
            () => {
              track.style.transition = "none";
              track.appendChild(firstCard);
              track.style.transform = "translateX(0)";
            },
            { once: true }
          );
        }

        function prevSlide() {
          if (!track || !track.lastElementChild) return;
          const lastCard = track.lastElementChild;
          const moveAmount = getMoveAmount();

          track.style.transition = "none";
          track.insertBefore(lastCard, track.firstElementChild);
          track.style.transform = `translateX(-${moveAmount}px)`;

          requestAnimationFrame(() => {
            requestAnimationFrame(() => {
              track.style.transition = "transform 0.45s ease";
              track.style.transform = "translateX(0)";
            });
          });
        }

        if (nextBtn) nextBtn.addEventListener("click", nextSlide);
        if (prevBtn) prevBtn.addEventListener("click", prevSlide);

        let autoSlide;
        if (track) {
          autoSlide = setInterval(nextSlide, 3000);
        }

        if (slider) {
          slider.addEventListener("mouseenter", () => {
            clearInterval(autoSlide);
          });

          slider.addEventListener("mouseleave", () => {
            autoSlide = setInterval(nextSlide, 3000);
          });
        }

        document.addEventListener("keydown", (e) => {
          if (e.key === "ArrowRight") nextSlide();
          if (e.key === "ArrowLeft") prevSlide();
        });
      });
    </script>
  </body>
</html>