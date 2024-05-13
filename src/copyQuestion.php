<?php
session_start();
require_once("../../../configFinal.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && ($_SESSION['role'] === 'A' || $_SESSION['role'] === 'U'))) {
    header("Location: index.php");
    exit;
}

function generateRandomCode() {
    global $conn;
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';
    do {
        $code = '';
        for ($i = 0; $i < 5; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }
        $check_sql = "SELECT * FROM questions WHERE code = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $code);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
    } while ($check_result->num_rows > 0);
    return $code;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['questionId'])){
        $questionId = $_POST['questionId'];
        
        $get_question_sql = "SELECT * FROM questions WHERE id = ?";
        $get_question_stmt = $conn->prepare($get_question_sql);
        $get_question_stmt->bind_param("i", $questionId);
        $get_question_stmt->execute();
        $question_result = $get_question_stmt->get_result();

        if ($question_result->num_rows > 0) {
            $question_row = $question_result->fetch_assoc();
            $description = $question_row['description'];
            $type = $question_row['type'];
            $subject = $question_row['subject'];
            $date_created = $question_row['date_created'];
            $date_closed = $question_row['date_closed'];
            $active = $question_row['active'];
            $note_closed = $question_row['note_closed'];
            $user_fk = $question_row['user_fk']; 
            $code = generateRandomCode();
            
            $insert_sql = "INSERT INTO questions (description, type, subject, date_created, date_closed, active, note_closed, user_fk, code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("sssssssis", $description, $type, $subject, $date_created, $date_closed, $active, $note_closed, $user_fk, $code);
            $insert_stmt->execute();

            if ($insert_stmt->affected_rows > 0) {
                $new_question_id = $insert_stmt->insert_id;
                
                $get_answers_sql = "SELECT * FROM answers WHERE question_fk = ?";
                $get_answers_stmt = $conn->prepare($get_answers_sql);
                $get_answers_stmt->bind_param("i", $questionId);
                $get_answers_stmt->execute();
                $answers_result = $get_answers_stmt->get_result();

                while ($answer_row = $answers_result->fetch_assoc()) {
                    $answer_description = $answer_row['description'];
                    $votes = 0;
                    $insert_answer_sql = "INSERT INTO answers (description, votes, question_fk) VALUES (?, ?, ?)";
                    $insert_answer_stmt = $conn->prepare($insert_answer_sql);
                    $insert_answer_stmt->bind_param("sii", $answer_description, $votes, $new_question_id);
                    $insert_answer_stmt->execute();
                }

                $_SESSION['copyQuestionSuccess'] = true;
            } else {
                $_SESSION['copyQuestionSuccess'] = false;
            }
        } else {
            $_SESSION['copyQuestionSuccess'] = false;
        }
    } else {
        $_SESSION['copyQuestionSuccess'] = false;
    }
}

header("Location: index.php");
exit;
?>
