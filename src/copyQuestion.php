<?php
session_start();
require_once("../../../configFinal.php");
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && ($_SESSION['role'] === 'A' || $_SESSION['role'] === 'U'))) {
    header("Location: index.php");
    exit;
}

function generateRandomCode() {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';
    for ($i = 0; $i < 5; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $code;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['questionId'])){
        $questionId = $_POST['questionId'];
        echo $questionId;
        $sql = "SELECT * FROM questions WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $questionId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $description = $row['description'];
            $type = $row['type'];
            $subject = $row['subject'];
            $date_created = $row['date_created'];
            $date_closed = $row['date_closed'];
            $active = $row['active'];
            $note_closed = $row['note_closed'];
            $user_fk = $row['user_fk']; 
            $code = generateRandomCode();
            
            $insert_sql = "INSERT INTO questions (description, type, subject, date_created, date_closed, active, note_closed, user_fk, code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("sssssssis", $description, $type, $subject, $date_created, $date_closed, $active, $note_closed, $user_fk, $code);
            $stmt->execute();

            $_SESSION['copyQuestionSuccess'] = true;
        }
    }
}
header("Location: index.php");
exit;
?>
