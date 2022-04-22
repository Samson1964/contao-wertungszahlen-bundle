<?php

$GLOBALS['TL_LANG']['tl_wertungszahlen_import']['headline'] = 'Wertungszahlen aus einer CSV-Datei importieren';
$GLOBALS['TL_LANG']['tl_wertungszahlen_import']['format'] = 
'Die hochgeladenen CSV-Dateien sollten im UTF-8-Format vorliegen. Je Zeile steht ein Datensatz in der Datei. 
Die 1. Zeile ist die Kopfzeile mit der Definition der Spalten. Die Spalten werden mit einem Semikolon voneinander getrennt.
Die Reihenfolge der Spalten ist frei wählbar.<br><br>
Eindeutiges Kriterium der Zuordnung zu vorhandenen Datensätzen sind die Spalten <b>vorname</b> und <b>nachname</b>. Ist ein Datensatz mit mit diesen beiden Spalten bereits
vorhanden, wird dieser mit der Datensatz um die neue FWZ ergänzt. 
Folgende Spaltenarten werden unterstützt:
<table class="tl_wertungszahlen_tabelle">
<tr>
	<th>Name der Spalte<br>(1. Zeile)</th>
	<th>Wert der Spalte<br>(2. - x. Zeile)</th>
</tr>
<tr>
	<td>vorname</td>
	<td>Vorname des Spielers</td>
</tr>
<tr>
	<td>nachname</td>
	<td>Nachname des Spielers</td>
</tr>
<tr>
	<td>partien</td>
	<td>Anzahl der gespielten Partien</td>
</tr>
<tr>
	<td>fwz</td>
	<td>Fernschach-Wertungszahl</td>
</tr>
</table>
<p style="margin:18px"><b>Achtung! Beim Import werden die alten FWZ in der ausgewählten Wertungsliste gelöscht.</b></p>
';
