<?php
session_start();
require_once("../../../configFinal.php");

if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && $_SESSION['role'] === 'A')) {
    header("Location: index.php");
    exit;
}

$errors = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userLogin = $_POST['userLogin'];
    $userRole = $_POST["userRole"];
    $userPassword = $_POST["userPassword"];

    if (empty(trim($userLogin))) {
        $errors[] = "Login je povinný";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $userLogin)) {
        $errors[] = "Login môže obsahovať iba písmená, číslice a podčiarkovníky";
    }

    $sql = "SELECT login FROM users WHERE login = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userLogin);
    $stmt->execute();
    $stmt->store_result();
    $logins_existing = $stmt->num_rows;
    $stmt->close();

    if ($logins_existing != 0){
        $errors[] = "Tykýto Login už existuje";
    }

    if (empty($userPassword)) {
        $errors[] = "Heslo je povinné";
    } elseif (strlen($userPassword) < 6) {
        $errors[] = "Heslo musí mať aspoň 6 znakov";
    }

    if (!empty($errors)){
        $_SESSION['userCreationErrors'] = $errors;
        $_SESSION['userCreationSuccess'] = false;
        header("Location: manageUsers.php");
        exit;
    }

    $sql = "INSERT INTO users (login, password, role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $userLogin, password_hash($userPassword, PASSWORD_ARGON2ID), $userRole);
    $stmt->execute();
    $stmt->close();

    $_SESSION['userCreationSuccess'] = true;
    header("Location: manageUsers.php");
    exit;
}
?>