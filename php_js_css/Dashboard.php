<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: LogIn.php");
    exit();
}

if ($_SESSION["user_role"] !== "admin") {
    header("Location: Homepage.php");
    exit();
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

require_once 'Database.php';
include_once 'users.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: LogIn.php');
    exit;
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: Homepage.php');
    exit;
}

$db = new Database();
$conn = $db->getConnection();
$userModel = new User($conn);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action == 'create') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirmPassword'] ?? '';

        if (empty($name) || empty($email) || empty($password) || empty($confirm)) {
            $error = "Plotëso të gjitha fushat!";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Email nuk është valid!";
        } elseif ($password !== $confirm) {
            $error = "Password-at nuk përputhen!";
        } else {
            if ($userModel->register($name, $email, $password)) {
                $success = "User u krijua me sukses!";
            } else {
                $error = "Ky email ekziston tashmë!";
            }
        }
    }

    if ($action == 'update') {
        $id = $_POST['id'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = !empty($_POST['password']) ? $_POST['password'] : null;
        $role = $_POST['role'] ?? 'user';

        if ($userModel->updateUser($id, $name, $email, $password, $role)) {
            $success = "User u përditësua me sukses!";
        } else {
            $error = "Gabim gjatë përditësimit!";
        }
    }

    if (isset($_POST['create_restaurant'])) {
        $restaurantName = trim($_POST['restaurant_name'] ?? '');
        $restaurantDescription = trim($_POST['restaurant_description'] ?? '');
        $restaurantImage = trim($_POST['restaurant_image'] ?? '');

        if ($restaurantName === "" || $restaurantDescription === "" || $restaurantImage === "") {
            $error = "Plotëso të gjitha fushat për restaurant!";
        } else {
            $stmt = $conn->prepare("
                INSERT INTO restaurants (name, description, image)
                VALUES (:name, :description, :image)
            ");

            if ($stmt->execute([
                ':name' => $restaurantName,
                ':description' => $restaurantDescription,
                ':image' => $restaurantImage
            ])) {
                header("Location: Dashboard.php?restaurant_created=1");
                exit;
            } else {
                $error = "Gabim gjatë shtimit të restaurant!";
            }
        }
    }

    if (isset($_POST['update_restaurant'])) {
        $restaurantId = (int)($_POST['restaurant_id'] ?? 0);
        $restaurantName = trim($_POST['restaurant_name'] ?? '');
        $restaurantDescription = trim($_POST['restaurant_description'] ?? '');
        $restaurantImage = trim($_POST['restaurant_image'] ?? '');

        if ($restaurantId <= 0 || $restaurantName === "" || $restaurantDescription === "" || $restaurantImage === "") {
            $error = "Plotëso të gjitha fushat për përditësim të restaurant!";
        } else {
            $stmt = $conn->prepare("
                UPDATE restaurants
                SET name = :name,
                    description = :description,
                    image = :image
                WHERE id = :id
            ");

            if ($stmt->execute([
                ':name' => $restaurantName,
                ':description' => $restaurantDescription,
                ':image' => $restaurantImage,
                ':id' => $restaurantId
            ])) {
                header("Location: Dashboard.php?restaurant_updated=1");
                exit;
            } else {
                $error = "Gabim gjatë përditësimit të restaurant!";
            }
        }
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($userModel->deleteUser($id)) {
        $success = "User u fshi me sukses!";
    } else {
        $error = "Gabim gjatë fshirjes!";
    }
}

if (isset($_GET['delete_msg'])) {
    $id = (int)$_GET['delete_msg'];
    $stmt = $conn->prepare("DELETE FROM contacts WHERE id = :id");
    if ($stmt->execute([':id' => $id])) {
        header("Location: Dashboard.php");
        exit;
    } else {
        $error = "Gabim gjatë fshirjes së mesazhit!";
    }
}

if (isset($_GET['delete_restaurant'])) {
    $id = (int)$_GET['delete_restaurant'];
    $stmt = $conn->prepare("DELETE FROM restaurants WHERE id = :id");

    if ($stmt->execute([':id' => $id])) {
        header("Location: Dashboard.php?restaurant_deleted=1");
        exit;
    } else {
        $error = "Gabim gjatë fshirjes së restaurant!";
    }
}

$editRestaurant = null;

if (isset($_GET['edit_restaurant'])) {
    $id = (int)$_GET['edit_restaurant'];
    $stmt = $conn->prepare("SELECT * FROM restaurants WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $editRestaurant = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$editRestaurant) {
        $error = "Restaurant nuk u gjet!";
    }
}

if (isset($_GET['restaurant_created'])) {
    $success = "Restaurant u shtua me sukses!";
}

if (isset($_GET['restaurant_updated'])) {
    $success = "Restaurant u përditësua me sukses!";
}

if (isset($_GET['restaurant_deleted'])) {
    $success = "Restaurant u fshi me sukses!";
}

$users = $userModel->getAllUsers();

$stmt = $conn->prepare("SELECT * FROM contacts ORDER BY created_at DESC");
$stmt->execute();
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmtRestaurants = $conn->prepare("SELECT * FROM restaurants ORDER BY id DESC");
$stmtRestaurants->execute();
$restaurants = $stmtRestaurants->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard</title>
<link rel="stylesheet" href="dashboard.css">
</head>
<body>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if ($success): ?>
    <p class="success"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<header class="topbar">
      <a class="back" href="Homepage.php">Home Page</a>
</header>

<div class="dashboard">

    <div class="panel">
        <h2>Users</h2>

        <button class="create-btn" onclick="openModal('createModal')">
            Create New User
        </button>

        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>

            <?php foreach ($users as $u): ?>
            <tr>
                <td><?= (int)($u['id'] ?? 0) ?></td>
                <td><?= htmlspecialchars($u['name'] ?? '-') ?></td>
                <td><?= htmlspecialchars($u['email'] ?? '-') ?></td>
                <td><?= htmlspecialchars($u['role'] ?? '-') ?></td>
                <td><?= htmlspecialchars($u['created_at'] ?? '-') ?></td>
                <td>
                    <button class="edit-btn"
                        onclick="openEditModal(
                            <?= (int)($u['id'] ?? 0) ?>,
                            '<?= htmlspecialchars($u['name'] ?? '', ENT_QUOTES) ?>',
                            '<?= htmlspecialchars($u['email'] ?? '', ENT_QUOTES) ?>',
                            '<?= htmlspecialchars($u['role'] ?? 'user', ENT_QUOTES) ?>'
                        )">
                        Edit
                    </button>

                    <a href="?delete=<?= (int)($u['id'] ?? 0) ?>" onclick="return confirm('Jeni i sigurt?')">
                        <button class="delete-btn" type="button">Delete</button>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="panel">
        <h2>Contact Messages</h2>

        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Message</th>
                <th>Sent</th>
                <th>Actions</th>
            </tr>

            <?php foreach ($contacts as $c): ?>
            <tr>
                <td><?= (int)($c['id'] ?? 0) ?></td>
                <td><?= htmlspecialchars($c['name'] ?? '-') ?></td>
                <td><?= htmlspecialchars($c['email'] ?? '-') ?></td>
                <td><?= htmlspecialchars($c['message'] ?? '-') ?></td>
                <td><?= htmlspecialchars($c['created_at'] ?? '-') ?></td>
                <td>
                    <a href="?delete_msg=<?= (int)($c['id'] ?? 0) ?>" onclick="return confirm('Fshi mesazhin?')">
                        <button class="delete-btn" type="button">Delete</button>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="panel">
        <?php if ($editRestaurant): ?>
            <h2>Edit Restaurant</h2>

            <form method="POST">
                <input type="hidden" name="restaurant_id" value="<?= (int)($editRestaurant['id'] ?? 0) ?>">

                <input
                    type="text"
                    name="restaurant_name"
                    placeholder="Emri i restaurant"
                    value="<?= htmlspecialchars($editRestaurant['name'] ?? '') ?>"
                    required
                >

                <textarea
                    name="restaurant_description"
                    placeholder="Përshkrimi"
                    required
                ><?= htmlspecialchars($editRestaurant['description'] ?? '') ?></textarea>

                <input
                    type="text"
                    name="restaurant_image"
                    placeholder="Foto p.sh. manuka.jpg"
                    value="<?= htmlspecialchars($editRestaurant['image'] ?? '') ?>"
                    required
                >

                <button type="submit" name="update_restaurant" class="create-btn">Update Restaurant</button>
            </form>
        <?php else: ?>
            <h2>Shto Restaurant</h2>

            <form method="POST">
                <input
                    type="text"
                    name="restaurant_name"
                    placeholder="Emri i restaurant"
                    required
                >

                <textarea
                    name="restaurant_description"
                    placeholder="Përshkrimi i restaurant"
                    required
                ></textarea>

                <input
                    type="text"
                    name="restaurant_image"
                    placeholder="Foto p.sh. manuka.jpg"
                    required
                >

                <button type="submit" name="create_restaurant" class="create-btn">Create Restaurant</button>
            </form>
        <?php endif; ?>
    </div>

    <div class="panel">
        <h2>Restaurants</h2>

        <table>
            <tr>
                <th>ID</th>
                <th>Foto</th>
                <th>Emri</th>
                <th>Përshkrimi</th>
                <th>Krijuar</th>
                <th>Actions</th>
            </tr>

            <?php foreach ($restaurants as $r): ?>
            <tr>
                <td><?= (int)($r['id'] ?? 0) ?></td>
                <td>
                    <img
                        src="images/<?= htmlspecialchars($r['image'] ?? 'default.jpg') ?>"
                        alt="<?= htmlspecialchars($r['name'] ?? 'Restaurant') ?>"
                        style="width:80px; height:60px; object-fit:cover; border-radius:8px;"
                    >
                </td>
                <td><?= htmlspecialchars($r['name'] ?? '-') ?></td>
                <td><?= htmlspecialchars($r['description'] ?? '-') ?></td>
                <td><?= htmlspecialchars($r['created_at'] ?? '-') ?></td>
                <td>
                    <a class="edit-btn" href="?edit_restaurant=<?= (int)($r['id'] ?? 0) ?>">Edit</a>
                    <a class="delete-btn" href="?delete_restaurant=<?= (int)($r['id'] ?? 0) ?>" onclick="return confirm('A je i sigurt?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

</div>

<div id="createModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('createModal')">&times;</span>
        <h3>Create User</h3>

        <form method="POST">
            <input type="hidden" name="action" value="create">
            <input type="text" name="name" placeholder="Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirmPassword" placeholder="Confirm Password" required>
            <button type="submit">Create</button>
        </form>
    </div>
</div>

<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editModal')">&times;</span>
        <h3>Edit User</h3>

        <form method="POST">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="editId">

            <input type="text" name="name" id="editName" required>
            <input type="email" name="email" id="editEmail" required>
            <input type="password" name="password" placeholder="New Password">

            <select name="role" id="editRole">
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>

            <button type="submit">Save</button>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).style.display = 'flex';
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

function openEditModal(id, name, email, role) {
    document.getElementById('editId').value = id;
    document.getElementById('editName').value = name;
    document.getElementById('editEmail').value = email;
    document.getElementById('editRole').value = role;
    openModal('editModal');
}

window.onclick = function(e) {
    if (e.target.classList.contains('modal')) {
        e.target.style.display = 'none';
    }
}
</script>

</body>
</html>