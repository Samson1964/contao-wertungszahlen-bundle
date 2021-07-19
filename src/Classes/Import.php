<?php

namespace Schachbulle\ContaoWertungszahlenBundle\Classes;

if (!defined('TL_ROOT')) die('You cannot access this file directly!');


/**
 * Class dsb_trainerlizenzImport
  */
class Import extends \Backend
{

	/**
	 * Return a form to choose a CSV file and import it
	 * @param object
	 * @return string
	 */
	public function importCSV(\DataContainer $dc)
	{
		if (\Input::get('key') != 'importCSV')
		{
			return '';
		}

		$this->import('BackendUser', 'User');
		$class = $this->User->uploader;

		// See #4086
		if (!class_exists($class))
		{
			$class = 'FileUpload';
		}

		$objUploader = new $class();

		// Importiere die Daten, wenn das Formular abgeschickt wurde
		if (\Input::post('FORM_SUBMIT') == 'tl_wertungszahlen_import')
		{
			$arrUploaded = $objUploader->uploadTo('system/tmp');

			if(empty($arrUploaded))
			{
				\Message::addError($GLOBALS['TL_LANG']['ERR']['all_fields']);
				$this->reload();
			}

			$this->import('Database');

			foreach($arrUploaded as $strFile)
			{
				$objFile = new \File($strFile, true);

				// Datei einlesen
				$daten = file($objFile->dirname.'/'.$objFile->basename);

				// Alte Datensätze löschen
				$this->Database->prepare('DELETE FROM tl_wertungszahlen_ratings WHERE ratingList = ?')
				               ->execute(\Input::get('id'));

				$row_nr = 0;
				// Neue Daten importieren
				foreach($daten as $zeile)
				{
					$row_nr++;
					if($row_nr == 1) continue; // Kopfzeile überspringen
					
					$spalte = explode('|', trim($zeile));
					$nachname = utf8_encode($spalte[0]);
					$vorname = utf8_encode($spalte[1]);
					$rating = $spalte[2];
					$partien = $spalte[3];

					// Spieler bereits vorhanden?
					$result = $this->Database->prepare('SELECT * FROM tl_wertungszahlen_players WHERE lastname=? AND firstname=?')
					                         ->limit(1)
					                         ->execute($nachname, $vorname);
					if($result->numRows)
					{
						// Spieler vorhanden
						$id = $result->id;
					}
					else
					{
						// Spieler nicht vorhanden, dann eintragen
						$set = array
						(
							'lastname'  => $nachname,
							'firstname' => $vorname,
							'tstamp'    => time(),
							'published' => 1,
						);
						$objInsert = $this->Database->prepare("INSERT INTO tl_wertungszahlen_players %s")
						                            ->set($set)
						                            ->execute();
						$id = $objInsert->insertId;
					}

					// Wertungszahl eintragen
					$set = array
					(
						'pid'        => $id,
						'tstamp'     => time(),
						'ratingList' => \Input::get('id'),
						'rating'     => $rating,
						'games'      => $partien,
						'published'  => 1,
					);
					$objInsert = $this->Database->prepare("INSERT INTO tl_wertungszahlen_ratings %s")
					                            ->set($set)
					                            ->execute();
				}
				

			}

			\System::setCookie('BE_PAGE_OFFSET', 0, 0);
			$this->redirect(str_replace('&key=importCSV', '', \Environment::get('request')));
		}

		// Return form
		return '
<div id="tl_buttons">
<a href="'.ampersand(str_replace('&key=importCSV', '', \Environment::get('request'))).'" class="header_back" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']).'" accesskey="b">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a>
</div>

<h2 class="sub_headline">'.$GLOBALS['TL_LANG']['MOD']['wertungszahlen_import_headline'][1].'</h2>
'.\Message::generate().'
<form action="'.ampersand(\Environment::get('request'), true).'" id="tl_wertungszahlen_import" class="tl_form" method="post" enctype="multipart/form-data">
<div class="tl_formbody_edit">
<input type="hidden" name="FORM_SUBMIT" value="tl_wertungszahlen_import">
<input type="hidden" name="REQUEST_TOKEN" value="'.REQUEST_TOKEN.'">

<fieldset class="tl_tbox nolegend">
  <div class="widget w50">
    <h3>'.$GLOBALS['TL_LANG']['MOD']['wertungszahlen_import_file'][0].'</h3>'.$objUploader->generateMarkup().(isset($GLOBALS['TL_LANG']['MOD']['wertungszahlen_import'][1]) ? '
    <p class="tl_help tl_tip">'.$GLOBALS['TL_LANG']['MOD']['wertungszahlen_import_file'][1].'</p>' : '').'
  </div>
</fieldset>
</div>

<div class="tl_formbody_submit">

<div class="tl_submit_container">
  <input type="submit" name="save" id="save" class="tl_submit" accesskey="s" value="'.specialchars($GLOBALS['TL_LANG']['MOD']['wertungszahlen_import_submit'][0]).'">
</div>

</div>
</form>';
	}

