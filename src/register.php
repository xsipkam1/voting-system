<?php
session_start();
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: index.php");
    exit;
}

require_once("../../../configFinal.php");

$errors = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $login = trim($_POST['login']);
    $password = $_POST['password'];
    $repeat_password = $_POST['repeat_password'];

    if (empty($login)) {
        $errors[] = "Login je povinný.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $login)) {
        $errors[] = "Login môže obsahovať iba písmená, číslice a podčiarkovníky.";
    }
    if (empty($password)) {
        $errors[] = "Heslo je povinné.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Heslo musí mať aspoň 6 znakov.";
    }
    if ($password !== $repeat_password) {
        $errors[] = "Heslo a opakovanie hesla sa nezhodujú.";
    }

    $sql = "SELECT id FROM users WHERE login = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $login);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) > 0) {
        $errors[] = "Používateľ s týmto loginom už existuje.";
    }
    mysqli_stmt_close($stmt);
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_ARGON2ID);
        $role = "U";
        $sql = "INSERT INTO users (login, password, role) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $login,  $hashed_password, $role);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include "menu.php"; ?>
<div class="out-cont">
    <h2>Registrácia</h2>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">

        <div class="detail">
            <label for="login">Login:</label>
            <input type="text" id="login" name="login">
        </div>
        <div class="detail">
            <label for="password">Heslo:</label>
            <input type="password" id="password" name="password">
        </div>
        <div class="detail">
            <label for="repeat_password">Zopakovať heslo:</label>
            <input type="password" id="repeat_password" name="repeat_password">
        </div>
        <div class="buttons">
            <input type="submit" value="Registrovať">
        </div>
        <?php
        if (!empty($errors)) {
            echo '<div class="error">' . implode("<br>", $errors) . '</div>';
        }
        ?>

    </form>
</div>
</body>
</html>
