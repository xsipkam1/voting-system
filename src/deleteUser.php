<?php
session_start();
require_once("../../../configFinal.php");

if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && $_SESSION['role'] === 'A')) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_POST['userId'];

    $sqlDeleteAnswers = "DELETE a FROM answers a JOIN questions q ON a.question_fk = q.id WHERE q.user_fk = ?";
    $stmtDeleteAnswers = $conn->prepare($sqlDeleteAnswers);
    $stmtDeleteAnswers->bind_param("i", $userId);
    $stmtDeleteAnswers->execute();
    $stmtDeleteAnswers->close();

    $sqlDeleteQuestions = "DELETE FROM questions WHERE user_fk = ?";
    $stmtDeleteQuestions = $conn->prepare($sqlDeleteQuestions);
    $stmtDeleteQuestions->bind_param("i", $userId);
    $stmtDeleteQuestions->execute();
    $stmtDeleteQuestions->close();

    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();
    $_SESSION['deletionSuccess'] = true;
    header("Location: manageUsers.php");
    exit;
}
?>
