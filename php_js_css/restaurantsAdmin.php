<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

require_once 'Database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: LogIn.php");
    exit;
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: Homepage.php");
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$error = "";
$success = "";

/* =========================
   CREATE
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_restaurant'])) {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $image = trim($_POST['image'] ?? '');

    if ($name === "" || $description === "" || $image === "") {
        $error = "Plotëso të gjitha fushat!";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO restaurants (name, description, image)
            VALUES (:name, :description, :image)
        ");

        if ($stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':image' => $image
        ])) {
            $success = "Restoranti u shtua me sukses!";
        } else {
            $error = "Gabim gjatë shtimit të restorantit!";
        }
    }
}

/* =========================
   DELETE
========================= */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM restaurants WHERE id = :id");
    if ($stmt->execute([':id' => $id])) {
        header("Location: RestaurantsAdmin.php?deleted=1");
        exit;
    } else {
        $error = "Gabim gjatë fshirjes!";
    }
}

if (isset($_GET['deleted'])) {
    $success = "Restoranti u fshi me sukses!";
}

/* =========================
   GET EDIT DATA
========================= */
$editRestaurant = null;

if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];

    $stmt = $conn->prepare("SELECT * FROM restaurants WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $editRestaurant = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$editRestaurant) {
        $error = "Restoranti nuk u gjet!";
    }
}

/* =========================
   UPDATE
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_restaurant'])) {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $image = trim($_POST['image'] ?? '');

    if ($id <= 0 || $name === "" || $description === "" || $image === "") {
        $error = "Plotëso të gjitha fushat për përditësim!";
    } else {
        $stmt = $conn->prepare("
            UPDATE restaurants
            SET name = :name,
                description = :description,
                image = :image
            WHERE id = :id
        ");

        if ($stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':image' => $image,
            ':id' => $id
        ])) {
            header("Location: RestaurantsAdmin.php?updated=1");
            exit;
        } else {
            $error = "Gabim gjatë përditësimit!";
        }
    }
}

if (isset($_GET['updated'])) {
    $success = "Restoranti u përditësua me sukses!";
}

/* =========================
   READ
========================= */
$stmt = $conn->prepare("SELECT * FROM restaurants ORDER BY id DESC");
$stmt->execute();
$restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurants Admin</title>
    <style>
        body {
            font-family: Poppins, Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .topbar a {
            text-decoration: none;
            background: #111;
            color: white;
            padding: 10px 16px;
            border-radius: 8px;
        }

        .card {
            background: white;
            border-radius: 14px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.08);
        }

        h1, h2 {
            margin-top: 0;
        }

        .message-success {
            background: #e8f7e8;
            color: #166534;
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .message-error {
            background: #fdecec;
            color: #b91c1c;
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        form {
            display: grid;
            gap: 12px;
        }

        input, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #dcdcdc;
            border-radius: 10px;
            font-size: 14px;
            box-sizing: border-box;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        button {
            background: #111;
            color: white;
            border: none;
            padding: 12px 18px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            border-bottom: 1px solid #e5e5e5;
            padding: 12px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #fafafa;
        }

        .actions a {
            display: inline-block;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 8px;
            margin-right: 8px;
            color: white;
            font-size: 13px;
        }

        .edit-btn {
            background: #2563eb;
        }

        .delete-btn {
            background: #dc2626;
        }

        .img-preview {
            width: 90px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        @media (max-width: 900px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }

            th {
                display: none;
            }

            td {
                border-bottom: 1px solid #eee;
            }

            td::before {
                content: attr(data-label);
                font-weight: 700;
                display: block;
                margin-bottom: 6px;
            }
        }
    </style>
</head>
<body>
<div class="container">

    <div class="topbar">
        <h1>CRUD për Restorante</h1>
        <div style="display:flex; gap:10px;">
            <a href="Dashboard.php">Dashboard</a>
            <a href="Homepage.php">Homepage</a>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="message-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="message-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($editRestaurant): ?>
        <div class="card">
            <h2>Edit Restaurant</h2>
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?php echo (int)($editRestaurant['id'] ?? 0); ?>">

                <input
                    type="text"
                    name="name"
                    placeholder="Emri i restorantit"
                    value="<?php echo htmlspecialchars($editRestaurant['name'] ?? '-'); ?>"
                    required
                >

                <textarea
                    name="description"
                    placeholder="Përshkrimi"
                    required
                ><?php echo htmlspecialchars($editRestaurant['description'] ?? '-'); ?></textarea>

                <input
                    type="text"
                    name="image"
                    placeholder="Emri i fotos p.sh. manuka.jpg"
                    value="<?php echo htmlspecialchars($editRestaurant['image'] ?? '-'); ?>"
                    required
                >

                <div style="display:flex; gap:10px;">
                    <button type="submit" name="update_restaurant">Ruaj Ndryshimet</button>
                    <a href="RestaurantsAdmin.php" style="background:#666;color:#fff;padding:12px 18px;border-radius:10px;text-decoration:none;">Anulo</a>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div class="card">
            <h2>Shto Restaurant të Ri</h2>
            <form method="POST" action="">
                <input
                    type="text"
                    name="name"
                    placeholder="Emri i restorantit"
                    required
                >

                <textarea
                    name="description"
                    placeholder="Përshkrimi i restorantit"
                    required
                ></textarea>

                <input
                    type="text"
                    name="image"
                    placeholder="Emri i fotos p.sh. manuka.jpg"
                    required
                >

                <button type="submit" name="create_restaurant">Shto Restaurant</button>
            </form>
        </div>
    <?php endif; ?>

    <div class="card">
        <h2>Lista e Restoranteve</h2>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Foto</th>
                    <th>Emri</th>
                    <th>Përshkrimi</th>
                    <th>Krijuar</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($restaurants as $r): ?>
                    <tr>
                        <td data-label="ID"><?php echo (int)($r['id'] ?? 0); ?></td>
                        <td data-label="Foto">
                            <img
                                src="images/<?php echo htmlspecialchars($r['image'] ?? 'default.jpg'); ?>"
                                alt="<?php echo htmlspecialchars($r['name'] ?? 'Restaurant'); ?>"
                                class="img-preview"
                            >
                        </td>
                        <td data-label="Emri"><?php echo htmlspecialchars($r['name'] ?? '-'); ?></td>
                        <td data-label="Përshkrimi"><?php echo htmlspecialchars($r['description'] ?? '-'); ?></td>
                        <td data-label="Krijuar"><?php echo htmlspecialchars($r['created_at'] ?? '-'); ?></td>
                        <td data-label="Actions" class="actions">
                            <a class="edit-btn" href="RestaurantsAdmin.php?edit=<?php echo (int)($r['id'] ?? 0); ?>">Edit</a>
                            <a class="delete-btn" href="RestaurantsAdmin.php?delete=<?php echo (int)($r['id'] ?? 0); ?>" onclick="return confirm('A je i sigurt që don me fshi këtë restaurant?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>