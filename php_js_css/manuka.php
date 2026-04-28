<?php
require_once 'database.php';

$db = new Database();
$conn = $db->getConnection();

$errors = [];
$success = "";

// INPUTET
$fullName = "";
$phone = "";
$email = "";
$date = "";
$time = "";
$guests = "";
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fullName = trim($_POST["fullName"] ?? "");
    $phone    = trim($_POST["phone"] ?? "");
    $email    = trim($_POST["email"] ?? "");
    $date     = trim($_POST["date"] ?? "");
    $time     = trim($_POST["time"] ?? "");
    $guests   = trim($_POST["guests"] ?? "");
    $message  = trim($_POST["message"] ?? "");

    // ================= VALIDIMI =================

    // EMRI
    if ($fullName === "") {
        $errors["fullName"] = "Emri është i detyrueshëm.";
    } elseif (!preg_match("/^[a-zA-ZÀ-ž\s]{2,60}$/u", $fullName)) {
        $errors["fullName"] = "Emri duhet të ketë vetëm shkronja (2-60 karaktere).";
    }

    // TELEFONI
    if ($phone === "") {
        $errors["phone"] = "Telefoni është i detyrueshëm.";
    } elseif (!preg_match("/^\+?[0-9\s\-]{8,20}$/", $phone)) {
        $errors["phone"] = "Numri i telefonit nuk është valid.";
    }

    // EMAIL
    if ($email === "") {
        $errors["email"] = "Email është i detyrueshëm.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Email nuk është valid.";
    }

    // DATA
    if ($date === "") {
        $errors["date"] = "Data është e detyrueshme.";
    } elseif (strtotime($date) < strtotime(date("Y-m-d"))) {
        $errors["date"] = "Data nuk mund të jetë në të kaluarën.";
    }

    // ORA
    if ($time === "") {
        $errors["time"] = "Ora është e detyrueshme.";
    }

    // GUESTS
    if ($guests === "") {
        $errors["guests"] = "Numri i mysafirëve është i detyrueshëm.";
    } elseif (!is_numeric($guests) || $guests <= 0) {
        $errors["guests"] = "Numër jo valid.";
    }

    // ================= INSERT =================

    if (empty($errors)) {

        $sql = "INSERT INTO bookings 
        (full_name, phone, email, date, time, guests, message) 
        VALUES (:fullName, :phone, :email, :date, :time, :guests, :message)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ":fullName" => $fullName,
            ":phone"    => $phone,
            ":email"    => $email,
            ":date"     => $date,
            ":time"     => $time,
            ":guests"   => $guests,
            ":message"  => $message
        ]);

        $success = "Rezervimi u dërgua me sukses!";

        // Reset
        $fullName = $phone = $email = $date = $time = $guests = $message = "";
    }
}
?>

<!DOCTYPE html>
<html lang="sq">
<head>
<meta charset="UTF-8">
<title>Booking</title>

<style>
body {
    font-family: Poppins;
    background: #111;
    color: white;
}

.form-box {
    max-width: 400px;
    margin: 50px auto;
}

input, textarea {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
}

.error {
    color: red;
    font-size: 13px;
}

.success {
    color: gold;
    text-align: center;
    margin-bottom: 15px;
}

button {
    margin-top: 10px;
    padding: 10px;
    width: 100%;
    background: gold;
    border: none;
}
</style>

</head>
<body>

<div class="form-box">

<h2>Rezervo Tavolinë</h2>

<?php if ($success): ?>
    <div class="success"><?php echo $success; ?></div>
<?php endif; ?>

<form method="POST">

<input type="text" name="fullName" placeholder="Emri"
value="<?php echo htmlspecialchars($fullName); ?>">
<div class="error"><?php echo $errors["fullName"] ?? ""; ?></div>

<input type="text" name="phone" placeholder="Telefoni"
value="<?php echo htmlspecialchars($phone); ?>">
<div class="error"><?php echo $errors["phone"] ?? ""; ?></div>

<input type="email" name="email" placeholder="Email"
value="<?php echo htmlspecialchars($email); ?>">
<div class="error"><?php echo $errors["email"] ?? ""; ?></div>

<input type="date" name="date"
value="<?php echo htmlspecialchars($date); ?>">
<div class="error"><?php echo $errors["date"] ?? ""; ?></div>

<input type="time" name="time"
value="<?php echo htmlspecialchars($time); ?>">
<div class="error"><?php echo $errors["time"] ?? ""; ?></div>

<input type="number" name="guests" placeholder="Nr. mysafirëve"
value="<?php echo htmlspecialchars($guests); ?>">
<div class="error"><?php echo $errors["guests"] ?? ""; ?></div>

<textarea name="message" placeholder="Mesazh"><?php echo htmlspecialchars($message); ?></textarea>

<button type="submit">Rezervo</button>

</form>

</div>

</body>
</html>