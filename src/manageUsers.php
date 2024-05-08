<?php
session_start();
require_once("../../../configFinal.php");

if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && $_SESSION['role'] === 'A')) {
    header("Location: index.php");
    exit;
}

$sql = "SELECT * FROM users WHERE role = 'U'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body>

    <?php include "menu.php"; ?>

    <div class="container border p-1 mt-4 shadow">
        <h1 class="mb-4">SPRÁVA POUŽÍVATEĽOV</h1>
        <table class="table border shadow table-striped text-center">
            <thead>
                <tr class="first-row">
                    <th>ID</th>
                    <th>LOGIN</th>
                    <th>AKCIA</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr class='other-row'>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["login"] . "</td>";
                        echo "<td>";
                        echo "<a href='updateUser.php?id=" . $row["id"] . "' class='btn btn-outline-primary btn-sm'>UPRAVIŤ</a>";
                        echo "<a href='deleteUser.php?id=" . $row["id"] . "' class='btn btn-outline-danger btn-sm ms-1'>VYMAZAŤ</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Momentálne žiadní používatelia</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</body>