	/**
	 * Return a form to choose a CSV file and import it
	 * @param object
	 * @return string
	 */
	public function importTable(\DataContainer $dc)
	{
		if (\Input::get('key') != 'importTable')
		{
			return '';
		}

		$this->import('BackendUser', 'User');
		$class = $this->User->uploader;

		// See #4086
		if (!class_exists($class))
		{
			$class = 'FileUpload';
		}

		$objUploader = new $class();

		// Importiere die Daten, wenn das Formular abgeschickt wurde
		if (\Input::post('FORM_SUBMIT') == 'tl_wertungszahlen_importTable')
		{
			$arrUploaded = $objUploader->uploadTo('system/tmp');

			if(empty($arrUploaded))
			{
				\Message::addError($GLOBALS['TL_LANG']['ERR']['all_fields']);
				$this->reload();
			}

			$this->import('Database');

			foreach($arrUploaded as $strFile)
			{
				$objFile = new \File($strFile, true);

				// Datei einlesen
				$daten = file_get_contents($objFile->dirname.'/'.$objFile->basename);

				if($objFile->extension == 'html' || $objFile->extension == 'htm')
				{
					// HTML-Import
					$tabelle = self::ImportHTML($daten);
					$csv = self::ConvertToCSV($tabelle);
				}
				elseif($objFile->extension == 'json')
				{
					// JSON-Import
				}
				else
				{
					// Falsches Format
					\Message::addError(sprintf($GLOBALS['TL_LANG']['ERR']['filetype'], $objFile->extension));
					continue;
					$tabelle = '';
				}

				$set = array
				(
					'csv'         => $csv,
					'importRaw'   => $daten,
					'importArray' => serialize($tabelle)
				);
				$this->Database->prepare('UPDATE tl_wertungszahlen_tabellen %s WHERE id = ?')
				               ->set($set)
				               ->execute(\Input::get('id'));

			}

			\System::setCookie('BE_PAGE_OFFSET', 0, 0);
			$this->redirect(str_replace('&key=importTable', '&act=edit', \Environment::get('request')));

		}

		// Return form
		return '
<div id="tl_buttons">
<a href="'.ampersand(str_replace('&key=importTable', '', \Environment::get('request'))).'" class="header_back" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']).'" accesskey="b">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a>
</div>

<h2 class="sub_headline">'.$GLOBALS['TL_LANG']['MOD']['wertungszahlen_importTable_headline'][1].'</h2>
'.\Message::generate().'
<form action="'.ampersand(\Environment::get('request'), true).'" id="tl_wertungszahlen_importTable" class="tl_form" method="post" enctype="multipart/form-data">
<div class="tl_formbody_edit">
<input type="hidden" name="FORM_SUBMIT" value="tl_wertungszahlen_importTable">
<input type="hidden" name="REQUEST_TOKEN" value="'.REQUEST_TOKEN.'">

<fieldset class="tl_tbox nolegend">
  <div class="widget w50">
    <h3>'.$GLOBALS['TL_LANG']['MOD']['wertungszahlen_importTable_file'][0].'</h3>'.$objUploader->generateMarkup().(isset($GLOBALS['TL_LANG']['MOD']['wertungszahlen_importTable'][1]) ? '
    <p class="tl_help tl_tip">'.$GLOBALS['TL_LANG']['MOD']['wertungszahlen_importTable_file'][1].'</p>' : '').'
  </div>
</fieldset>
</div>

<div class="tl_formbody_submit">

<div class="tl_submit_container">
  <input type="submit" name="save" id="save" class="tl_submit" accesskey="s" value="'.specialchars($GLOBALS['TL_LANG']['MOD']['wertungszahlen_importTable_submit'][0]).'">
</div>

</div>
</form>';
	}

