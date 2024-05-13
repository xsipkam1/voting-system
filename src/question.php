<?php
session_start();
require_once("../../../configFinal.php");
header('Content-Type: text/html; charset=utf-8');
include_once 'translation.php';
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
    <title><?php echo translate('Otázka'); ?></title>
    <link rel="stylesheet" href="../styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
</head>
<body class="bg-dark-custom">

<div class='p-4 m-2'>
    <form action="../questionAnswer.php" method="post" class="" autocomplete="off">

        <?php
        if (!preg_match('/^[a-zA-Z\/]+$/', $code)) {
            echo "<h3 class='text-center'>".translate('Nesprávny formát kľúča!')."</h3>";
            echo '<div class="text-center">';
            echo '<a href="../index.php" class="btn btn-outline-secondary mt-3">' . translate("DOMOV") . '</a>';
            echo '</div>';
            exit;
        }

        $sql = "SELECT * FROM questions WHERE code = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $code);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if ($row['active'] === 0) {
                echo "<h3 class='text-center'>".translate('Otázka nie je aktívna')."</h3>";
                echo '<div class="text-center">';
                echo '<a href="../index.php" class="btn btn-outline-secondary mt-3">' . translate("DOMOV") . '</a>';
                echo '</div>';
                exit;
            }

            echo "<h3 class='text-center'>".translate('Kód otázky:') . " " . $row['code'] . "</h3>";
            echo "<hr>";
            if ($row['type'] === 'list') {
                echo "<p class='fs-5 mb-2'>" . $row['description'] . "</p>";
                $sql_answers = "SELECT * FROM answers WHERE question_fk = ?";
                $stmt_answers = mysqli_prepare($conn, $sql_answers);
                mysqli_stmt_bind_param($stmt_answers, "i", $row['id']);
                mysqli_stmt_execute($stmt_answers);
                $result_answers = mysqli_stmt_get_result($stmt_answers);

                while ($answer = mysqli_fetch_assoc($result_answers)) {
                    echo "<div class='form-check'>";
                    echo "<input class='form-check-input' type='radio' name='answer' id='answer{$answer['id']}' value='{$answer['id']}'>";
                    echo "<label class='form-check-label fw-normal' for='answer{$answer['id']}'>{$answer['description']}</label>";
                    echo "</div>";
                }

                mysqli_stmt_close($stmt_answers);
            } else {
                echo "<p class='fs-5 mb-2'>" . $row['description'] . "</p>";
                echo "<div class='mb-3'>";
                echo "<input type='text' class='form-control' placeholder='" . translate("Tvoja odpoveď ...") . "' id='freeAnswer' name='freeAnswer'>";
                echo "</div>";
                echo "<input type='hidden' name='questionId' value='{$row['id']}'>";
            }
            echo "</h3>";
            echo "<hr>";
            ?>
            <div class="text-center">
                <button type="submit" class="btn btn-primary mt-3"><?php echo translate('ODPOVEDAŤ'); ?></button>
            </div>
            <?php
        } else {
            echo "<h3 class='text-center'>".translate('Otázka sa nenašla')."</h3>";
            echo '<div class="text-center">';
            echo '<a href="../index.php" class="btn btn-outline-secondary mt-3">' . translate("DOMOV") . '</a>';
            echo '</div>';
        }

        mysqli_stmt_close($stmt);
        ?>


    </form>
</div>
</body>
</html>