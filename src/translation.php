<?php
session_start();

$translations = array(
    "Domov" => "Home",
    "Používatelia" => "Users",
    "Odhlásenie" => "Logout",
    "Prihlásenie" => "Login",
    "Registrácia" => "Registration",
    "Jazyk" => "Language",
    "Hlasovací systém" => "Voting system",
    "Prihlásený ako" => "Login as",
    "Predmet" => "Subject",
    "Dátum vytvorenia" => "Date of creation",
    "Vytvoriť otázku" => "Create question",
    "Otázka č." => "Question n.",
    "Vytvoriť otázku" => "Create question",
    "Vytvoriť otázku" => "Create question",
    "Vytvoriť otázku" => "Create question",
    "Vytvoriť otázku" => "Create question",
    "Vytvoriť otázku" => "Create question",
);

function translate($word) {
    global $translations;
    $currentLanguage = isset($_SESSION['currentLanguage']) ? $_SESSION['currentLanguage'] : "sk";
    if ($currentLanguage === 'sk') {
        echo $word;
    } else {
        echo $translations[$word];
    }
}

?>
