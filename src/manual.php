<?php
session_start();
require_once("../../../configFinal.php");
include_once 'translation.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo translate('Používatelská príručka'); ?></title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
</head>
<body class="bg-dark-custom">
<?php include "menu.php"; ?>
<div class="container-md p-1 mt-4 mb-4">
    <h1><?php echo translate('Používatelská príručka'); ?></h1>
    <h2><?php echo translate('Čo ktorá rola umožnuje:'); ?></h2>

    <div class='border border-light shadow p-4 m-2 bg-white bg-gradient'>
        <h3><?php echo translate('Neprihlásený používateľ'); ?></h3>
        <ul class="list-group list-group-flush">
            <li class="list-group-item"><?php echo translate("Registráciu a prihlásenie pomocou tlačidiel v menu, a následným vyplnením údajov."); ?>
            </li>
            <li class="list-group-item"><?php echo translate('Prístup k používateľskej príručke.'); ?></li>
            <li class="list-group-item"><?php echo translate('Zmenu jazyka kliknutím na vlajky v menu.'); ?></li>
            <li class="list-group-item"><?php echo translate('Prístup na stránku s hlasovacou otázkou: načítaním QR kódu, vložením kódu do URL alebo napísaním kódu do vstupného pola na domovskej stránke.'); ?>
            </li>
            <li class="list-group-item"><?php echo translate('Prístup na stránku so zobrazenými výsledkami hlasovania po hlasovaní v otázke.'); ?>
            </li>
        </ul>
    </div>
    <div class='border border-light shadow p-4 m-2 bg-white bg-gradient'>
        <h3><?php echo translate('Prihlásený používateľ'); ?></h3>
        <ul class="list-group list-group-flush">
            <li class="list-group-item"><?php echo translate('Zmenu svojho hesla a odhlásenie pomocou tlačidiel v menu.'); ?>
            </li>
            <li class="list-group-item"><?php echo translate('Vytvorenie otázok dvoch typov: výber odpovede a otvorená odpoveď.'); ?>
            </li>
            <li class="list-group-item"><?php echo translate('Vidieť zoznam všetkých svojich otázok a filtrovať ich pomocou predmetu a dátumu vytvorenia.'); ?>
            </li>
            <li class="list-group-item"><?php echo translate('Vygenerovať kód a QR pre hlasovanie v otázke.'); ?></li>
            <li class="list-group-item"><?php echo translate('Úpravu, vymazanie a kopírovanie už definovaných otázok.'); ?>
            </li>
            <li class="list-group-item"><?php echo translate('Uzavretie otázky a zobrazenie výsledkov hlasovania.'); ?>
            </li>
            <li class="list-group-item"><?php echo translate('Export otázok a odpovedí do JSON-u.'); ?></li>
        </ul>
    </div>
    <div class='border border-light shadow p-4 m-2 bg-white bg-gradient'>
        <h3>Admin</h3>
        <ul class="list-group list-group-flush">
            <li class="list-group-item"><?php echo translate('Všetku funkcionalitu prihláseného používateľa.'); ?></li>
            <li class="list-group-item"><?php echo translate('Vidieť zoznam všetkých (nie len svojich) otázok a filtrovať ich pomocou predmetu, dátumu vytvorenia a používateľa.'); ?>
            </li>
            <li class="list-group-item"><?php echo translate('Správu používateľov, čo zahrňuje vytváranie, úpravu a vymazávanie používateľov.'); ?>
            </li>
        </ul>
    </div>
    <form method="post" id="pdfForm">
        <button type="button" name="export_pdf" id="exportPdfBtn" class="btn btn-primary mb-3">Exportovať do PDF
        </button>
    </form>

</div>


</body>
</html>
