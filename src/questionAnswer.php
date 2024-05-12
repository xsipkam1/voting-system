<?php
session_start();
require_once("../../../configFinal.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['answer'])) {

        $selectedAnswerId = filter_input(INPUT_POST, 'answer', FILTER_VALIDATE_INT);
        if ($selectedAnswerId === false || $selectedAnswerId === null) {
            echo "Invalid answer ID!";
            header("Location: index.php");
            exit;
        }

        $sql_update_votes = "UPDATE answers SET votes = votes + 1 WHERE id = ?";
        $stmt_update_votes = mysqli_prepare($conn, $sql_update_votes);
        mysqli_stmt_bind_param($stmt_update_votes, "i", $selectedAnswerId);
        mysqli_stmt_execute($stmt_update_votes);
        mysqli_stmt_close($stmt_update_votes);
    } elseif (isset($_POST['freeAnswer']) && !empty($_POST['freeAnswer'])) {

        $freeAnswer = $_POST['freeAnswer'];
        $questionId = $_POST['questionId'];
        $sql_check_answer = "SELECT * FROM answers WHERE description = ? AND question_fk = ?";
        $stmt_check_answer = mysqli_prepare($conn, $sql_check_answer);
        mysqli_stmt_bind_param($stmt_check_answer, "si", $freeAnswer, $questionId);
        mysqli_stmt_execute($stmt_check_answer);
        $result_check_answer = mysqli_stmt_get_result($stmt_check_answer);

        if ($row_check_answer = mysqli_fetch_assoc($result_check_answer)) {

            $sql_update_votes = "UPDATE answers SET votes = votes + 1 WHERE id = ?";
            $stmt_update_votes = mysqli_prepare($conn, $sql_update_votes);
            mysqli_stmt_bind_param($stmt_update_votes, "i", $row_check_answer['id']);
            mysqli_stmt_execute($stmt_update_votes);
            mysqli_stmt_close($stmt_update_votes);
            $selectedAnswerId = $row_check_answer['id'];

        } else {
            $sql_insert_answer = "INSERT INTO answers (description, question_fk, votes) VALUES (?, ?, 1)";
            $stmt_insert_answer = mysqli_prepare($conn, $sql_insert_answer);
            mysqli_stmt_bind_param($stmt_insert_answer, "si", $freeAnswer, $questionId);
            mysqli_stmt_execute($stmt_insert_answer);
            mysqli_stmt_close($stmt_insert_answer);
            $selectedAnswerId = mysqli_insert_id($conn);
        }
    } else {
        echo "Answer not provided!";
        header("Location: index.php");
        exit;
    }
}
?>
<!doctype html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Answers</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
</head>
<body class="bg-dark-custom">
<?php include "menu.php"; ?>
<div class="container-md p-1 mt-4 mb-4 ">
    <?php
    $sql_question_fk = "SELECT question_fk FROM answers WHERE id = ?";
    $stmt_question_fk = mysqli_prepare($conn, $sql_question_fk);
    mysqli_stmt_bind_param($stmt_question_fk, "i", $selectedAnswerId);
    mysqli_stmt_execute($stmt_question_fk);
    $result_question_fk = mysqli_stmt_get_result($stmt_question_fk);

    if ($row_question_fk = mysqli_fetch_assoc($result_question_fk)) {
        $sql_question = "SELECT * FROM questions WHERE id = ?";
        $stmt_question = mysqli_prepare($conn, $sql_question);
        mysqli_stmt_bind_param($stmt_question, "i", $row_question_fk['question_fk']);
        mysqli_stmt_execute($stmt_question);
        $result_question = mysqli_stmt_get_result($stmt_question);

        if ($row_question = mysqli_fetch_assoc($result_question)) {
            echo "<span class='fs-5 text-muted'>" . $row_question['description'] . "</span>";
            echo "</h3>";

            $sql_answers = "SELECT * FROM answers WHERE question_fk = ?";
            $stmt_answers = mysqli_prepare($conn, $sql_answers);
            mysqli_stmt_bind_param($stmt_answers, "i", $row_question['id']);
            mysqli_stmt_execute($stmt_answers);
            $result_answers = mysqli_stmt_get_result($stmt_answers);

            $totalVotes = 0;
            while ($row_answer = mysqli_fetch_assoc($result_answers)) {
                $totalVotes += $row_answer['votes'];
            }

            mysqli_data_seek($result_answers, 0);
            while ($row_answer = mysqli_fetch_assoc($result_answers)) {
                $percentage = ($row_answer['votes'] / $totalVotes) * 100;
                echo "<div class='mb-3'>";
                echo "<label for='answer{$row_answer['id']}' class='form-label'>{$row_answer['description']}</label>";
                echo "<div class='progress' role='progressbar' style='height: 20px;'>";
                echo "<div class='progress-bar' style='width: {$percentage}%;' aria-valuenow='{$percentage}' aria-valuemin='0' aria-valuemax='100'>{$row_answer['votes']}</div>";
                echo "</div>";
                echo "</div>";
            }

            mysqli_stmt_close($stmt_answers);
        } else {
            echo "Question not found!";
        }

        mysqli_stmt_close($stmt_question);
    } else {
        echo "Question ID not found!";
    }

    mysqli_stmt_close($stmt_question_fk);
    ?>
</div>
</body>
</html>
