<?php
session_start();
require_once("../../../configFinal.php");
include_once 'translation.php';
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo translate('Hlasovací systém'); ?></title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body>
    <?php include "menu.php"; ?>

    <div class="container border p-1 mt-4 shadow mb-4 bg-dark-custom">
        <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) : ?>
            <h2><?php echo translate('Prihlásený ako'); ?>: <?= $_SESSION['login'] ?> <span class="text-muted">(<?= ($_SESSION['role'] === 'A' ? 'admin' : 'user') ?>)</span></h2>

            <div class="d-flex justify-content-between mt-4 filter-options">
                
                <div class="dropdown d-flex mb-2 ms-2">
                    <div class="input-group-text bg-dark-subtle"><?php echo translate('Predmet'); ?>:</div>
                    <select class="form-select" aria-label="select1">
                        <option value="1">Tu budu options na filtrovanie na zaklade predmetov</option>
                    </select>
                </div>
                
                <div class="dropdown d-flex mb-2">
                    <div class="input-group-text bg-dark-subtle"><?php echo translate('Dátum vytvorenia'); ?>:</div>
                    <select class="form-select" aria-label="select2">
                        <option value="1">Tu budu options na filtrovanie na zaklade datumu vytvorenia</option>
                    </select>
                </div>

                <a href="createQuestion.php" class="btn btn-primary mb-2 me-2"><?php echo translate('Vytvoriť otázku'); ?></a>
            </div>

            <?php
                $sql = "SELECT * FROM questions WHERE user_fk = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "i", $_SESSION['id']);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) > 0) {
                    $questionNumber = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<div class='border border-light shadow p-4 m-2 bg-white bg-gradient'>";

                        echo "<h3>" . translate('Otázka č.') . $questionNumber;

                            if ($row['type'] === 'list') {
                                echo "<span class='fs-5 text-muted'>(" . translate('otázka s výberom správnej odpovede') . ")</span>";
                            } else {
                                echo "<span class='fs-5 text-muted'>(" . translate('otázka s voľnou odpoveďou') . ")</span>";
                            }
                            echo "</h3>";

                            echo "<hr>";
                            echo "<p class='fs-5 mb-2'>" . $row['description'] . "</p>";

                            $sql_answers = "SELECT * FROM answers WHERE question_fk = ?";
                            $stmt_answers = mysqli_prepare($conn, $sql_answers);
                            mysqli_stmt_bind_param($stmt_answers, "i", $row['id']);
                            mysqli_stmt_execute($stmt_answers);
                            $result_answers = mysqli_stmt_get_result($stmt_answers);

                            if (mysqli_num_rows($result_answers) > 0) {
                                echo "<ul>";
                                while ($row_answers = mysqli_fetch_assoc($result_answers)) {
                                    echo "<li>" . $row_answers['description'] . "</li>";
                                }
                                echo "</ul>";
                            }
                            echo "<hr>";

                            echo "<p class='fs-6 mb-1'>" . translate('Predmet') . ": " . $row['subject'] . "</p>";
                            echo "<p class='fs-6 mb-1'>" . translate('Dátum vytvorenia') . ": " . $row['date_created'] . "</p>";
                            echo "<p class='fs-6'>" . translate('Aktívna') . ": " . ($row['active'] ? translate('áno') : translate('nie')) . "</p>";
                            echo "<div class='d-flex question-buttons'>";
                                echo "<button class='btn btn-outline-secondary h6 me-1'>" . translate('UPRAVIŤ') . "</button>";
                                echo "<button class='btn btn-outline-secondary h6 me-1'>" . translate('KOPÍROVAŤ') . "</button>";
                                echo "<button class='btn btn-outline-secondary h6 me-1'>" . translate('ZMAZAŤ') . "</button>";
                                echo "<button class='btn btn-outline-secondary h6 me-1'>" . translate('VÝSLEDKY HLASOVANIA') . "</button>";
                                echo "<button class='btn btn-outline-secondary h6'>" . translate('UZATVORIŤ HLASOVANIE') . "</button>";
                            echo "</div>";
                            

                        echo "</div>";
                        $questionNumber++;
                    }
                } else {
                    echo "<h3 class='text-center mt-3 mb-4'>Zatiaľ ste nevytvorili žiadne otázky.</h3>";
                }

                mysqli_stmt_close($stmt);
                mysqli_close($conn);
            ?>

        <?php else : ?>
            <h2><?php echo translate('Neprihlásený používateľ'); ?></h2>
        <?php endif; ?>
    </div>
    
</body>