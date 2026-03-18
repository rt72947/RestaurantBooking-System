<?php
include_once 'Database.php';

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("SELECT * FROM restaurants LIMIT 6");
$stmt->execute();
$restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="AboutUs.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css">
</head>
<body>
    <header class="topbar">
      <a class="back" href="Homepage.php">Home Page</a>
    </header>

    <main>
        <div class="main">
            <div class="main_background">
                <img src="images/homepageimg.jpg" alt="background photo" />
                <div class="main_text">
                    <h3><i>Dine Spot</i></h3>
                </div>
            </div>

            <div class="main-text">
                <h3>About Us</h3>
                <p>
                    Mirësevini në faqen tonë, një udhërrëfyes i përqendruar tek restorantet më të njohura dhe të vizituara të Ferizajt. 
                    Këtu mund të eksploroni menutë, shërbimet dhe eksperiencat kulinare që çdo vend ka për të ofruar, duke ju ndihmuar të gjeni 
                    gjithmonë diçka të re për të shijuar. Ne synojmë të sjellim një pasqyrë të plotë të atmosferës, shijeve dhe stilit unik të çdo restoranti, 
                    nga kuzhina tradicionale shqiptare te konceptet më moderne dhe ndërkombëtare.
                    <br><br>
                    Qëllimi ynë është të ndihmojmë banorët dhe vizitorët të zbulojnë vendet më të veçanta dhe shijet më të mira të qytetit. 
                    Në të njëjtën kohë, mbështesim bizneset lokale duke krijuar një platformë të thjeshtë, të besueshme dhe të këndshme, 
                    ku çdo dashamirës i ushqimit mund të gjejë rekomandime të dobishme, ide për vizita dhe përvoja kulinare të paharrueshme. 
                    Ne duam që çdo përdorues të ndihet i udhëzuar dhe i frymëzuar për të eksploruar gastronominë e Ferizajt dhe për të shijuar momentet e vogla të kënaqësisë që sjell një vakt i mirë.
                </p> 
            </div>

            <div class="ferizaj">
                <img src="images/ferizaj.jpg" alt="ferizaj photo" />
                <p>
                    Ferizaj është një qytet dinamik dhe në zhvillim të shpejtë në Kosovë, i njohur për pozicionin strategjik, kulturën e pasur dhe mikpritjen e veçantë të banorëve. 
                    Ai kombinon elemente moderne me trashëgimi historike, me arkitekturë bashkëkohore, hapësira publike në zhvillim dhe lagje tradicionale që ruajnë identitetin lokal. 
                    Komuniteti i rinjve dhe energjia e qytetit e bëjnë Ferizajin një vend aktiv dhe të gjallë në aspektin social, kulturor dhe ekonomik.
                    <br><br>
                    Në qytet nuk mungojnë restorantet, bistro dhe lokalet që bashkojnë shijet tradicionale shqiptare me kuzhinën ndërkombëtare, 
                    duke krijuar një ambient të këndshëm për vizitorët dhe banorët. Ritmi i gjallë i jetës urbane, aktivitetet kulturore dhe ngjarjet artistike e pozicionojnë Ferizajin si një qytet me potencial të madh për zhvillim, inovacion dhe përvojë të pasur sociale dhe kulturore.
                </p>
            </div>    

            <div class="card-slider swiper">
                <div class="swiper-wrapper">
                    <?php foreach($restaurants as $r): ?>
                        <div class="card-item swiper-slide">
                            <img src="images/<?= $r['image'] ?>" class="card-image" alt="<?= $r['name'] ?>">
                            <div class="card-details">
                                <h3 class="card-title"><?= $r['name'] ?></h3>
                                <p><i><?= $r['description'] ?></i></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
                <div class="swiper-pagination"></div>
            </div>

        </div>
    </main>

    <footer>
        <div class="footer">
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

    <script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
    <script>
        const swiper = new Swiper(".card-slider", {
            loop: true,
            spaceBetween: 30,
            grabCursor: true,
            pagination: { el: ".swiper-pagination", clickable: true },
            navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
            breakpoints: { 0:{ slidesPerView:1 }, 768:{ slidesPerView:2 }, 1080:{ slidesPerView:3 } }
        });
    </script>
</body>
</html>