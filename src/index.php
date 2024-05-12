<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once("../../../configFinal.php");
include_once 'translation.php';

function getUsername($userId, $conn) {
    $sql = "SELECT login FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        return $row['login'];
    } else {
        return "neznámy";
    }
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php translate('Hlasovací systém'); ?></title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body class="bg-dark-custom">
    <?php include "menu.php"; ?>

    <div class="container-md p-1 mt-4 mb-4">
        <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) : ?>
            <h2><?php translate('Prihlásený ako'); ?>: <?= $_SESSION['login'] ?> <span class="text-muted">(<?= ($_SESSION['role'] === 'A' ? 'admin' : 'user') ?>)</span></h2>

            <div class="d-flex justify-content-between mt-4 filter-options">
                
                <div class="dropdown d-flex mb-2 ms-2">
                    <div class="input-group-text bg-dark-subtle"><?php translate('Predmet'); ?>:</div>
                    <select class="form-select" id="subjectFilter" aria-label="select1">
                        <option value="">Všetky</option>
                        <?php
                            if ($_SESSION['role'] === 'A') {
                                $sqlSubjects = "SELECT DISTINCT subject FROM questions";
                                $stmtSubjects = $conn->prepare($sqlSubjects);
                                $stmtSubjects->execute();
                            } else {
                                $sqlSubjects = "SELECT DISTINCT subject FROM questions WHERE user_fk = ?";
                                $stmtSubjects = $conn->prepare($sqlSubjects);
                                $stmtSubjects->bind_param("i", $_SESSION['id']);
                                $stmtSubjects->execute();
                            }
                            $resultSubjects = $stmtSubjects->get_result();
                            if ($resultSubjects && $resultSubjects->num_rows > 0) {
                                while ($rowSubject = $resultSubjects->fetch_assoc()) {
                                    echo "<option value='" . $rowSubject['subject'] . "'>" . $rowSubject['subject'] . "</option>";
                                }
                            }
                            $stmtSubjects->close();
                        ?>
                    </select>
                </div>
                
                <div class="dropdown d-flex mb-2">
                    <div class="input-group-text bg-dark-subtle"><?php translate('Dátum vytvorenia'); ?>:</div>
                    <select class="form-select" id="dateFilter" aria-label="select2">
                        <option value="">Všetky</option>
                        <?php
                            if ($_SESSION['role'] === 'A') {
                                $sqlSubjects = "SELECT DISTINCT DATE(date_created) AS creation_date FROM questions";
                                $stmtDates = $conn->prepare($sqlSubjects);
                                $stmtDates->execute();
                            } else {
                                $sqlDates = "SELECT DISTINCT DATE(date_created) AS creation_date FROM questions WHERE user_fk = ?";
                                $stmtDates = $conn->prepare($sqlDates);
                                $stmtDates->bind_param("i", $_SESSION['id']);
                                $stmtDates->execute();
                            }
                            
                            $resultDates = $stmtDates->get_result();
                            if ($resultDates && $resultDates->num_rows > 0) {
                                while ($rowDate = $resultDates->fetch_assoc()) {
                                    echo "<option value='" . $rowDate['creation_date'] . "'>" . $rowDate['creation_date'] . "</option>";
                                }
                            }
                            $stmtDates->close();
                        ?>
                    </select>
                </div>

                <?php
                    if ($_SESSION['role'] === 'A') {
                        echo '<div class="dropdown d-flex mb-2">';
                        echo '<div class="input-group-text bg-dark-subtle">Používateľ:</div>';
                        echo '<select class="form-select" id="userFilter" aria-label="select3">';
                        echo '<option value="">Všetci</option>';

                        $sqlUsers = "SELECT DISTINCT user_fk FROM questions WHERE user_fk != ?";
                        $stmtUsers = $conn->prepare($sqlUsers);
                        $stmtUsers->bind_param("i", $_SESSION['id']);
                        $stmtUsers->execute();

                        $resultUsers = $stmtUsers->get_result();
                        if ($resultUsers && $resultUsers->num_rows > 0) {
                            while ($rowUser = $resultUsers->fetch_assoc()) {
                                $username = getUsername($rowUser['user_fk'], $conn);
                                echo "<option value='" . $rowUser['user_fk'] . "'>" . $username . "</option>";
                            }
                        }
                        $stmtUsers->close();

                        echo '</select>';
                        echo '</div>';
                    }
                ?>

                <a href="createQuestion.php" class="btn btn-primary mb-2 me-2"><i class="bi bi-plus-square"></i></a>
            </div>

            <div class="questions">
                <?php
                    if ($_SESSION['role'] === 'A') {
                        $sql = "SELECT * FROM questions";
                        $stmt = mysqli_prepare($conn, $sql);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                    } else {
                        $sql = "SELECT * FROM questions WHERE user_fk = ?";
                        $stmt = mysqli_prepare($conn, $sql);
                        mysqli_stmt_bind_param($stmt, "i", $_SESSION['id']);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                    }

                    if (mysqli_num_rows($result) > 0) {
                        $questionNumber = 1;
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<div class='border border-light shadow p-4 m-2 bg-white bg-gradient'>";
                                echo "<h3>Otázka č. " . $questionNumber;
                                if ($row['type'] === 'list') {
                                    echo "<span class='fs-5 text-muted'>  (otázka s výberom správnej odpovede)</span>";
                                } else {
                                    echo "<span class='fs-5 text-muted'>  (otázka s voľnou odpoveďou)</span>";
                                }
                                echo "</h3>";

                                if ($_SESSION['role'] === 'A' && $row['user_fk'] !== $_SESSION['id']) {
                                    echo "<p class='fs-5 text-muted user' data-user-id='" . $row['user_fk'] . "'>- od používateľa " . getUsername($row['user_fk'], $conn) . "</p>";
                                }

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

                                $collapseId = "collapse" . $row['id'];
                                echo "<button class='btn btn-secondary mb-2' type='button' data-bs-toggle='collapse' data-bs-target='#$collapseId' aria-expanded='false' aria-controls='$collapseId'><i class='bi bi-chevron-down'></i> ROZBALIŤ</button>";

                                echo "<div class='collapse' id='$collapseId'>";
                                    echo "<p class='fs-6 mb-1'>Predmet: " . $row['subject'] . "</p>";
                                    echo "<p class='fs-6 mb-1'>Dátum vytvorenia: " . $row['date_created'] . "</p>";
                                    echo "<p class='fs-6'>Aktívna: " . ($row['active'] ? 'áno' : 'nie') . "</p>";
                                    echo "<div class='d-flex question-buttons '>";
                                        echo "<button class='btn btn-outline-secondary h6 me-1'><i class='bi bi-pen'></i> UPRAVIŤ</button>";
                                        echo "<button class='btn btn-outline-secondary h6 me-1'><i class='bi bi-copy'></i> KOPÍROVAŤ</button>";
                                        echo "<button class='btn btn-outline-secondary h6 me-1'><i class='bi bi-trash3'></i> ZMAZAŤ</button>";
                                        echo "<button class='btn btn-outline-secondary h6 me-1'><i class='bi bi-bar-chart-steps'></i> VÝSLEDKY HLASOVANIA</button>";
                                        echo "<button class='btn btn-outline-secondary h6'><i class='bi bi-door-closed'></i> UZATVORIŤ HLASOVANIE</button>";
                                    echo "</div>";
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
            </div>

        <?php else : ?>
            <h2>Neprihlásený používateľ</h2>
        <?php endif; ?>
    </div>
    

    <script>
        function filterQuestions() {
            var subject = document.getElementById('subjectFilter').value;
            var date = document.getElementById('dateFilter').value;
            var userFilter = document.getElementById('userFilter');
            var user = userFilter ? userFilter.value : '';

            var questions = document.querySelectorAll('.questions > .border');

            questions.forEach(function(question) {
                var subjectMatch = !subject || question.querySelector('.fs-6:nth-of-type(1)').textContent.includes(subject);
                var dateMatch = !date || question.querySelector('.fs-6:nth-of-type(2)').textContent.includes(date);
                var userElement = question.querySelector('.user');
                var userMatch = !user || (userElement && userElement.dataset.userId === user); 

                if (subjectMatch && dateMatch && userMatch) {
                    question.style.display = 'block';
                } else {
                    question.style.display = 'none';
                }
            });
        }

        document.getElementById('subjectFilter').addEventListener('change', filterQuestions);
        document.getElementById('dateFilter').addEventListener('change', filterQuestions);
        var userFilter = document.getElementById('userFilter');
        if (userFilter) {
            userFilter.addEventListener('change', filterQuestions);
        }

        filterQuestions();
    </script>

</body>
</html>