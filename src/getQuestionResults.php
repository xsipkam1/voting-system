<?php
session_start();
require_once("../../../configFinal.php");
include_once 'translation.php';

if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && ($_SESSION['role'] === 'A' || $_SESSION['role'] === 'U'))) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $questionId = $_POST['questionId'];
    $_SESSION['questioResults'] = "";

    ob_start();

    $sql_questions = "SELECT description FROM questions WHERE id = ?";
    $stmt_questions = mysqli_prepare($conn, $sql_questions);
    mysqli_stmt_bind_param($stmt_questions, "i", $questionId);
    mysqli_stmt_execute($stmt_questions);
    $result_question = mysqli_stmt_get_result($stmt_questions);
    $row_question = mysqli_fetch_assoc($result_question);
    mysqli_stmt_close($stmt_questions);

    $sql_answers = "SELECT * FROM answers WHERE question_fk = ?";
    $stmt_answers = mysqli_prepare($conn, $sql_answers);
    mysqli_stmt_bind_param($stmt_answers, "i", $questionId);
    mysqli_stmt_execute($stmt_answers);
    $result_answers = mysqli_stmt_get_result($stmt_answers);

    $totalVotes = 0;
    while ($row_answer = mysqli_fetch_assoc($result_answers)) {
        $totalVotes += $row_answer['votes'];
    }

    echo "<h3 class='text-center'>" . $row_question['description'] . "</h3>";
    echo "<h3 class='text-center text-muted'>" . translate("počet odpovedí") . ": " . $totalVotes . "</h3>";
    mysqli_data_seek($result_answers, 0);
    while ($row_answer = mysqli_fetch_assoc($result_answers)) {
        if ($totalVotes == 0){
            $percentage = 0;
        }
        else{
            $percentage = ($row_answer['votes'] / $totalVotes) * 100;
        }
        echo "<div class='mb-3'>";
        echo "<label for='answer{$row_answer['id']}' class='form-label'>{$row_answer['description']}</label>";
        echo "<div class='progress' role='progressbar' style='height: 20px;'>";
        echo "<div class='progress-bar' style='width: {$percentage}%;' aria-valuenow='{$percentage}' aria-valuemin='0' aria-valuemax='100'>{$row_answer['votes']}</div>";
        echo "</div>";
        echo "</div>";
    }

    $_SESSION['questioResults'] = ob_get_clean();

    mysqli_stmt_close($stmt_answers);

    header("Location: index.php");
    exit;
}
else {
    header("Location: index.php");
    exit;
}
?>