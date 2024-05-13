<?php
session_start();
include_once 'translation.php';
require_once("../../../configFinal.php");


if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && ($_SESSION['role'] === 'A' || $_SESSION['role'] === 'U'))) {
    header("Location: index.php");
    exit;
}

$errors = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $password = $_POST['password'];
    $repeat_password = $_POST['repeat_password'];

    if (empty($password)) {
        $errors[] = translate("Heslo je povinné.");
    } elseif (strlen($password) < 6) {
        $errors[] = translate("Heslo musí mať aspoň 6 znakov.");
    }
    if ($password !== $repeat_password) {
        $errors[] = translate("Heslo a opakovanie hesla sa nezhodujú.");
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_ARGON2ID);
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $hashed_password, $_SESSION['id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        $_SESSION['passwordChangedSuccess'] = true;
    } else {
        $_SESSION['passwordChangedError'] = true;
        $_SESSION['passwordChangeErrors'] = $errors;
    }
    header("Location: index.php");
    exit;
}

?>