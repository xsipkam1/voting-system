<?php
session_start();
require_once("../../../configFinal.php");

if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && ($_SESSION['role'] === 'A' || $_SESSION['role'] === 'U'))) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $questionId = $_POST['questionId'];

    $sqlDeleteAnswers = "DELETE FROM answers WHERE question_fk = ?";
    $stmtDeleteAnswers = $conn->prepare($sqlDeleteAnswers);
    $stmtDeleteAnswers->bind_param("i", $questionId);
    $stmtDeleteAnswers->execute();
    $stmtDeleteAnswers->close();

    $sqlDeleteQuestions = "DELETE FROM questions WHERE id = ?";
    $stmtDeleteQuestions = $conn->prepare($sqlDeleteQuestions);
    $stmtDeleteQuestions->bind_param("i", $questionId);
    $stmtDeleteQuestions->execute();
    $stmtDeleteQuestions->close();

    $_SESSION['deletionSuccess'] = true;
    header("Location: index.php");
    exit;
}
?>