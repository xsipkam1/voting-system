<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
    <body>
    <div id="menu">
        <header>
            <nav class="navbar navbar-expand-md navbar-dark bg-dark p-0">
                <div class="container-fluid">
                    
                    <a href="index.php">Domov</a>
                    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-label="Expand">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="nav">
                        <ul class="navbar-nav ms-auto">
                            <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) : ?>
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
</body>
</html>