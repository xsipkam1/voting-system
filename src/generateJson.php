<?php
session_start();
require_once("../../../configFinal.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && ($_SESSION['role'] === 'A' || $_SESSION['role'] === 'U'))) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['generate_json'])) {
    $question_id = $_POST['question_id'];

    $question_sql = "SELECT * FROM questions WHERE id = $question_id";
    $question_result = mysqli_query($conn, $question_sql);
    $question_row = mysqli_fetch_assoc($question_result);

    $question_data = array(
        'question' => array(
            'id' => $question_row['id'],
            'description' => $question_row['description'],
            'type' => $question_row['type'],
            'date_created' => $question_row['date_created'],
            'date_closed' => $question_row['date_closed'],
            'active' => $question_row['active'],
            'note_closed' => $question_row['note_closed'],
            'code' => $question_row['code']
        ),
        'answers' => array()
    );

    $answers_sql = "SELECT * FROM answers WHERE question_fk = $question_id";
    $answers_result = mysqli_query($conn, $answers_sql);

    if (mysqli_num_rows($answers_result) > 0) {
        while ($answer_row = mysqli_fetch_assoc($answers_result)) {
            $question_data['answers'][] = array(
                'id' => $answer_row['id'],
                'description' => $answer_row['description'],
                'votes' => $answer_row['votes']
            );
        }
    }

    $json_data = json_encode($question_data);

    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="question_' . $question_id . '.json"');

    echo $json_data;
    exit;
}
?>
