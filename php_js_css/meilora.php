<!DOCTYPE html>
<html lang="sq">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Ad Meliora</title>

    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&family=Cinzel:wght@400;600&display=swap"
      rel="stylesheet"
    />

    <link rel="stylesheet" href="restaurant.css" />
  </head>

  <body>
    <header class="topbar">
      <a class="back" href="Homepage.html">Home Page</a>
    </header>

    <main class="hero">
      <video autoplay muted loop playsinline class="hero-video">
        <source
          src="videos/Fryma e festave ka mbërritur në Ad Meliora. Mirë se vini në atmosferën tonë të ngrohtë dhe elegante!.mp4"
          type="video/mp4"
        />
      </video>

      <div class="overlay"></div>

      <div class="hero-content">
        <h1>AD MELIORA<br />RESTAURANT</h1>
        <a class="cta" href="#menu">FIND OUT MORE</a>
      </div>
    </main>

    <section class="menu-section" id="menu">
      <h2>We Offer Top Notch</h2>

      <p class="menu-subtitle">
        Ambient elegant, kuzhinë moderne dhe një eksperiencë e veçantë
        gastronomike për çdo vizitor.
      </p>

      <div class="menu-slider">
        <button class="slider-btn prev" aria-label="Previous">&#10094;</button>

        <div class="menu-viewport">
          <div class="menu-container slider-track">
            <div class="menu-card">
              <img src="images/breakfastMeilora.jpg" alt="Breakfast" />
              <h3>Breakfast</h3>
              <a href="#" class="menu-link">View Menu</a>
            </div>

            <div class="menu-card">
              <img src="images/lunchMeilora.jpg" alt="Main Course" />
              <h3>Main Course</h3>
              <a href="#" class="menu-link">View Menu</a>
            </div>

            <div class="menu-card">
              <img src="images/MEILORAdrinks.jpg" alt="Drinks" />
              <h3>Drinks</h3>
              <a href="#" class="menu-link">View Menu</a>
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
            Jezerc, Ferizaj 70000 <br />
            08:00 - 24:00
          </p>
        </div>

        <div class="contact-card">
          <h3>Phone</h3>
          <p>-</p>
        </div>

        <div class="contact-card">
          <h3>Email</h3>
          <p>-</p>
        </div>

        <div class="contact-card">
          <h3>Follow Us</h3>
          <p>
            <a href="#" target="_blank">Facebook</a> |
            <a href="#" target="_blank">Instagram</a> |
            <a href="#" target="_blank">TikTok</a>
          </p>
        </div>
      </div>

      <div class="reservation-wrap">
        <div class="reservation-form">
          <div class="res-head">
            <h3>Rezervo Tavolinën</h3>
            <p>Plotëso të dhënat dhe ne të konfirmojmë sa më shpejt.</p>
            <div class="res-divider"></div>
          </div>

          <form action="#" method="POST" class="res-grid" id="reservationForm">
            <label for="fullName">Emri juaj</label>
            <input
              type="text"
              id="fullName"
              name="fullName"
              placeholder="Shkruani emrin"
              required
            />

            <div class="res-row">
              <div class="col">
                <label for="phone">Telefoni</label>
                <input
                  type="text"
                  id="phone"
                  name="phone"
                  placeholder="Shkruani numrin"
                  required
                />
              </div>

              <div class="col">
                <label for="email">Email</label>
                <input
                  type="email"
                  id="email"
                  name="email"
                  placeholder="Shkruani email"
                  required
                />
              </div>
            </div>

            <div class="res-row">
              <div class="col">
                <label for="date">Data</label>
                <input type="date" id="date" name="date" required />
              </div>

              <div class="col">
                <label for="time">Ora</label>
                <input type="time" id="time" name="time" required />
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
              required
            />

            <label for="message">Mesazh / Shënim</label>
            <textarea
              id="message"
              name="message"
              placeholder="Opsionale"
            ></textarea>

            <button type="submit">Rezervo Tani</button>
            <p id="formMessage"></p>
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
        const form = document.querySelector("#reservationForm");
        const formMessage = document.querySelector("#formMessage");

        if (ctaBtn && menuSection) {
          ctaBtn.addEventListener("click", (e) => {
            e.preventDefault();
            menuSection.scrollIntoView({ behavior: "smooth" });
          });
        }

        function getMoveAmount() {
          const firstCard = track.querySelector(".menu-card");
          const gap = parseFloat(getComputedStyle(track).gap) || 0;
          return firstCard.offsetWidth + gap;
        }

        function nextSlide() {
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

        let autoSlide = setInterval(nextSlide, 3000);

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

        if (form) {
          form.addEventListener("submit", (e) => {
            e.preventDefault();

            const fullName = document.querySelector("#fullName").value.trim();
            const phone = document.querySelector("#phone").value.trim();
            const email = document.querySelector("#email").value.trim();
            const date = document.querySelector("#date").value;
            const time = document.querySelector("#time").value;
            const guests = document.querySelector("#guests").value;

            if (!fullName || !phone || !email || !date || !time || !guests) {
              formMessage.textContent = "Ju lutem plotësoni të gjitha fushat obligative.";
              formMessage.style.color = "red";
              return;
            }

            const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,}$/i;
            if (!emailPattern.test(email)) {
              formMessage.textContent = "Ju lutem shkruani një email valid.";
              formMessage.style.color = "red";
              return;
            }

            formMessage.textContent = "Rezervimi u dërgua me sukses!";
            formMessage.style.color = "green";

            form.reset();
          });
        }
      });
    </script>
  </body>
</html>