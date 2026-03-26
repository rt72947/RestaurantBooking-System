<?php
require_once 'database.php';

$db = new Database();
$conn = $db->getConnection();

$errors = [];
$success = "";

$restaurantId = 1; // MANUKA

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
        $errors[] = "Emri është i detyrueshëm.";
    } elseif (strlen($fullName) < 2) {
        $errors[] = "Emri duhet të ketë të paktën 2 karaktere.";
    }

    if ($phone === "") {
        $errors[] = "Telefoni është i detyrueshëm.";
    } elseif (!preg_match('/^[0-9+\-\s]{6,20}$/', $phone)) {
        $errors[] = "Numri i telefonit nuk është valid.";
    }

    if ($email === "") {
        $errors[] = "Email është i detyrueshëm.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email nuk është valid.";
    }

    if ($date === "") {
        $errors[] = "Data është e detyrueshme.";
    } elseif (strtotime($date) < strtotime(date("Y-m-d"))) {
        $errors[] = "Data e rezervimit nuk mund të jetë në të kaluarën.";
    }

    if ($time === "") {
        $errors[] = "Ora është e detyrueshme.";
    }

    if ($guests === "") {
        $errors[] = "Numri i personave është i detyrueshëm.";
    } elseif (!filter_var($guests, FILTER_VALIDATE_INT)) {
        $errors[] = "Numri i personave duhet të jetë numër i plotë.";
    } elseif ((int)$guests < 1 || (int)$guests > 20) {
        $errors[] = "Numri i personave duhet të jetë nga 1 deri në 20.";
    }

    if ($message !== "" && strlen($message) > 500) {
        $errors[] = "Mesazhi nuk duhet të ketë më shumë se 500 karaktere.";
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

            $success = "Rezervimi u ruajt me sukses në databazë!";

            $fullName = "";
            $phone = "";
            $email = "";
            $date = "";
            $time = "";
            $guests = "";
            $message = "";
        } catch (PDOException $e) {
            $errors[] = "Gabim gjatë ruajtjes në databazë: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sq">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manuka Restaurant</title>

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
        <source src="videos/manuka.mov" type="video/mp4" />
      </video>

      <div class="overlay"></div>

      <div class="hero-content">
        <h1>MANUKA<br />RESTAURANT</h1>
        <a class="cta" href="#menu">FIND OUT MORE</a>
      </div>
    </main>

    <section class="menu-section" id="menu">
      <h2>We Offer Top Notch</h2>

      <p class="menu-subtitle">
        Eksperienca jonë kulinare kombinon artin e gatimit me elegancë moderne.
        Çdo pjatë përgatitet me përkushtim, kreativitet dhe përbërës të zgjedhur
        me kujdes.
      </p>

      <div class="menu-slider">
        <button class="slider-btn prev" aria-label="Previous">&#10094;</button>

        <div class="menu-viewport">
          <div class="menu-container slider-track">
            <div class="menu-card">
              <img src="images/petullatTradicionale.jpg" alt="Breakfast" />
              <h3>Breakfast</h3>
              <a href="MenuManuka.php" class="menu-link">View Menu</a>
            </div>

            <div class="menu-card">
              <img src="images/paragjella.jpg" alt="Appetizers" />
              <h3>Appetizers</h3>
              <a href="MenuManuka.php" class="menu-link">View Menu</a>
            </div>

            <div class="menu-card">
              <img src="images/pijet.jpg" alt="Drinks" />
              <h3>Drinks</h3>
              <a href="MenuManuka.php" class="menu-link">View Menu</a>
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
            Rr. Ramadan Rexhepi, Ferizaj <br />
            06:00 - 00:00
          </p>
        </div>

        <div class="contact-card">
          <h3>Phone</h3>
          <p><a href="tel:+38349500327">+383 49 500 327</a></p>
        </div>

        <div class="contact-card">
          <h3>Email</h3>
          <p class="email">
            <a href="mailto:manukarestaurant2023@gmail.com">
              manukarestaurant2023@gmail.com
            </a>
          </p>
        </div>

        <div class="contact-card">
          <h3>Follow Us</h3>
          <p>
            <a href="https://www.facebook.com/manukarestaurant" target="_blank">Facebook</a> |
            <a href="https://www.instagram.com/manukarestaurant/" target="_blank">Instagram</a> |
            <a href="https://www.tiktok.com/@manukarestaurant" target="_blank">TikTok</a>
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
            <label for="fullName">Emri juaj</label>
            <input
              type="text"
              id="fullName"
              name="fullName"
              placeholder="Shkruani emrin"
              value="<?php echo htmlspecialchars($fullName); ?>"
            />

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
              </div>

              <div class="col">
                <label for="time">Ora</label>
                <input
                  type="time"
                  id="time"
                  name="time"
                  value="<?php echo htmlspecialchars($time); ?>"
                />
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

            <label for="message">Mesazh / Shënim</label>
            <textarea
              id="message"
              name="message"
              placeholder="Opsionale"
            ><?php echo htmlspecialchars($message); ?></textarea>

            <button type="submit">Rezervo Tani</button>

            <?php if (!empty($errors)): ?>
              <div id="formMessage" style="color: red; margin-top: 15px;">
                <?php foreach ($errors as $error): ?>
                  <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

            <?php if ($success): ?>
              <div id="formMessage" style="color: green; margin-top: 15px;">
                <p><?php echo htmlspecialchars($success); ?></p>
              </div>
            <?php endif; ?>
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