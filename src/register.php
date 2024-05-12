<?php
session_start();
include_once 'translation.php';
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
        $errors[] = translate("Login je povinný.");
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $login)) {
        $errors[] = translate("Login môže obsahovať iba písmená, číslice a podčiarkovníky.");
    }
    if (empty($password)) {
        $errors[] = translate("Heslo je povinné.");
    } elseif (strlen($password) < 6) {
        $errors[] = translate("Heslo musí mať aspoň 6 znakov.");
    }
    if ($password !== $repeat_password) {
        $errors[] = translate("Heslo a opakovanie hesla sa nezhodujú.");
    }

    $sql = "SELECT id FROM users WHERE login = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $login);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) > 0) {
        $errors[] = translate("Používateľ s týmto loginom už existuje.");
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
        $_SESSION['registrationSuccess'] = true;
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body class="bg-dark-custom">
<?php include "menu.php"; ?>
<div class="out-cont">
    <h2><?php echo mb_strtoupper(translate('Registrácia'), 'UTF-8'); ?></h2>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">

        <div class="detail">
            <label for="login">Login:</label>
            <input type="text" id="login" name="login">
        </div>
        <div class="detail">
        <label for="password"><?php echo translate("Heslo"); ?>:</label>

            <input type="password" id="password" name="password">
        </div>
        <div class="detail">
        <label for="repeat_password"><?php echo translate("Zopakovať heslo"); ?>:</label>
            <input type="password" id="repeat_password" name="repeat_password">
        </div>
        <div class="buttons">
            <input type="submit" value="<?php echo translate('Registrovať'); ?>">
        </div>
        <?php
        if (!empty($errors)) {
            echo '<div class="error">' . implode("<br>", $errors) . '</div>';
        }
        ?>

    </form>
</div>

<div class="modal fade" id="registrationSuccessModal" tabindex="-1" aria-labelledby="registrationSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-center">
            <h3 class="modal-title w-100" id="registrationSuccessModalLabel"><?php echo translate('REGISTRÁCIA ÚSPEŠNÁ'); ?></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <?php echo translate("Váš účet bol úspešne vytvorený. Teraz sa môžete prihlásiť pod loginom, ktorý ste si zvolili."); ?>
            </div>

            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<script>
    <?php if (isset($_SESSION['registrationSuccess']) && $_SESSION['registrationSuccess'] === true) : ?>
        var registrationSuccessModal = new bootstrap.Modal(document.getElementById('registrationSuccessModal'));
        registrationSuccessModal.show();
        <?php unset($_SESSION['registrationSuccess']); ?>
    <?php endif; ?>
</script>

</body>
</html>

