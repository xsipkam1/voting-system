<?php
session_start();
require_once("../../../configFinal.php");

if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && ($_SESSION['role'] === 'A' || $_SESSION['role'] === 'U'))) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $questionId = $_POST['questionId'];
    $closingNote = $_POST['closingNote'];
    $active = 0;

    if (empty(trim($closingNote))){
        $_SESSION['votingClosedSuccess'] = false;
        header("Location: index.php");
        exit;
    }

    $sqlCloseQuestion = "UPDATE questions SET active = ?, date_closed = IFNULL(date_closed, NOW()), note_closed = IFNULL(note_closed, ?) WHERE id = ?";
    $stmtCloseQuestion = $conn->prepare($sqlCloseQuestion);
    $stmtCloseQuestion->bind_param("isi", $active, $closingNote, $questionId);
    $stmtCloseQuestion->execute();
    $stmtCloseQuestion->close();

    $_SESSION['votingClosedSuccess'] = true;
    header("Location: index.php");
    exit;
}
?>