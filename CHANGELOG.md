# Wertungszahlen-Listen Changelog

## Version 1.1.1 (2025-01-02)

* Fix: Warning: Undefined variable $txtFile in /src/Classes/Import.php (line 57) 
* Change: log_message deaktiviert in Import.php wegen Problemen auf BdF-Website mit PHP 8.2
* Fix: Warning: Undefined array key "sex_options" in src/Resources/contao/dca/tl_wertungszahlen_players.php (line 224) 

## Version 1.1.0 (2024-04-18)

* Add: codefog/contao-haste
* Change: Haste-Toggler statt des normalen Togglers
* Add: Kompatibilität PHP 8

## Version 1.0.4 (2022-04-22)

* Add: Backend-CSS
* Fix: UTF8-Erkennung beim Import verbessert
* Fix: Import-Formular verbessert mit Infos zum Importformat
* Fix: Importfunktionen verbessert

## Version 1.0.3 (2021-08-21)

* Fix: Änderung Sortierung tl_wertungszahlen_ratings von id ASC auf id DESC
* Change: tl_wertungszahlen_ratings.ratingList auf SELECT umgestellt
* Change: tl_wertungszahlen_players.sex auf SELECT umgestellt
* Change: tl_wertungszahlen_players.birthday auf alternative Eingabe von MM.JJJJ oder JJJJ umgestellt

## Version 1.0.2 (2021-08-20)

* Fix: Ausgabe der Wertungszahlen in BE-Liste verbessert

## Version 1.0.1 (2021-08-10)

* Fix: Spielersuche, Ausgabe der Trefferliste aufsteigend sortiert

## Version 1.0.0 (2021-07-21)

* Add: Ausgabe einer Suche als Frontend-Modul
* Add: Abhängigkeit schachbulle/contao-helper-bundle

## Version 0.0.4 (2021-07-19)

* Ausbau des Bundles mit den benötigten Feldern
* Add: Importfunktion für Wertungslisten
* Add: Ausgabe einer Bestenliste als Frontend-Modul

## Version 0.0.3 (2021-07-19)

* Ausbau des Bundles und Update wegen Symlinks

## Version 0.0.2 (2021-07-19)

* Fix: Überrest vom Elo-Bundle

## Version 0.0.1 (2021-07-18)

* Initialversion