	private function ImportHTML($string)
	{
		//$string = iconv('windows-1251', 'utf-8', $string); // Bug in paquettg/php-html-parser umgehen, https://github.com/paquettg/php-html-parser/issues/209#event-3327333893
		// Umwandeln von ANSI westeuropäisch in UTF8
		$string = iconv('windows-1252', 'utf-8', $string); // Bug in paquettg/php-html-parser umgehen, https://github.com/paquettg/php-html-parser/issues/209#event-3327333893
		$string = str_replace(array('<th', '</th>'), array('<td', '</td>'), $string);

		//$fp = fopen('test.html', 'w');
		//fputs($fp, $string);
		//fclose($fp);
		
		$dom = new \PHPHtmlParser\Dom;
		$dom->load($string);
		$table = $dom->find('table')[0];
		$rows = $table->find('tr');
		$tabelle = array();
		$daten = array();
		$rowNr = 0;
		foreach($rows as $row)
		{
			$cols = $row->find('td');
			$colNr = 0;
			$i = 0;
			foreach($cols as $col)
			{
				$colspan =  $col->getAttribute('colspan');
				if(!$colspan) $colspan = 1;
				$value = $col->innerHtml;
		
				// Rundenanzahl feststellen
				if($rowNr == 0 && $colNr == 0) $runden = count($cols) - 4;
		
				for($x = 0; $x < $colspan; $x++)
				{
					if($i == 0) $name = 'platz';
					elseif($i == 1) $name = 'cb-name';
					elseif($i == 2) $name = 'cb-land';
					elseif($i == 3) $name = 'cb-rating';
					elseif($i == $runden + 4) $name = 'punkte';
					elseif($i == $runden + 5) $name = 'wertung1';
					elseif($i == $runden + 6) $name = 'wertung2';
					else
					{
						$name = 'runden';
						$rundeIndex = $i - 4;
					}
		
					//if($name = 'cb-land')
					//{
						// Land extrahieren
						$array = array();
						preg_match('/src="([^"]*)"/i', $value, $array);
						$land = $array[1];
					//}
					
					$value = str_replace('&nbsp;', '', $value);
					$value = strip_tags($value);
					$value = utf8_decode($value);
					
					// Tabellenzelle schreiben
					if($name == 'runden') $tabelle[$rowNr][$name][$rundeIndex] = str_replace(array('&diams;', '&#9830;', '&loz;', '&#9674;'), array('s', 's', 'w', 'w'), $value);
					elseif($name == 'cb-name')
					{
						// Benutzername extrahieren, z.B.
						// Svane; Rasmus,(sumsar42)
						// Resultat: sumsar42
						if(strpos($value, '(') !== false)
						{
							// Öffnende Klammer ist im Wert, dann steht in (...) der Benutzername
							// Benutzername extrahieren
							$array = array();
							preg_match('/\((.*?)\)/', $value, $array);
							$cbname = $array[1];
							$tabelle[$rowNr][$name] = $cbname;
						}
						else
						{
							// Benutzername steht direkt im Wert
							$tabelle[$rowNr][$name] = $value;
						}
					}
					elseif($name == 'cb-land')
					{
						// Nation extrahieren, z.B.
						// flags/nat16_GER.gif
						// Internet%20Cup%20V%201%20B%202020-Dateien/nat16_GER.gif
						// Resultat: GER
						$tabelle[$rowNr][$name] = substr(str_replace('.gif', '', $land), -3);
					}
					else $tabelle[$rowNr][$name] = $value;
					$i++;
				}
				$colNr++;
			}
			$rowNr++;
		}
		//echo "<pre>";
		//print_r($tabelle);
		//echo "</pre>";
		return $tabelle;
	}

