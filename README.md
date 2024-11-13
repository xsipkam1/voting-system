# ZADANIE

Vytvorte aplikáciu, v rámci ktorej realizujete online hlasovací systém využiteľný počas prednášok. Nezabudnite na to, že sa hodnotí aj grafický dizajn vytvorenej aplikácie, vhodne navrhnuté členenie, ľahkosť orientácie v prostredí. Pamätať by ste mali aj na zabezpečenie celej aplikácie. Na vypracovanie projektu je možné použiť aj PHP framework.

## Požiadavky

* Pri práci na projekte je potrebné používať verzionovací systém, napr. github, gitlab, bitbucket.
* Vytvorená webstránka bude navrhnutá ako dvojjazyčná (slovenčina, angličtina).
Pozn.: ak sa prepínate medzi jazykmi, musíte zostať na tej istej stránke ako ste boli pred prepnutím a nie vrátiť sa na domovskú stránku aplikácie.
* Celá stránka bude responzívna vrátane použitej grafiky.
* Aplikácia bude vyžadovať 3 typy rolí: neprihlásený používateľ, prihlásený používateľ a administrátor.
* Na vhodnom mieste bude umiestnená používateľská príručka aplikácie, kde bude vysvetlené, čo ktorá rola umožňuje a ako je možné dané veci realizovať (návod na použitie). Túto príručku bude možné exportovať aj do pdf súboru. V prípade zmeny v návode na stránke, sa táto zmena musí odraziť aj vo vygenerovanom PDF súbore (t.j. súbor je treba generovať dynamicky).
* Vytvorte video, ktorým budete dokumentovať celú funkcionalitu vytvorenej aplikácie. Ak niektorá funkcionalita nebude ukázaná na videu, tak ju môžeme považovať za nespravenú.
* Titulná stránka aplikácie bude poskytovať možnosť prihlásenia a zadania vstupného kódu pre zobrazenie hlasovacej otázky.

## Pohľad neprihláseného používateľa:

* Na stránku s hlasovacou otázkou sa bude dať dostať načítaním zverejneného QR kódu, zadaním vstupného kódu na domovskej stránke aplikácie alebo zadaním adresy do prehliadača v tvare https://nodeXX.webte.fei.stuba.sk/abcde, kde abcde prezentuje 5 znakov vstupný kód, ktorý presne definuje zobrazovanú hlasovaciu otázku. Treba zapracovať všetky možnosti.
* Po vyplnení hlasovacej otázky bude používateľ presmerovaný na stránku s grafickým zobrazením výsledkov hlasovania na danú otázku, odkiaľ bude možný návrat na domovskú stránku aplikácie.
* Pri zobrazení výsledkov hlasovania na otvorenú otázku budú odpovede zobrazené buď ako položky nečíslovaného zoznamu alebo pomocou tzv. „word cloud-u“, ktorý si je treba vytvoriť samostatne, t.j. pri tejto úlohe nie je možné prebrať kód z Internetu alebo využiť nejakú službu. Pri „word cloud-e“ bude mať počet rovnakých odpovedí na otázku vplyv na veľkosť písma pri zobrazení tejto odpovede (ak sa napríklad odpoveď „Martin“ vyskytne medzi všetkými odpoveďami 4x a odpoveď „Zvolen“ 8x, tak „Zvolen“ bude vo „word cloud-e“ zobrazený väčším písmom ako „Martin“).

## Pohľad prihláseného používateľa:
Z pohľadu prihláseného používateľa je potrebné, aby aplikácia umožnila:

* prihlásenie do aplikácie na základe vlastnej registrácie (používateľ sa nemôže zaregistrovať ako administrátor),
* zmenu svojho hesla,
* zadefinovanie viacerých hlasovacích otázok a definovanie, ktoré z nich sú aktívne a ktoré nie,
* pre každú otázku vygenerovať QR kód a jedinečný náhodne generovaný 5 znakov kód, ktoré slúžia na zobrazenie otázky (viď pohľad neprihláseného používateľa, bod 1),
* jednoduché zadefinovanie 2 typov otázok;
    *otázky s výberom správnej odpovede (môže byť jedna, ale aj viacero správnych odpovedí),
    *otázky s otvorenou krátkou odpoveďou.
* pri otázkach s otvorenou odpoveďou definovať, ako sa budú zobrazovať výsledky hlasovania - či sa zobrazia ako položky zoznamu alebo vo forme „word cloud-u“.
* úpravu, vymazanie a kopírovanie už definovaných otázok.
* ku každej otázke definovať, k akému predmetu sa vzťahuje.
* filtrovať otázky podľa predmetu a podľa dátumu vytvorenia.
* uzatvoriť aktuálne hlasovania na danú otázku. Uzatvorenie otázky spočíva v tom, že všetky doterajšie odpovede na otázku sa zálohujú k určitému dátumu a ďalšie hlasovanie začína ako keby od začiatku. Pri uzatváraní sa k danému uzatvoreniu bude dať vytvoriť poznámka a do databázy sa uloží aj dátum uzatvorenia.
* zobraziť výsledky aktuálnych, ale aj archivovaných hlasovaní. Pri otázkach s výberom správnej odpovede umožnite pri jednotlivých otázkach aj porovnanie aktuálneho hlasovania s historickými hlasovaniami (napr. pomocou tabuľky). Ak napr. pri nejakej otázke budú odpovede ÁNO a NIE, tak z tohoto porovnania bude možné vidieť, že napr. v r. 2024 za odpoveď ÁNO hlasovalo 46% respondentov, v r. 2023 53% respondentov a v r. 2022 82% respondentov, t.j. že tendencia hlasovania sa rokmi mení.
* export otázok a odpovedí na do externého súboru (csv, json, xml - výber je na vás).

## Pohľad administrátora:

* Administrátor má tú istú funkcionalitu ako prihlásený používateľ s tým rozdielom, že má k dispozícii hlasovacie otázky všetkých prihlásených používateľov s možnosťou filtrovania nad vybraným používateľom.
* V prípade, že administrátor vytvára novú hlasovaciu otázku je treba špecifikovať, v koho mene to robí (môže to robiť v svojom mene, ale aj v mene iného používateľa)
* Okrem toho administrátor môže robiť aj správu prihlásených používateľov (celý CRUD spolu s prípadnou zmenou hesla) a zmeniť im aj rolu.
