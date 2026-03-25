<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['user_id'])) {
    header("Location: LogIn.php");
    exit();
}

include 'Database.php';
$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("SELECT * FROM restaurants ORDER BY id ASC");
$stmt->execute();
$restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);

$isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
$userName = $_SESSION['user_name'] ?? 'User';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>DineSpot</title>
    <link rel="stylesheet" href="Homepage.css?v=999" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body id="home">
    <header class="header">
        <nav class="navigation">
            <div class="nav-left">
                <a href="#home"><b>Home</b></a>
                <a href="#about"><b>About Us</b></a>
                <a href="ContactUs.php"><b>Contact Us</b></a>

                <?php if ($isAdmin): ?>
                    <a href="dashboard.php"><b>Dashboard</b></a>
                <?php endif; ?>
            </div>

            <div style="display:flex; align-items:center; gap:12px;">
                <span style="color:white; font-weight:500;">
                    Welcome, <?php echo htmlspecialchars($userName); ?>
                </span>
                <a href="LogOut.php"><button class="button">Log Out</button></a>
            </div>
        </nav>
    </header>

    <main>
        <div class="main">
            <div class="main_background">
                <img src="images/homepageimg.jpg" alt="background photo" />
                <div class="main_text">
                    <h3>Dine Spot</h3>
                    <p class="tagline"><i>Zbuloni shijen, ndjeni atmosferën, krijoni momentet e veçanta.</i></p>
                </div>
            </div>

            <div class="restaurant_listing">
                <?php foreach ($restaurants as $r): ?>
                <div class="restuarant">
                    <a href="<?php echo htmlspecialchars($r['page']); ?>">
                        <img src="images/<?php echo htmlspecialchars($r['image']); ?>" alt="<?php echo htmlspecialchars($r['name']); ?>" />
                    </a>

                    <h3>
                        <a href="<?php echo htmlspecialchars($r['page']); ?>">
                            <?php echo htmlspecialchars($r['name']); ?>
                        </a>
                    </h3>

                    <p><i><?php echo htmlspecialchars($r['description']); ?></i></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-left">
            <h2>About us</h2>
            <div class="about-row" id="about">
                <p>
                    Dine Spot sjell restorantet më të mira të Ferizajt në një vend.
                    Zbuloni shije të shkëlqyera dhe momente të veçanta çdo ditë.
                </p>
                <a href="AboutUs.php"><button class="about">Learn more</button></a>
            </div>
        </div>

        <div class="footer-right">
            <h2>Get in Touch</h2>
            <div class="contact-row" id="contact">
                <p>
                    Keni pyetje, dëshironi të rezervoni një tavolinë apo të organizoni një event?
                    Na shkruani dhe do ju kontaktojmë sa më shpejt.
                </p>
                <a href="ContactUs.php"><button class="contact">Contact Us</button></a>
            </div>
        </div>
    </footer>
</body>
</html>