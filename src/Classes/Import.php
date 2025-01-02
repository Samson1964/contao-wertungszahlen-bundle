<?php

namespace Schachbulle\ContaoWertungszahlenBundle\Classes;

/**
 * Class Import
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

				if ($objFile->extension != 'csv')
				{
					\Message::addError(sprintf($GLOBALS['TL_LANG']['ERR']['filetype'], $objFile->extension));
					continue;
				}

				//log_message('Importiere Datei: '.$txtFile,'wertungszahlen.log');
				$resFile = $objFile->handle;
				$record_count = 0;
				$neu_count = 0;
				$update_count = 0;
				$kopf = array(); // Nimmt die Spaltennamen aus Zeile 1 auf
				$start = microtime(true);

				// Alte Datensätze löschen
				//log_message('Lösche alte Wertungszahlen aus Liste '.\Input::get('id'),'wertungszahlen.log');
				\Database::getInstance()->prepare('DELETE FROM tl_wertungszahlen_ratings WHERE ratingList = ?')
				                        ->execute(\Input::get('id'));

				//log_message('Import startet ...','wertungszahlen.log');
				while(!feof($resFile))
				{
					$zeile = self::remove_utf8_bom(trim(fgets($resFile)));
					$spalte = explode(';', $zeile);
					if($record_count == 0)
					{
						// Kopfzeile auslesen
						$kopf = $spalte;
						//log_message('Lese Kopfzeile '.$record_count.': '.$zeile,'wertungszahlen.log');
					}
					else
					{
						if($zeile)
						{
							// Datensatz auslesen
							//log_message('Importiere Datenzeile '.$record_count.': '.$zeile,'wertungszahlen.log');
							$set = array();
							$fwzdaten = array();
							for($x = 0; $x < count($spalte); $x++)
							{
								switch($kopf[$x])
								{
									case 'nachname':
										$fwzdaten['lastname'] = \Schachbulle\ContaoHelperBundle\Classes\Helper::is_utf8($spalte[$x]) ? $spalte[$x] : utf8_encode($spalte[$x]); break;
									case 'vorname':
										$fwzdaten['firstname'] = \Schachbulle\ContaoHelperBundle\Classes\Helper::is_utf8($spalte[$x]) ? $spalte[$x] : utf8_encode($spalte[$x]); break;
									case 'fwz':
										$fwzdaten['rating'] = $spalte[$x]; break;
									case 'partien':
										$fwzdaten['games'] = $spalte[$x]; break;
									default:
								}
							}

							// Spieler bereits vorhanden?
							$result = \Database::getInstance()->prepare('SELECT * FROM tl_wertungszahlen_players WHERE lastname=? AND firstname=?')
							                                  ->limit(1)
							                                  ->execute($fwzdaten['lastname'], $fwzdaten['firstname']);
							if($result->numRows)
							{
								// Spieler vorhanden
								$id = $result->id;
								$update_count++;
							}
							else
							{
								// Spieler nicht vorhanden, dann eintragen
								//log_message('Neuer Spieler: '.$fwzdaten['firstname'].' '.$fwzdaten['lastname'],'wertungszahlen.log');
								$set = array
								(
									'lastname'  => $fwzdaten['lastname'],
									'firstname' => $fwzdaten['firstname'],
									'tstamp'    => time(),
									'published' => 1,
								);
								$objInsert = \Database::getInstance()->prepare("INSERT INTO tl_wertungszahlen_players %s")
								                                     ->set($set)
								                                     ->execute();
								$id = $objInsert->insertId;
								$neu_count++;
							}

							// Wertungszahl eintragen
							//log_message('Neue FWZ bei '.$fwzdaten['firstname'].' '.$fwzdaten['lastname'].': '.$fwzdaten['rating'],'wertungszahlen.log');
							$set = array
							(
								'pid'        => $id,
								'tstamp'     => time(),
								'ratingList' => \Input::get('id'),
								'rating'     => $fwzdaten['rating'],
								'games'      => $fwzdaten['games'],
								'published'  => 1,
							);
							$objInsert = \Database::getInstance()->prepare("INSERT INTO tl_wertungszahlen_ratings %s")
							                                     ->set($set)
							                                     ->execute();
						}
					}
					$record_count++;
				}
				$dauer = sprintf('%f0.4', microtime(true) - $start);
				\System::log('Turnierimport aus Datei '.$objFile->name.' - '.($record_count).' Datensätze - '.$neu_count.' Spieler neu, '.$update_count.' Spieler ergänzt - Dauer: '.$dauer.'s', __METHOD__, TL_GENERAL);
			}

			\System::setCookie('BE_PAGE_OFFSET', 0, 0);
			$this->redirect(str_replace('&key=importCSV', '', \Environment::get('request')));
		}

		// Return form
		return '
<div id="tl_buttons">
<a href="'.ampersand(str_replace('&key=importCSV', '', \Environment::get('request'))).'" class="header_back" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']).'" accesskey="b">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a>
</div>

'.\Message::generate().'
<form action="'.ampersand(\Environment::get('request'), true).'" id="tl_wertungszahlen_import" class="tl_form tl_edit_form" method="post" enctype="multipart/form-data">

<div class="tl_formbody_edit">
	<input type="hidden" name="FORM_SUBMIT" value="tl_wertungszahlen_import">
	<input type="hidden" name="REQUEST_TOKEN" value="'.REQUEST_TOKEN.'">
	<input type="hidden" name="MAX_FILE_SIZE" value="' . \Config::get('maxFileSize') . '">

	<h2 class="sub_headline">'.$GLOBALS['TL_LANG']['tl_wertungszahlen_import']['headline'].'</h2>
	<p style="margin: 18px;">'.$GLOBALS['TL_LANG']['tl_wertungszahlen_import']['format'].'

	<div class="widget">
		<h3>'.$GLOBALS['TL_LANG']['MOD']['wertungszahlen_import_file'][0].'</h3>'.$objUploader->generateMarkup().(isset($GLOBALS['TL_LANG']['MOD']['wertungszahlen_import'][1]) ? '
		<p class="tl_help tl_tip">'.$GLOBALS['TL_LANG']['MOD']['wertungszahlen_import_file'][1].'</p>' : '').'
	</div>
</div>

<div class="tl_formbody_submit">

	<div class="tl_submit_container">
		<input type="submit" name="save" id="save" class="tl_submit" accesskey="s" value="'.specialchars($GLOBALS['TL_LANG']['MSC']['tw_import'][0]).'">
	</div>

</div>
</form>
';
	}

	function remove_utf8_bom($text)
	{
		$bom = pack('H*','EFBBBF');
		$text = preg_replace("/^$bom/", '', $text);
		return $text;
	}

}
