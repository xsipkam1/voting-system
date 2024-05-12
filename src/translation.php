<?php

$translations = array(
    "Domov" => "Home",
    "Používatelia" => "Users",
    "Odhlásenie" => "Logout",
    "Prihlásenie" => "Sign in",
    "Registrácia" => "Sign up",
    "Jazyk" => "Language",
    "Hlasovací systém" => "Voting system",
    "Prihlásený ako" => "Logged in as",
    "Predmet" => "Subject",
    "Dátum vytvorenia" => "Date created",
    "Otázka č." => "Question no.",
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