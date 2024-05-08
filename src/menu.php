<div id="menu">
    <a href="index.php">Domov</a>
    <?php
    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
        echo '<a href="logout.php">Odhlásenie</a>';
        echo '<span>Prihlásený ako ' . $_SESSION['login'] . '</span>';
        echo '<span>Rola: ' . ($_SESSION['role'] === 'A' ? 'Admin' : 'User') . '</span>';
    } else {
        echo '<a href="login.php">Prihlásenie</a>';
        echo '<a href="register.php">Registrácia</a>';
    }
    ?>
</div>
