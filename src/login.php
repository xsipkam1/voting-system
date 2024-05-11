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
    if (empty($errors)) {
        $sql = "SELECT id, login, password, role FROM users WHERE login = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $login);
        mysqli_stmt_execute($stmt);

        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) == 1) {
            mysqli_stmt_bind_result($stmt, $id, $login, $hashed_password, $role);
            mysqli_stmt_fetch($stmt);
            if (password_verify($password, $hashed_password)) {
                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = $id;
                $_SESSION["login"] = $login;
                $_SESSION["role"] = $role;
                header("location: index.php");
                exit;
            } else {
                $errors[] = "Nesprávne meno alebo heslo.";
            }
        } else {
            $errors[] = "Nesprávne meno alebo heslo.";
        }
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
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body>
<?php include "menu.php"; ?>
<div class="out-cont">
    <h2>PRIHLÁSENIE</h2>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">

        <div class="detail">
            <label for="login">Login:</label>
            <input type="text" id="login" name="login">
        </div>
        <div class="detail">
            <label for="password">Heslo:</label>
            <input type="password" id="password" name="password">
        </div>
        <div class="buttons">
            <input type="submit" value="Prihlásiť">
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
