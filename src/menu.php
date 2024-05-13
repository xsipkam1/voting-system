<?php
include_once 'translation.php';
if (isset($_GET['lang'])) {
    $_SESSION['currentLanguage'] = $_GET['lang'];
}
$currentLanguage = isset($_SESSION['currentLanguage']) ? $_SESSION['currentLanguage'] : "sk";
?>

<div id="menu">
    <header>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark p-0">
            <div class="container-fluid">
                
                <li class="nav-item">
                    <a href="index.php" class="nav-link active" aria-current="page" id="domov-link"><i class="bi bi-house"></i> </a>
                </li>

                <li class="nav-item">
                    <a <?php echo ($currentLanguage == 'sk') ? 'class="nav-link inactive"' : 'class="nav-link"'; ?> href="?lang=sk"><img src="https://www.geonames.org/flags/x/sk.gif" alt="Slovensky" style="width: 20px; height: auto;"></a>
                    <a <?php echo ($currentLanguage == 'en') ? 'class="nav-link inactive"' : 'class="nav-link"'; ?> href="?lang=en"><img src="https://www.geonames.org/flags/x/gb.gif" alt="English" style="width: 20px; height: auto;"></a>
                </li>
                <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-label="Expand">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="nav">
                    <ul class="navbar-nav ms-auto">
                        <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) : ?>
                            <?php if ($_SESSION['role'] === 'A') : ?>
                                <li class="nav-item">
                                    <a href="manageUsers.php" class="nav-link"> <i class="bi bi-people"></i> <?php echo translate('Používatelia'); ?></a>
                                </li>
                            <?php endif; ?>
                            <li class="nav-item">
                                <a href="#" class="nav-link" id="settings-link"> <i class="bi bi-gear"></i> <?php echo translate('Nastavenia'); ?></a>
                            </li>
                            <li class="nav-item">
                                <a href="logout.php" class="nav-link"> <i class="bi bi-box-arrow-right"></i> <?php echo translate('Odhlásenie'); ?></a>
                            </li>
                        <?php else : ?>
                            <li class="nav-item">
                                <a href="login.php" class="nav-link"><i class="bi bi-door-open"></i> <?php echo translate('Prihlásenie'); ?></a>
                            </li>
                            <li class="nav-item">
                                <a href="register.php" class="nav-link"><i class="bi bi-person-plus"></i> <?php echo translate('Registrácia'); ?></a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>

            </div>
        </nav>
    </header>
</div>

<style>
    .inactive {
        pointer-events: none;
    }
    .inactive img {
        filter: brightness(120%);
    }
    .nav-link:not(.inactive) img {
        filter: brightness(70%);
    }
</style>

<div class="modal fade" id="settingsModal" tabindex="-1" aria-labelledby="settingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h3 class="modal-title w-100" id="codeModalLabel"><?php echo translate('NASTAVENIA'); ?></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="accordion" id="accordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                <i class="bi bi-person-lock me-2"></i>  <?php echo translate('ZMENIŤ HESLO'); ?>
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordion">
                            <div class="accordion-body">
                                <form action="changePassword.php" method="post" class="shadow-none m-0 p-0">

                                    <div class="detail">
                                        <label for="password"><?php echo translate("Nové heslo"); ?>:</label>
                                        <input type="password" id="password" name="password">
                                    </div>
                                    <div class="detail">
                                        <label for="repeat_password"><?php echo translate("Zopakovať heslo"); ?>:</label>
                                        <input type="password" id="repeat_password" name="repeat_password">
                                    </div>
                                    <div class="buttons">
                                        <input type="submit" value="<?php echo translate('ZMENIŤ'); ?>">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo translate('ZATVORIŤ'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="passwordUpdateSuccessModal" tabindex="-1" aria-labelledby="passwordUpdateSuccessModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-header">
                <?php
                    if (isset($_SESSION['passwordChangedSuccess']) && $_SESSION['passwordChangedSuccess']) {
                        echo "<h3 class='modal-title w-100' id='passwordUpdateSuccessLabel'> ". translate("ÚSPEŠNE ZMENENÉ") . "</h3>";
                    } else {
                        echo "<h3 class='modal-title w-100' id='passwordUpdateSuccessLabel'> ". translate("ZMENA NEBOLA ÚSPEŠNÁ") . "</h3>";
                    }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <?php
                if (isset($_SESSION['passwordChangedSuccess']) && $_SESSION['passwordChangedSuccess']) {
                    echo translate("Uspešne ste zmenili svoje heslo."); 
                } else {
                    echo translate("Pri menení hesla nastala chyba.");
                }
                ?>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="passwordUpdateErrorModal" tabindex="-1" aria-labelledby="passwordUpdateErrorModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-header">
                <?php
                    echo "<h3 class='modal-title w-100' id='passwordUpdateSuccessLabel'> ". translate("ZMENA NEBOLA ÚSPEŠNÁ") . "</h3>";
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <?php
                 if (isset($_SESSION['passwordChangeErrors']) && !empty($_SESSION['passwordChangeErrors'])) {
                    echo '<div class="error">';
                    foreach ($_SESSION['passwordChangeErrors'] as $error) {
                        echo $error . '<br>';
                    }
                    echo '</div>';
                    unset($_SESSION['passwordChangeErrors']);
                }
                ?>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>


<script>
    document.getElementById('settings-link').addEventListener('click', function() {
        var myModal = new bootstrap.Modal(document.getElementById('settingsModal'));
        myModal.show();
    });

    <?php if (isset($_SESSION['passwordChangedError'])): ?>
        const passwordUpdateErrorModal = new bootstrap.Modal(document.getElementById('passwordUpdateErrorModal'));
        passwordUpdateErrorModal.show();
        <?php unset($_SESSION['passwordChangedError']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['passwordChangedSuccess'])): ?>
        const passwordUpdateSuccessModal = new bootstrap.Modal(document.getElementById('passwordUpdateSuccessModal'));
        passwordUpdateSuccessModal.show();
        <?php unset($_SESSION['passwordChangedSuccess']); ?>
    <?php endif; ?>

   

</script>
