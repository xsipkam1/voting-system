<?php
session_start();
require_once("../../../configFinal.php");

if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && $_SESSION['role'] === 'A')) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_POST['userId'];
    $newRole = $_POST["userRole"];
    $newPassword = $_POST["userPassword"];

    $sql = "UPDATE users SET role = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $newRole, $userId);

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
