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
                    <th scope="col">ID</th>
                    <th scope="col">LOGIN</th>
                    <th scope="col">AKCIA</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr class='other-row'>";
                        echo "<td class='col-lg-2'>" . $row["id"] . "</td>";
                        echo "<td class='col-lg-6'>" . $row["login"] . "</td>";
                        echo "<td class='col-lg-4'>";
                        echo "<button type='button' class='btn btn-outline-primary btn-sm edit-button' data-bs-toggle='modal' data-bs-target='#editUserModal' data-user-id='" . $row["id"] . "' data-user-login='" . $row["login"] . "'>UPRAVIŤ</button>";
                        echo "<a href='#' class='btn btn-outline-danger btn-sm ms-1 delete-button' data-user-id='" . $row["id"] . "'>ZMAZAŤ</a>";
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

    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h3 class="modal-title w-100" id="confirmDeleteModalLabel">POTVRĎTE VYMAZANIE</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    Naozaj chcete vymazať tohto používateľa?
                </div>
                <div class="modal-footer justify-content-center">
                    <form action="deleteUser.php" method="post" class="p-0 m-0">
                        <input type="hidden" name="userId" id="deleteUserId">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ZRUŠIŤ</button>
                        <button type="submit" class="btn btn-outline-danger">ZMAZAŤ</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deletionSuccessModal" tabindex="-1" aria-labelledby="deletionSuccessModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-header">
                    <h3 class="modal-title w-100" id="deletionSuccessModalLabel">ÚSPEŠNE VYMAZANÉ</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <?php
                    if (isset($_SESSION['deletionSuccess']) && $_SESSION['deletionSuccess']) {
                        echo "Uspešne ste odstránili používateľa.";
                    } else {
                        echo "Pri odstráňovaní používateľa nastala chyba.";
                    }
                    ?>
                </div>
                <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="roleUpdateSuccessModal" tabindex="-1" aria-labelledby="roleUpdateSuccessModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-header">
                    <h3 class="modal-title w-100" id="roleUpdateSuccessModalLabel">ÚSPEŠNE ZMENENÉ</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <?php
                    if (isset($_SESSION['roleUpdateSuccess']) && $_SESSION['roleUpdateSuccess']) {
                        echo "Uspešne ste zmenili používateľa.";
                    } else {
                        echo "Pri menení informácií o používateľovi nastala chyba.";
                    }
                    ?>
                </div>
                <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h3 class="modal-title w-100" id="editUserModalLabel">UPRAVIŤ POUŽÍVATEĽA</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="editUser.php" method="post" class="p-0 m-0 shadow-none">
                        <input type="hidden" id="editUserId" name="userId">
                        <div class="mb-3 b-0">
                            <label for="editUserRole" class="form-label">ROLA</label>
                            <select class="form-select" id="editUserRole" name="userRole">
                                <option value="U">U</option>
                                <option value="A">A</option>
                            </select>
                        </div>
                        <div class="text-center">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ZATVORIŤ</button>
                            <button type="submit" class="btn btn-outline-primary">ULOŽIŤ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const deleteButtons = document.querySelectorAll('.delete-button');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-user-id');
                    document.getElementById('deleteUserId').value = userId;
                    const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
                    modal.show();
                });
            });

            const editButtons = document.querySelectorAll('.edit-button');
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-user-id');
                    const userLogin = this.getAttribute('data-user-login');
                    document.getElementById('editUserId').value = userId;
                });
            });
        });

        <?php if (isset($_SESSION['deletionSuccess']) && $_SESSION['deletionSuccess']): ?>
            const deletionSuccessModal = new bootstrap.Modal(document.getElementById('deletionSuccessModal'));
            deletionSuccessModal.show();
            <?php unset($_SESSION['deletionSuccess']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['roleUpdateSuccess'])): ?>
            const roleUpdateSuccessModal = new bootstrap.Modal(document.getElementById('roleUpdateSuccessModal'));
            roleUpdateSuccessModal.show();
            <?php unset($_SESSION['roleUpdateSuccess']); ?>
        <?php endif; ?>

    </script>

</body>
</html>