	private function ConvertToCSV($tabelle)
	{

		// Spaltenbreiten ermitteln
		$breite = $tabelle[0];
		for($x = 0; $x < count($tabelle); $x++)
		{
			if($x == 0)
			{
				$breite['platz'] = 3;
				$breite['cb-name'] = 8;
				$breite['cb-land'] = 4;
				$breite['cb-rating'] = 3;
				$breite['punkte'] = 4;
				$breite['wertung1'] = 4;
				$breite['wertung2'] = 4;
				for($y = 0; $y < count($tabelle[$x]['runden']); $y++)
				{
					$breite['runden'][$y] = strlen($tabelle[$x]['runden'][$y]);
				}
			}
			else
			{
				$breite['platz'] = strlen($tabelle[$x]['platz']) > $breite['platz'] ? strlen($tabelle[$x]['platz']) : $breite['platz'];
				$breite['cb-name'] = strlen($tabelle[$x]['cb-name']) > $breite['cb-name'] ? strlen($tabelle[$x]['cb-name']) : $breite['cb-name'];
				$breite['cb-land'] = strlen($tabelle[$x]['cb-land']) > $breite['cb-land'] ? strlen($tabelle[$x]['cb-land']) : $breite['cb-land'];
				$breite['cb-rating'] = strlen($tabelle[$x]['cb-rating']) > $breite['cb-rating'] ? strlen($tabelle[$x]['cb-rating']) : $breite['cb-rating'];
				$breite['punkte'] = strlen($tabelle[$x]['punkte']) > $breite['punkte'] ? strlen($tabelle[$x]['punkte']) : $breite['punkte'];
				$breite['wertung1'] = strlen($tabelle[$x]['wertung1']) > $breite['wertung1'] ? strlen($tabelle[$x]['wertung1']) : $breite['wertung1'];
				$breite['wertung2'] = strlen($tabelle[$x]['wertung2']) > $breite['wertung2'] ? strlen($tabelle[$x]['wertung2']) : $breite['wertung2'];
				for($y = 0; $y < count($tabelle[$x]['runden']); $y++)
				{
					$breite['runden'][$y] = strlen($tabelle[$x]['runden'][$y]) > $breite['runden'][$y] ? strlen($tabelle[$x]['runden'][$y]) : $breite['runden'][$y];
				}
			}
		}

		$csv = '';
		for($x = 0; $x < count($tabelle); $x++)
		{
			if($x == 0)
			{
				$csv = 'Pl.;Benutzer;Land;CBR;Pkt.;SoBe;Wtg2;';
				$csv = substr('Pl.'.str_repeat(' ', 100), 0, $breite['platz']).';';
				$csv .= substr('Benutzer'.str_repeat(' ', 100), 0, $breite['cb-name']).';';
				$csv .= substr('Land'.str_repeat(' ', 100), 0, $breite['cb-land']).';';
				$csv .= substr('CBR'.str_repeat(' ', 100), 0, $breite['cb-rating']).';';
				$csv .= substr('Pkt.'.str_repeat(' ', 100), 0, $breite['punkte']).';';
				$csv .= substr('SoBe'.str_repeat(' ', 100), 0, $breite['wertung1']).';';
				$csv .= substr('Wtg2'.str_repeat(' ', 100), 0, $breite['wertung2']).';';

				for($y = 0; $y < count($tabelle[$x]['runden']); $y++)
				{
					$csv .= substr($tabelle[$x]['runden'][$y].str_repeat(' ', 100), 0, $breite['runden'][$y]).';';
				}
				$csv = substr($csv, 0, -1)."\n";
			}
			else
			{
				$csv .= mb_substr($tabelle[$x]['platz'].str_repeat(' ', 100), 0, $breite['platz']).';';
				$csv .= mb_substr($tabelle[$x]['cb-name'].str_repeat(' ', 100), 0, $breite['cb-name']).';';
				$csv .= mb_substr($tabelle[$x]['cb-land'].str_repeat(' ', 100), 0, $breite['cb-land']).';';
				$csv .= mb_substr($tabelle[$x]['cb-rating'].str_repeat(' ', 100), 0, $breite['cb-rating']).';';
				$csv .= mb_substr($tabelle[$x]['punkte'].str_repeat(' ', 100), 0, $breite['punkte']).';';
				$csv .= mb_substr($tabelle[$x]['wertung1'].str_repeat(' ', 100), 0, $breite['wertung1']).';';
				$csv .= mb_substr($tabelle[$x]['wertung2'].str_repeat(' ', 100), 0, $breite['wertung2']).';';
				for($y = 0; $y < count($tabelle[$x]['runden']); $y++)
				{
					$csv .= mb_substr($tabelle[$x]['runden'][$y].str_repeat(' ', 100), 0, $breite['runden'][$y]).';';
				}
				$csv = substr($csv, 0, -1)."\n";
			}
		} 
		return $csv;
	}
}
