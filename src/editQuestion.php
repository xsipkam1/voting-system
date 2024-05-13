<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once("../../../configFinal.php");

if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && $_SESSION['role'] === 'A')) {
    header("Location: index.php");
    exit;
}

if(isset($_POST['getData'])) {
    $questionId = $_POST['questionId'];

    $sql = "SELECT * FROM questions WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $questionId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $description = $row['description'];
        $subject = $row['subject'];
        $type = $row['type'];


        $answers = array();
        if ($type === "list") {
            
            $answers_sql = "SELECT * FROM answers WHERE question_fk = ?";
            $answers_stmt = $conn->prepare($answers_sql);
            $answers_stmt->bind_param("i", $questionId);
            $answers_stmt->execute();
            $answers_result = $answers_stmt->get_result();
            while ($answer_row = $answers_result->fetch_assoc()) {
                $answers[] = $answer_row['description'];
            }
        }

        $response = array(
            'id' => $questionId,
            'description' => $description,
            'subject' => $subject,
            'type' => $type,
            'answers' => $answers
        );

        echo json_encode($response);
    }
} else {
    if(isset($_POST['questionId'])){
        $questionId = $_POST['questionId'];
    }

    $editDescription = $_POST['editDescription'];
    $editSubject = $_POST['editSubject'];
    $editAnswers = array();

    $i = 1;
    while (isset($_POST['editAnswer' . $i])) {
        $editAnswers[] = $_POST['editAnswer' . $i];
        $i++;
    }

    $sql = "UPDATE questions SET description = ?, subject = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $editDescription, $editSubject, $questionId);
    $stmt->execute();

    if ($i > 1) {
        $deleteAnswersSql = "DELETE FROM answers WHERE question_fk = ?";
        $deleteAnswersStmt = $conn->prepare($deleteAnswersSql);
        $deleteAnswersStmt->bind_param("i", $questionId);
        $deleteAnswersStmt->execute();

        $insertAnswerSql = "INSERT INTO answers (description, votes, question_fk) VALUES (?, ?, ?)";
        $insertAnswerStmt = $conn->prepare($insertAnswerSql);
        $votes = 0;
        foreach ($editAnswers as $answer) {
            $insertAnswerStmt->bind_param("sii", $answer, $votes, $questionId);
            $insertAnswerStmt->execute();
        }
    }

    $_SESSION['editQuestionSuccess'] = true;
    header("Location: index.php");
    exit;
}
?>
