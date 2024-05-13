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
    <title><?php echo translate('Hlasovací systém'); ?></title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body class="bg-dark-custom">
    <?php include "menu.php"; ?>

    <div class="container-md p-1 mt-4 mb-4">
        <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) : ?>
            <h2><?php echo translate('Prihlásený ako'); ?>: <?= $_SESSION['login'] ?> <span class="text-muted">(<?= ($_SESSION['role'] === 'A' ? 'admin' : 'user') ?>)</span></h2>

            <div class="d-flex justify-content-between mt-4 filter-options">
                
                <div class="dropdown d-flex mb-2 ms-2">
                    <div class="input-group-text bg-dark-subtle"><?php echo translate('Predmet'); ?>:</div>
                    <select class="form-select" id="subjectFilter" aria-label="select1">
                        <option value=""><?php echo translate('Všetky'); ?></option>
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
                    <div class="input-group-text bg-dark-subtle"><?php echo translate('Dátum vytvorenia'); ?>:</div>
                    <select class="form-select" id="dateFilter" aria-label="select2">
                        <option value=""><?php echo translate('Všetky'); ?></option>
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
                        echo "<div class='input-group-text bg-dark-subtle'>" . translate('Používateľ') . ": </div>";
                        echo '<select class="form-select" id="userFilter" aria-label="select3">';
                        echo "<option value=''>" . translate('Všetci') . "</option>";

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

            
            <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header text-center">
                            <h3 class="modal-title w-100" id="confirmDeleteModalLabel"><?php echo translate('POTVRĎTE VYMAZANIE'); ?></h3>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <?php echo translate('Naozaj chcete vymazať túto otázku?'); ?>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <form action="deleteQuestion.php" method="post" class="p-0 m-0">
                                <input type="hidden" name="questionId" id="deleteQuestionId">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo translate('ZRUŠIŤ'); ?></button>
                                <button type="submit" class="btn btn-outline-danger"><?php echo translate('ZMAZAŤ'); ?></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="deletionSuccessModal" tabindex="-1" aria-labelledby="deletionSuccessModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content text-center">
                        <div class="modal-header">
                            <?php
                            if (isset($_SESSION['deletionSuccess']) && $_SESSION['deletionSuccess']) {
                                echo '<h3 class="modal-title w-100" id="deletionSuccessModalLabel">'.translate('ÚSPEŠNE VYMAZANÉ').'</h3>';
                            } else {
                                echo '<h3 class="modal-title w-100" id="deletionSuccessModalLabel">'.translate('VYMAZANIE NEBOLO ÚSPEŠNÉ').'</h3>';
                            }
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <?php
                            if (isset($_SESSION['deletionSuccess']) && $_SESSION['deletionSuccess']) {
                                echo translate("Úspešne ste odstránili otázku");
                            } else {
                                echo translate("Pri odstráňovaní otázky nastala chyba.");
                            }
                            ?>
                        </div>
                        <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">OK</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="copyQuestionSuccessModal" tabindex="-1" aria-labelledby="copyQuestionSuccessModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content text-center">
                        <div class="modal-header">
                            <?php
                            if (isset($_SESSION['copyQuestionSuccess']) && $_SESSION['copyQuestionSuccess']) {
                                echo '<h3 class="modal-title w-100" id="copyQuestionSuccessModalLabel">'.translate('ÚSPEŠNE SKOPÍROVANÉ').'</h3>';
                            } else {
                                echo '<h3 class="modal-title w-100" id="copyQuestionSuccessModalLabel">'.translate('KOPÍROVANIE NEBOLO ÚSPEŠNÉ').'</h3>';
                            }
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <?php
                            if (isset($_SESSION['copyQuestionSuccess']) && $_SESSION['copyQuestionSuccess']) {
                                echo translate("Úspešne ste skopírovali otázku.");
                            } else {
                                echo translate("Pri kopírovaní otázky nastala chyba.");
                            }
                            ?>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">OK</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="copyQuestionSuccessModal" tabindex="-1" aria-labelledby="copyQuestionSuccessModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content text-center">
                        <div class="modal-header">
                            <?php
                            if (isset($_SESSION['copyQuestionSuccess']) && $_SESSION['copyQuestionSuccess']) {
                                echo '<h3 class="modal-title w-100" id="copyQuestionSuccessModalLabel">'.translate('ÚSPEŠNE ZKOPIROVANÉ').'</h3>';
                            } else {
                                echo '<h3 class="modal-title w-100" id="copyQuestionSuccessModalLabel">'.translate('KOPÍROVANIE NEBOLO ÚSPEŠNÉ').'</h3>';
                            }
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <?php
                            if (isset($_SESSION['copyQuestionSuccess']) && $_SESSION['copyQuestionSuccess']) {
                                echo translate("Úspešne ste skopírovali otázku.");
                            } else {
                                echo translate("Pri kopírovaní otázky nastala chyba.");
                            }
                            ?>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">OK</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="questionResultsModal" tabindex="-1" aria-labelledby="questionResultsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content text-center">
                        <div class="modal-header">
                            <h3 class="modal-title w-100" id="deletionSuccessModalLabel"><?php echo translate("VÝSLEDKY HLASOVANIA"); ?></h3>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <?php
                            if (isset($_SESSION['questioResults'])) {
                                echo $_SESSION['questioResults'];
                            } else {
                                echo translate("Pri odstráňovaní otázky nastala chyba.");
                            }
                            ?>
                        </div>
                        <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">OK</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="codeModal" tabindex="-1" aria-labelledby="codeModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header text-center">
                            <h3 class="modal-title w-100" id="codeModalLabel"><?php echo translate('KÓD PRE OTÁZKU'); ?></h3>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center" id="show-qr">
                            <h2 id="codeModalBody"></h2>
                            
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo translate('ZRUŠIŤ'); ?></button>
                        </div>
                    </div>
                </div>
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
    
                            echo "<h3>" . translate('Otázka č.') . " " . $questionNumber;

                                if ($row['type'] === 'list') {
                                    echo "<span class='fs-5 text-muted'> (" . translate('otázka s výberom správnej odpovede') . ")</span>";
                                } else {
                                    echo "<span class='fs-5 text-muted'> (" . translate('otázka s voľnou odpoveďou') . ")</span>";
                                }
                                echo "</h3>";

                                if ($_SESSION['role'] === 'A' && $row['user_fk'] !== $_SESSION['id']) {
                                    echo "<p class='fs-5 text-muted user' data-user-id='" . $row['user_fk'] . "'>- " . translate('od používateľa') . " " . getUsername($row['user_fk'], $conn) . "</p>";
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
                                echo "<div class='d-flex three-buttons'>";
                                echo "<button class='btn btn-secondary mb-2 me-1' type='button' data-bs-toggle='collapse' data-bs-target='#$collapseId' aria-expanded='false' aria-controls='$collapseId'><i class='bi bi-chevron-down'></i> " . translate('ROZBALIŤ') . "</button>";
                                echo "<button class='btn btn-secondary mb-2 me-1' type='button' data-bs-toggle='modal' data-bs-target='#codeModal' data-question-id='" . $row['code'] . "'><i class='bi bi-unlock'></i> " . translate('UKÁŽ KÓD') . "</button>";
                                echo '<form method="post" action="generateJson.php" class="p-0 m-0 me-1 bg-body shadow-none">';
                                    echo '<input type="hidden" name="question_id" value="' . $row['id'] . '">';
                                    echo '<button type="submit" name="generate_json" class="btn btn-secondary mb-2 w-100"><i class="bi bi-filetype-json"></i> JSON EXPORT</button>';
                                echo '</form>';
                                echo "</div>";

                                echo "<div class='collapse' id='$collapseId'>";
                                    echo "<p class='fs-6 mb-1'>" . translate('Predmet') . ": " . $row['subject'] . "</p>";
                                    echo "<p class='fs-6 mb-1'>" . translate('Dátum vytvorenia') . ": " . $row['date_created'] . "</p>";
                                    echo "<p class='fs-6'>" . translate('Aktívna') . ": " . ($row['active'] ? translate('áno') : translate('nie')) . "</p>";
                                    echo "<div class='d-flex question-buttons '>";
                                        echo "<button class='btn btn-outline-secondary h6 me-1'><i class='bi bi-pen'></i> " . translate('UPRAVIŤ') . "</button>";
                                        echo "<form action='copyQuestion.php' method='post' class='p-0 m-0 me-1 bg-body shadow-none'>";
                                            echo "<input type='hidden' name='questionId' id='deleteQuestionId' value='" . $row['id'] . "'>";
                                            echo "<button type= 'submit' class='btn btn-outline-secondary h6 w-100'><i class='bi bi-copy'></i> " . translate('KOPÍROVAŤ') . "</button>";
                                        echo "</form>";
                                        echo "<button class='btn btn-outline-secondary h6 me-1' onclick='deleteQuestion(".$row['id'].")'><i class='bi bi-trash3'></i> " . translate('ZMAZAŤ') . "</button>";
                                        echo "<form action='getQuestionResults.php' method='POST'>";
                                            echo "<input type='hidden' name='questionId' id='questionIdField' value='".$row['id']."'>";
                                            echo "<button type='submit' class='btn btn-outline-secondary h6 me-1'><i class='bi bi-bar-chart-steps'></i> " . translate('VÝSLEDKY HLASOVANIA') . "</button>";
                                        echo "</form>";
                                        echo "<button class='btn btn-outline-secondary h6'><i class='bi bi-door-closed'></i> " . translate('UZATVORIŤ HLASOVANIE') . "</button>";
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
        <h2 class="mb-5"><?php echo translate('Neprihlásený používateľ'); ?></h2>
        <form id="codeForm" method="GET" onsubmit="submitForm()">
            <div class="list-question">
                <div class="detail">
                    <h2><?php echo translate('Zadaj vstupný kód:'); ?></h2>
                    <label><input type="text" id="code" name="code" maxlength="5"
                                  class="form-control text-center"></label>
                </div>
                <div class="buttons">
                    <button type="submit" class="btn btn-outline-secondary"><?php echo translate('POTVRDIŤ'); ?></button>
                </div>
            </div>
        </form>
        <script>
            function submitForm() {
                var code = document.getElementById("code").value;
                document.getElementById("codeForm").action = "question/" + code;
            }
        </script>
        <?php endif; ?>
    </div>
    

    <script>

        const codeModal = document.getElementById('codeModal');
        const codeModalBody = document.getElementById('codeModalBody');

        codeModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const questionId = button.getAttribute('data-question-id');
            codeModalBody.textContent = questionId;
            const existingImg = document.querySelector('#show-qr img');
            if (!existingImg) {
                const img = document.createElement('img');
                img.src = 'codes/' + questionId + '.png';
                document.getElementById('show-qr').appendChild(img);
            }
        });

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

        function deleteQuestion(questionId){
            document.getElementById('deleteQuestionId').value = questionId;
            const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
            modal.show();
        }


        <?php if (isset($_SESSION['deletionSuccess']) && $_SESSION['deletionSuccess']): ?>
            const deletionSuccessModal = new bootstrap.Modal(document.getElementById('deletionSuccessModal'));
            deletionSuccessModal.show();
            <?php unset($_SESSION['deletionSuccess']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['copyQuestionSuccess']) && $_SESSION['copyQuestionSuccess']): ?>
            const copySuccessModal = new bootstrap.Modal(document.getElementById('copyQuestionSuccessModal'));
            copySuccessModal.show();
            <?php unset($_SESSION['copyQuestionSuccess']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['questioResults']) && $_SESSION['questioResults']): ?>
            const questioResultsModal = new bootstrap.Modal(document.getElementById('questionResultsModal'));
            questioResultsModal.show();
            <?php unset($_SESSION['questioResults']); ?>
        <?php endif; ?>


        filterQuestions();
    </script>

</body>
</html>