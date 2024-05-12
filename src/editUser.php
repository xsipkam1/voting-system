<?php
session_start();
require_once("../../../configFinal.php");

if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && $_SESSION['role'] === 'A')) {
    header("Location: index.php");
    exit;
}

$errors = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_POST['userId'];
    $newLogin = $_POST['userLogin'];
    $newRole = $_POST["userRole"];
    $newPassword = $_POST["userPassword"];

    if (empty(trim($newLogin))) {
        $errors[] = "Login je povinný";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $newLogin)) {
        $errors[] = "Login môže obsahovať iba písmená, číslice a podčiarkovníky";
    }

    $sql = "SELECT login FROM users WHERE login = ? AND id != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $newLogin, $userId);
    $stmt->execute();
    $stmt->store_result();
    $logins_existing = $stmt->num_rows;
    $stmt->close();

    if ($logins_existing != 0){
        $errors[] = "Takýto Login už existuje";
    }

    if (!empty($newPassword)){
        if (strlen($newPassword) < 6) {
            $errors[] = "Heslo musí mať aspoň 6 znakov";
        }
    }

    if (!empty($errors)){
        $_SESSION['roleUpdateErrors'] = $errors;
        $_SESSION['roleUpdateSuccess'] = false;
        header("Location: manageUsers.php");
        exit;
    }

    $sql = "UPDATE users SET role = ?, login = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $newRole, $newLogin, $userId);
    
    $stmt->execute();
    $stmt->close();
    
    if (!empty($newPassword)) {
        $hashedPassword = password_hash($newPassword, PASSWORD_ARGON2ID);
        $sqlUpdatePassword = "UPDATE users SET password = ? WHERE id = ?";
        $stmtUpdatePassword = $conn->prepare($sqlUpdatePassword);
        $stmtUpdatePassword->bind_param("si", $hashedPassword, $userId);
        $stmtUpdatePassword->execute();
        $stmtUpdatePassword->close();
    }

    $_SESSION['roleUpdateSuccess'] = true;
    header("Location: manageUsers.php");
    exit;
}
?>
