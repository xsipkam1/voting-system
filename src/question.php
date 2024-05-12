<?php
session_start();
require_once("../../../configFinal.php");
header('Content-Type: text/html; charset=utf-8');

$key = isset($_GET['key']) ? $_GET['key'] : '';
//$key = "/webte2-final/src/question/HyDPI";
//$key = "/webte2-final/src/question/LvapX";
$code = substr($key, -5);

?>

<!doctype html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Question</title>
    <link rel="stylesheet" href="../styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
</head>
<body>

<div class='border border-light shadow p-4 m-2 bg-white bg-gradient'>
    <form action="../questionAnswer.php" method="post" class="" autocomplete="off">

    <?php
        if (!preg_match('/^[a-zA-Z\/]+$/', $code)) {
            // Invalid $key format
            echo "Invalid key format!";
            exit; // or handle the error appropriately
        }

        $sql = "SELECT * FROM questions WHERE code = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $code);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {

            echo "<h3>Otázka: " . $row['code'];
            if ($row['type'] === 'list') {
                echo "<span class='fs-5 text-muted'>  (otázka s výberom správnej odpovede)</span>";

                echo "<hr>";
                echo "<p class='fs-5 mb-2'>" . $row['description'] . "</p>";
                echo "<hr>";
                $sql_answers = "SELECT * FROM answers WHERE question_fk = ?";
                $stmt_answers = mysqli_prepare($conn, $sql_answers);
                mysqli_stmt_bind_param($stmt_answers, "i", $row['id']);
                mysqli_stmt_execute($stmt_answers);
                $result_answers = mysqli_stmt_get_result($stmt_answers);

                while ($answer = mysqli_fetch_assoc($result_answers)) {
                    echo "<div class='form-check'>";
                    echo "<input class='form-check-input' type='radio' name='answer' id='answer{$answer['id']}' value='{$answer['id']}'>";
                    echo "<label class='form-check-label' for='answer{$answer['id']}'>{$answer['description']}</label>";
                    echo "</div>";
                }

                mysqli_stmt_close($stmt_answers);
            } else {
                echo "<span class='fs-5 text-muted'>  (otázka s voľnou odpoveďou)</span>";
                echo "<hr>";
                echo "<p class='fs-5 mb-2'>" . $row['description'] . "</p>";
                echo "<hr>";

                echo "<div class='mb-3'>";
                echo "<label for='freeAnswer' class='form-label'>Tvoja odpoveď</label>";
                echo "<input type='text' class='form-control' id='freeAnswer' name='freeAnswer'>";
                echo "</div>";
                echo "<input type='hidden' name='questionId' value='{$row['id']}'>";
            }
            echo "</h3>";
            echo "<hr>";
            ?>
            <button type="submit" class="btn btn-primary mt-3">Submit</button>
            <?php
        } else {
            echo "Question not found!";
        }

        mysqli_stmt_close($stmt);
        ?>


    </form>
</div>
</body>
</html>