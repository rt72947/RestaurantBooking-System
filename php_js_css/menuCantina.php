<?php
$menuItems = [
    [
        "img" => "images/breakfastCantina.jpg",
        "alt" => "tost",
        "name" => "Tost tradicional",
        "desc" => "Bukë e thekur, avokado, vezë.",
        "price" => "€2.75"
    ],
    [
        "img" => "images/Sincronizada.jpg",
        "alt" => "Sincronizada",
        "name" => "Sincronizada",
        "desc" => "Tortille, vezë, proshutë, djathë, domatina, ullinj.",
        "price" => "€3.20"
    ],
    [
        "img" => "images/tetelas.jpg",
        "alt" => "tetelas",
        "name" => "Mëngjes Cantina",
        "desc" => "Brumë misri i nixtamalizuar (masa harina), fasule të skuqura...",
        "price" => "€5.39"
    ],
    [
        "img" => "images/mengjesMeksikanCantina.webp",
        "alt" => "Vezë të ziera",
        "name" => "Vezë të ziera",
        "desc" => "Tortilla, domate, vezë dhe avokado.",
        "price" => "€2.75"
    ],
    [
        "img" => "images/tamalesCantina.jpg",
        "alt" => "Mëngjes tradicional Tamales",
        "name" => "Tamale",
        "desc" => "Mish derri, pulë ose fasule dhe djathë.",
        "price" => "€4.29"
    ],
    [
        "img" => "images/CantinaVeze.jpg",
        "alt" => "Vezë të fërguara",
        "name" => "Vezë të fërguara me domate",
        "desc" => "Vezë, domate, origano.",
        "price" => "€4.00"
    ]
];
?>

<!DOCTYPE html>
<html lang="sq">
<head>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&family=Cinzel:wght@400;600&display=swap"
        rel="stylesheet"
    />
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cantina Mëngjesi</title>
    <link rel="stylesheet" href="menu.css" />
</head>

<body>
    <div class="top">
        <a class="back" href="cantina.php">Back</a>
        <div class="brand">Cantina Restaurant</div>
    </div>

    <div class="nav">
        <a href="">Mëngjesi</a>
        <a href="#">Ëmbëlsira</a>
        <a href="#">Specialitete</a>
        <a href="#">Paragjella</a>
        <a href="#">Sallata</a>
        <a href="#">Pije</a>
    </div>

    <div class="head">
        <h1>MËNGJESI</h1>
    </div>

    <div class="menu">
        <?php foreach ($menuItems as $item): ?>
            <div class="item">
                <div class="left">
                    <img
                        src="<?php echo htmlspecialchars($item['img']); ?>"
                        alt="<?php echo htmlspecialchars($item['alt']); ?>"
                    />
                    <div class="name">
                        <?php echo htmlspecialchars($item['name']); ?>
                    </div>
                    <div class="desc">
                        <?php echo htmlspecialchars($item['desc']); ?>
                    </div>
                </div>

                <div class="price">
                    <?php echo htmlspecialchars($item['price']); ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="footer">© Cantina Restaurant • Ferizaj</div>
</body>
</html>