<?php
session_start();
require_once("../../../configFinal.php");
include_once 'translation.php';

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body class="bg-dark-custom">

    <?php include "menu.php"; ?>

    <div class="container p-1 mt-4">
        <h2 class="mb-2"><?php echo translate('SPRÁVA POUŽÍVATEĽOV'); ?></h2>
        <div class="d-flex justify-content-end"><button type="button" id="add-user" class="btn border border-secondary mb-1">+ <?php echo translate('Pridať používateľa'); ?></button></div>
        <table class="table border shadow table-striped text-center">
            <thead>
                <tr class="first-row">
                    <th scope="col">ID</th>
                    <th scope="col">LOGIN</th>
                    <th scope="col"><?php echo translate("AKCIA"); ?></th>
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
                        echo "<button type='button' class='btn btn-outline-primary btn-sm edit-button' data-bs-toggle='modal' data-bs-target='#editUserModal' data-user-id='" . $row["id"] . "' data-user-login='" . $row["login"] . "'>" . translate("UPRAVIŤ") . "</button>";
                        echo "<a href='#' class='btn btn-outline-danger btn-sm ms-1 delete-button' data-user-id='" . $row["id"] . "'>" . translate("ZMAZAŤ") . "</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>" . translate("Momentálne žiadní používatelia") . "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="userCreationModal" tabindex="-1" aria-labelledby="userCreationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h3 class="modal-title w-100" id="userCreationModalLabel"><?php echo translate('VYTVORIŤ POUŽÍVATEĽA'); ?></h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="createUser.php" method="post" class="p-0 m-0 shadow-none">
                        <div class="mb-3">
                            <label for="createUserLogin" class="form-label">Login</label>
                            <input type="text" class="form-control" id="createUserLogin" name="userLogin">
                        </div>
                        <div class="mb-3">
                            <label for="createUserPassword" class="form-label"><?php echo translate('Heslo'); ?></label>
                            <input type="password" class="form-control" id="createUserPassword" name="userPassword">
                        </div>
                        <div class="mb-3 b-0">
                            <label for="createUserRole" class="form-label"><?php echo translate('Rola'); ?></label>
                            <select class="form-select" id="createUserRole" name="userRole">
                                <option value="U">U</option>
                                <option value="A">A</option>
                            </select>
                        </div>
                        <div class="text-center">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo translate('ZATVORIŤ'); ?></button>
                            <button type="submit" class="btn btn-outline-primary"><?php echo translate('VYTVORIŤ'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="creationSuccessModal" tabindex="-1" aria-labelledby="creationSuccessModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-header">
                    <?php
                    if (isset($_SESSION['userCreationSuccess']) && $_SESSION['userCreationSuccess']) {
                        echo '<h3 class="modal-title w-100" id="creationSuccessModalLabel">ÚSPEŠNE VYTVORENÝ POUŽÍVATEĽ</h3>';
                    } else {
                        echo '<h3 class="modal-title w-100" id="creationSuccessModalLabel">POUŽÍVATEĽ NEBOL VYTVORENÝ</h3>';
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <?php
                    if (isset($_SESSION['userCreationSuccess']) && $_SESSION['userCreationSuccess']) {
                        echo "Úspešne ste vytvorili používateľa.";
                    } else {
                        if (isset($_SESSION['userCreationErrors'])) {
                            foreach ($_SESSION['userCreationErrors'] as $error) {
                                echo "<p>$error</p>";
                            }

                            unset($_SESSION['userCreationErrors']);
                        }
                        else{
                            echo "Pri vytváraní používateľa nastala chyba.";
                        }
                    }
                    ?>
                </div>
                <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-center">
                <h3 class="modal-title w-100" id="confirmDeleteModalLabel"><?php echo translate("POTVRĎTE VYMAZANIE"); ?></h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <?php echo translate("Naozaj chcete vymazať tohto používateľa?"); ?>
                </div>
                <div class="modal-footer justify-content-center">
                    <form action="deleteUser.php" method="post" class="p-0 m-0">
                        <input type="hidden" name="userId" id="deleteUserId">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo translate("ZRUŠIŤ"); ?></button>
                        <button type="submit" class="btn btn-outline-danger"><?php echo translate("ZMAZAŤ"); ?></button>
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
                        echo '<h3 class="modal-title w-100" id="deletionSuccessModalLabel"><?php echo translate("ÚSPEŠNE VYMAZANÉ"); ?></h3>';
                    } else {
                        echo '<h3 class="modal-title w-100" id="deletionSuccessModalLabel"><?php echo translate("VYMAZANIE NEBOLO ÚSPEŠNÉ"); ?></h3>';
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <?php
                    if (isset($_SESSION['deletionSuccess']) && $_SESSION['deletionSuccess']) {
                        echo translate("Uspešne ste odstránili používateľa.");
                    } else {
                        echo translate("Pri odstráňovaní používateľa nastala chyba.");
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
                    <?php
                    if (isset($_SESSION['roleUpdateSuccess']) && $_SESSION['roleUpdateSuccess']) {
                        echo "<h3 class='modal-title w-100' id='roleUpdateSuccessModalLabel'> ". translate("ÚSPEŠNE ZMENENÉ") . "</h3>";
                    } else {
                        echo "<h3 class='modal-title w-100' id='roleUpdateSuccessModalLabel'> ". translate("ZMENA NEBOLA ÚSPEŠNÁ") . "</h3>";
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <?php
                    if (isset($_SESSION['roleUpdateSuccess']) && $_SESSION['roleUpdateSuccess']) {
                        echo translate("Uspešne ste zmenili používateľa."); 
                    } else {
                        if (isset($_SESSION['roleUpdateErrors'])) {
                            foreach ($_SESSION['roleUpdateErrors'] as $error) {
                                echo "<p>$error</p>";
                            }

                            unset($_SESSION['roleUpdateErrors']);
                        }
                        else{
                            echo translate("Pri menení informácií o používateľovi nastala chyba.");
                        }
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
                    <h3 class="modal-title w-100" id="editUserModalLabel"><?php echo translate("UPRAVIŤ POUŽÍVATEĽA"); ?></h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="editUser.php" method="post" class="p-0 m-0 shadow-none">
                        <input type="hidden" id="editUserId" name="userId">
                        <div class="mb-3">
                            <label for="editUserLogin" class="form-label">Login</label>
                            <input type="text" class="form-control" id="editUserLogin" name="userLogin" placeholder=<?php echo translate('Nový login'); ?>>
                        </div>
                        <div class="mb-3">
                            <label for="editUserPassword" class="form-label"><?php echo translate("Heslo"); ?></label>
                            <input type="password" class="form-control" id="editUserPassword" name="userPassword" placeholder=>
                        </div>
                        <div class="mb-3 b-0">
                            <label for="editUserRole" class="form-label"><?php echo translate("Rola"); ?></label>
                            <select class="form-select" id="editUserRole" name="userRole">
                                <option value="U">U</option>
                                <option value="A">A</option>
                            </select>
                        </div>
                        <div class="text-center">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo translate("ZATVORIŤ"); ?></button>
                            <button type="submit" class="btn btn-outline-primary"><?php echo translate("ULOŽIŤ"); ?></button>
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
                    document.getElementById('editUserLogin').value = userLogin;
                    document.getElementById('editUserPassword').value = '';
                });
            });

            document.getElementById('add-user').addEventListener('click', function(){
                const userCreationModal = new bootstrap.Modal(document.getElementById('userCreationModal'));
                userCreationModal.show();
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

        <?php if (isset($_SESSION['userCreationSuccess'])): ?>
            const roleUpdateSuccessModal = new bootstrap.Modal(document.getElementById('creationSuccessModal'));
            roleUpdateSuccessModal.show();
            <?php unset($_SESSION['userCreationSuccess']); ?>
        <?php endif; ?>

    </script>

</body>
</html>