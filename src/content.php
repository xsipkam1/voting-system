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
