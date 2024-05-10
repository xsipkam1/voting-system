<div id="menu">
    <header>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark p-0">
            <div class="container-fluid">
                
                <li class="nav-item">
                    <a href="index.php" class="nav-link active mx-3" aria-current="page">Domov</a>
                </li>

                <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-label="Expand">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="nav">
                    <ul class="navbar-nav ms-auto">
                        <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) : ?>
                            <?php if ($_SESSION['role'] === 'A') : ?>
                                <li class="nav-item">
                                    <a href="manageUsers.php" class="nav-link mx-3">Používatelia</a>
                                </li>
                            <?php endif; ?>
                            <li class="nav-item">
                                <a href="logout.php" class="nav-link mx-3">Odhlásenie</a>
                            </li>
                        <?php else : ?>
                            <li class="nav-item">
                                <a href="login.php" class="nav-link mx-3">Prihlásenie</a>
                            </li>
                            <li class="nav-item">
                                <a href="register.php" class="nav-link mx-3">Registrácia</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>

            </div>
        </nav>
    </header>
</div>
