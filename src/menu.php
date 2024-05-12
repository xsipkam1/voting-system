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
                    <a href="index.php" class="nav-link active mx-3" aria-current="page"><i class="bi bi-house"></i> <?php translate('Domov'); ?></a>
                </li>

                <li class="nav-item">
                    <span> <?php translate('Jazyk'); ?>:</span>
                    <a href="?lang=sk" class="nav-link"><img src="https://www.geonames.org/flags/x/sk.gif" alt="Slovensky" style="width: 20px; height: auto;"></a>
                    <a href="?lang=en" class="nav-link"><img src="https://www.geonames.org/flags/x/gb.gif" alt="English" style="width: 20px; height: auto;"></a>
                </li>
                <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-label="Expand">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="nav">
                    <ul class="navbar-nav ms-auto">
                        <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) : ?>
                            <?php if ($_SESSION['role'] === 'A') : ?>
                                <li class="nav-item">
                                    <a href="manageUsers.php" class="nav-link mx-3"> <i class="bi bi-people"></i> <?php translate('Používatelia'); ?></a>
                                </li>
                            <?php endif; ?>
                            <li class="nav-item">
                                <a href="logout.php" class="nav-link mx-3"> <i class="bi bi-box-arrow-right"></i> <?php echo translate('Odhlásenie'); ?></a>
                            </li>
                        <?php else : ?>
                            <li class="nav-item">
                                <a href="login.php" class="nav-link mx-3"><i class="bi bi-door-open"></i> <?php echo translate('Prihlásenie'); ?></a>
                            </li>
                            <li class="nav-item">
                                <a href="register.php" class="nav-link mx-3"><i class="bi bi-person-plus"></i> <?php echo translate('Registrácia'); ?></a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>

            </div>
        </nav>
    </header>
</div>
