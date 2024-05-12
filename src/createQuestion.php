<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once 'translation.php';
if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && ($_SESSION['role'] === 'A' || $_SESSION['role'] === 'U'))) {
    header("Location: index.php");
    exit;
}

require_once("../../../configFinal.php");

function generateRandomCode() {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';
    for ($i = 0; $i < 5; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $code;
}

$errors = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['question-type'] === 'list') {
        if (empty($_POST['question'])) {
            $errors[] = translate("Popis otázky je povinný.");
        }
        $answerFields = array_filter($_POST, function($key) {
            return strpos($key, 'answer') !== false;
        }, ARRAY_FILTER_USE_KEY);

        foreach ($answerFields as $key => $value) {
            if (empty($value)) {
                $errors[] = translate("Možná odpoveď ") . substr($key, 6) . translate(" je povinná.");
            }
        }
    } elseif ($_POST['question-type'] === 'text') {
        if (empty($_POST['question_open'])) {
            $errors[] = translate("Popis otázky je povinný.");
        }
    }

    if (empty($errors)) {
        $description = ($_POST['question-type'] === 'list') ? $_POST['question'] : $_POST['question_open'];
        $subject = ($_POST['question-type'] === 'list') ? $_POST['question-subject'] : $_POST['question_open-subject'];
        $active = isset($_POST['active-checkbox']) ? true : false;
        $type = $_POST['question-type'];
        $user_fk = $_SESSION['id'];
        if ($_POST['question-type'] === 'list') {
            $active = isset($_POST['active-checkbox']) ? true : false;
        } elseif ($_POST['question-type'] === 'text') {
            $active = isset($_POST['active-checkbox2']) ? true : false;
        }

        $code = '';
        do {
            $code = generateRandomCode();
            $check_query = "SELECT COUNT(*) as count FROM questions WHERE code = ?";
            $check_stmt = mysqli_prepare($conn, $check_query);
            mysqli_stmt_bind_param($check_stmt, "s", $code);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_bind_result($check_stmt, $count);
            mysqli_stmt_fetch($check_stmt);
            mysqli_stmt_close($check_stmt);
        } while ($count > 0);

        $sql = "INSERT INTO questions (description, type, user_fk, subject, date_created, active, code) VALUES (?, ?, ?, ?, NOW(), ?,?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssis", $description,  $type, $user_fk, $subject, $active,$code);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $question_id = mysqli_insert_id($conn);

        if ($type === 'list') {
            $answerFields = array_filter($_POST, function($key) {
                return strpos($key, 'answer') !== false;
            }, ARRAY_FILTER_USE_KEY);
    
            foreach ($answerFields as $key => $value) {
                $answer_description = $value;
                $votes = 0;
    
                $sql = "INSERT INTO answers (description, votes, question_fk) VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "ssi", $answer_description, $votes, $question_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }

        mysqli_close($conn);
        $_SESSION['questionCreatedSuccess'] = true;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Question</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body class="bg-dark-custom">
<?php include "menu.php"; ?>
<div class="out-cont">
    <h2><?php echo mb_strtoupper(translate('Vytvorenie novej otázky'), 'UTF-8'); ?></h2>
    <form id="question-form" method="post" class="mt-4">

        <div class="radio-buttons">
            <label><input type="radio" name="question-type" value="list" checked> <?php echo translate('Otázka s výberom odpovede'); ?></label>
            <label><input type="radio" name="question-type" value="text"> <?php echo translate('Otázka s otvorenou odpoveďou'); ?></label>
        </div>

        <hr>

        <div class="list-question" style="display:none;">
            <div class="detail">
                <label for="question"><?php echo translate('Popis otázky'); ?>:</label>
                <input type="text" id="question" name="question">
            </div>
            <div class="detail">
                <label for="question-subject"><?php echo translate('Predmet'); ?>:</label>
                <input type="text" id="question-subject" name="question-subject">
            </div>
            <div id="answers-container">
                <div class="detail">
                    <label for="answer1"><?php echo translate('Možná odpoveď'); ?> 1:</label>
                    <input type="text" id="answer1" name="answer1">
                </div>
                <div class="detail">
                    <label for="answer2"><?php echo translate('Možná odpoveď'); ?> 2:</label>
                    <input type="text" id="answer2" name="answer2">
                </div>

            </div>
            <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" value="0" id="active-checkbox" name="active-checkbox" checked>
                    <label class="form-check-label" for="active-checkbox">
                        <?php echo translate('Aktívna otázka'); ?>
                    </label>
                </div>
                <button type="button" id="add-answer" class="btn border border-secondary">+ <?php echo translate('Pridať možnú odpoveď'); ?></button>
                <button type="button" id="minus-answer" style="display: none;" class="btn border border-secondary">- <?php echo translate('Odobrať možnú odpoveď'); ?></button>
        </div>

        <div class="text-question" style="display:none;">
            <div class="detail">
                <label for="question_open"><?php echo translate('Popis otázky'); ?>:</label>
                <input type="text" id="question_open" name="question_open">
            </div>
            <div class="detail">
                <label for="question_open-subject"><?php echo translate('Predmet'); ?>:</label>
                <input type="text" id="question_open-subject" name="question_open-subject">
            </div>
            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" value="0" id="active-checkbox2" name="active-checkbox2" checked>
                <label class="form-check-label" for="active-checkbox2">
                    <?php echo translate('Aktívna otázka'); ?>
                </label>
            </div>
        </div>

        <div class="buttons">
        <input type="submit" value="<?php echo translate('Vytvoriť otázku'); ?>">

        </div>
        <?php
        if (!empty($errors)) {
            echo '<div class="error">' . implode("<br>", $errors) . '</div>';
        }
        ?>

    </form>
</div>

<div class="modal fade" id="questionCreatedSuccessModal" tabindex="-1" aria-labelledby="questionCreatedSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h3 class="modal-title w-100" id="questionCreatedSuccessModalLabel"><?php echo translate('OTÁZKA ÚSPEŠNE VYTVORENÁ'); ?></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <?php echo translate('Úspešne ste vytvorili novú otázku.'); ?>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<script>
    var currentLanguage = "sk"
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (isset($_SESSION['questionCreatedSuccess']) && $_SESSION['questionCreatedSuccess'] === true) : ?>
            var registrationSuccessModal = new bootstrap.Modal(document.getElementById('questionCreatedSuccessModal'));
            registrationSuccessModal.show();
            <?php unset($_SESSION['questionCreatedSuccess']); ?>
        <?php endif; ?>
        const radioButtons = document.querySelectorAll('input[name="question-type"]');
        const listQuestionSection = document.querySelector('.list-question');
        const textQuestionSection = document.querySelector('.text-question');
        const form = document.getElementById('question-form');
        const errorDiv = document.querySelector('.error');

        form.addEventListener('submit', function(event) {
            event.preventDefault();
            if (validateForm()) {
                form.submit();
                const errors = document.querySelector('.error');
                if (errors && this.value !== prevRadioValue) {
                    errors.innerHTML = '';
                } 
            }
        });

        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    currentLanguage = xhr.responseText;
                    console.log(currentLanguage);
                } else {
                    console.error('Chyba pri získavaní aktuálneho jazyka');
                }
            }
        };
        xhr.open('GET', 'get_current_language.php', true); // Zmena na správnu cestu k vášmu skriptu
        xhr.send();

        function validateForm() {
            const questionType = document.querySelector('input[name="question-type"]:checked').value;
            const errors = [];

            if (questionType === 'list') {
                const questionInput = document.getElementById('question');
                if (!questionInput || !questionInput.value.trim()) {
                    if (currentLanguage === "en") {
                        errors.push("Question description is required");
                    } else {
                        errors.push("Popis otázky je povinný.");
                    }
                }

                const questionSubjectInput = document.getElementById('question-subject');
                if (!questionSubjectInput || !questionSubjectInput.value.trim()) {
                    if (currentLanguage === "en") {
                        errors.push("Subject of answer is required.");
                    } else {
                        errors.push("Predmet otázky je povinný.");
                    }
                }

                const answerInputs = document.querySelectorAll('input[id^="answer"]');
                for (let i = 0; i < answerInputs.length; i++) {
                    if (!answerInputs[i].value.trim()) {
                        if (currentLanguage === "en") {
                            errors.push(`Possible answer ${i + 1} is required.`);
                        } else {
                            errors.push(`Možná odpoveď ${i + 1} je povinná.`);
                        }
                        
                    }
                }
            } else if (questionType === 'text') {
                const questionOpenInput = document.getElementById('question_open');
                if (!questionOpenInput || !questionOpenInput.value.trim()) {
                    if (currentLanguage === "en") {
                        errors.push("Question description is required");
                    } else {
                        errors.push("Popis otázky je povinný.");
                    }
                }
                const questionSubjectInput = document.getElementById('question_open-subject');
                if (!questionSubjectInput || !questionSubjectInput.value.trim()) {
                    if (currentLanguage === "en") {
                        errors.push("Subject of answer is required.");
                    } else {
                        errors.push("Predmet otázky je povinný.");
                    }
                }
            }

            if (errors.length > 0) {
                displayError(errors.join("<br>"));
                return false;
            }

            return true;
        }

        function displayError(message) {
            let errorDiv = document.querySelector('.error');
            if (!errorDiv) {
                errorDiv = document.createElement('div');
                errorDiv.classList.add('error');
                document.querySelector('.out-cont').appendChild(errorDiv);
            }
            errorDiv.innerHTML = message;
        }

        let prevRadioValue = 'list';
        function toggleFormSections() {
            if (this.value === 'list') {
                listQuestionSection.style.display = 'block';
                textQuestionSection.style.display = 'none';
            } else if (this.value === 'text') {
                listQuestionSection.style.display = 'none';
                textQuestionSection.style.display = 'block';
            }
            const errors = document.querySelector('.error');
            if (errors && this.value !== prevRadioValue) {
                errors.innerHTML = '';
            }
            prevRadioValue = this.value;
        }
        radioButtons.forEach(button => {
            button.addEventListener('change', toggleFormSections);
        });
        toggleFormSections.call(document.querySelector('input[name="question-type"][value="list"]'));

        let answerCount = 2;
        function addAnswerField() {
            answerCount++;
            const answersContainer = document.getElementById('answers-container');
            const newDetailDiv = document.createElement('div');
            newDetailDiv.classList.add('detail');
            const newLabel = document.createElement('label');
            newLabel.setAttribute('for', 'answer' + answerCount);
            if (currentLanguage === "en") {
                newLabel.textContent = 'Possible answer ' + answerCount + ':';
            } else {
                newLabel.textContent = 'Možná odpoveď ' + answerCount + ':';
            }
            const newInput = document.createElement('input');
            newInput.setAttribute('type', 'text');
            newInput.setAttribute('id', 'answer' + answerCount);
            newInput.setAttribute('name', 'answer' + answerCount);
            newDetailDiv.appendChild(newLabel);
            newDetailDiv.appendChild(newInput);
            answersContainer.appendChild(newDetailDiv);
            updateMinusButtonVisibility();
        }

        function removeLastAnswerField() {
            const answersContainer = document.getElementById('answers-container');
            const lastAnswerField = answersContainer.lastElementChild;
            if (lastAnswerField && answerCount > 2) {
                answersContainer.removeChild(lastAnswerField);
                answerCount--;
            }
            updateMinusButtonVisibility();
        }

        function updateMinusButtonVisibility() {
            const minusButton = document.getElementById('minus-answer');
            minusButton.style.display = (answerCount > 2) ? 'inline-block' : 'none';
        }
        document.getElementById('add-answer').addEventListener('click', addAnswerField);
        document.getElementById('minus-answer').addEventListener('click', removeLastAnswerField);

    });

</script>

</body>
</html>

