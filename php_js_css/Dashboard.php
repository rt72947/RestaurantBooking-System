<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

include_once 'Database.php';
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
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirm = $_POST['confirmPassword'];

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
        $id = $_POST['id'];
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = !empty($_POST['password']) ? $_POST['password'] : null;
        $role = $_POST['role'];

        if ($userModel->updateUser($id, $name, $email, $password, $role)) {
            $success = "User u përditësua me sukses!";
        } else {
            $error = "Gabim gjatë përditësimit!";
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
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Gabim gjatë fshirjes së mesazhit!";
    }
}

$users = $userModel->getAllUsers();

$stmt = $conn->prepare("SELECT * FROM contacts ORDER BY created_at DESC");
$stmt->execute();
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

<div class="dashboard">

    <div class="panel">
        <h2>Users Dashboard</h2>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <?php if ($success): ?>
            <p class="success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

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
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['name']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['role']) ?></td>
                <td><?= htmlspecialchars($u['created_at']) ?></td>
                <td>
                    <button class="edit-btn"
                        onclick="openEditModal(
                            <?= (int)$u['id'] ?>,
                            '<?= htmlspecialchars($u['name'], ENT_QUOTES) ?>',
                            '<?= htmlspecialchars($u['email'], ENT_QUOTES) ?>',
                            '<?= htmlspecialchars($u['role'], ENT_QUOTES) ?>'
                        )">
                        Edit
                    </button>

                    <a href="?delete=<?= $u['id'] ?>" onclick="return confirm('Jeni i sigurt?')">
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
                <td><?= $c['id'] ?></td>
                <td><?= htmlspecialchars($c['name']) ?></td>
                <td><?= htmlspecialchars($c['email']) ?></td>
                <td><?= htmlspecialchars($c['message']) ?></td>
                <td><?= htmlspecialchars($c['created_at']) ?></td>
                <td>
                    <a href="?delete_msg=<?= $c['id'] ?>" onclick="return confirm('Fshi mesazhin?')">
                        <button class="delete-btn" type="button">Delete</button>
                    </a>
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
    document.getElementById(id).style.display = 'block';
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