<?php
$menuItems = [
    [
        "img" => "images/PetullaMengjesi.avif",
        "alt" => "Petullat",
        "name" => "Petulla Mëngjesi",
        "desc" => "Petulla të freskëta.",
        "price" => "€2.75"
    ],
    [
        "img" => "images/Omlet.avif",
        "alt" => "Omlet",
        "name" => "Omlet",
        "desc" => "Vezë, proshutë, djathë, domatina, ullinj.",
        "price" => "€3.20"
    ],
    [
        "img" => "images/SalmonITymosur.avif",
        "alt" => "Salmon i tymosur",
        "name" => "Mëngjes Manuka",
        "desc" => "Salmon i tymosur, spinaq, djathë, vezë, sos…",
        "price" => "€5.39"
    ],
    [
        "img" => "images/SandwichManuka.avif",
        "alt" => "Sandwich Manuka",
        "name" => "Sandwich Manuka",
        "desc" => "Rukolla, domate, mozzarella.",
        "price" => "€2.75"
    ],
    [
        "img" => "images/SandwichNePete.avif",
        "alt" => "Sandwich në Petë",
        "name" => "Sandwich në Petë me Mish Pule",
        "desc" => "Mish pule, perime, patate.",
        "price" => "€4.29"
    ],
    [
        "img" => "images/SandwichNePete.avif",
        "alt" => "Sandwich në Petë",
        "name" => "Sandwich në Petë Mish Viçi",
        "desc" => "Mish viçi, perime, patate.",
        "price" => "€4.29"
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
    <title>Manuka Mëngjesi</title>
    <link rel="stylesheet" href="menu.css" />
</head>

<body>
    <div class="top">
        <a class="back" href="manuka.php">Back</a>
        <div class="brand">Manuka Restaurant</div>
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

    <div class="footer">© Manuka Restaurant • Ferizaj</div>
</body>
</html>