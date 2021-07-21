<?php

namespace Schachbulle\ContaoWertungszahlenBundle\Classes;

/**
 * Class CounterRegister
 *
 * @copyright  Frank Hoppe 2014
 * @author     Frank Hoppe
 *
 * Basisklasse vom FH-Counter
 * Erledigt die Zählung der jeweiligen Contenttypen und schreibt die Zählerwerte in $GLOBALS
 */
class Suche extends \Module
{

	var $search; // Nimmt die Suchanfrage auf

	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### WERTUNGSZAHLEN-SUCHE ###';
			$objTemplate->title = $this->name;
			$objTemplate->id = $this->id;

			return $objTemplate->parse();
		}
		else
		{
			// FE-Modus: URL mit allen möglichen Parametern auflösen
			$this->search = \Input::get('wzs');
		}

		return parent::generate(); // Weitermachen mit dem Modul
	}

	/**
	 * Generate the module
	 */
	protected function compile()
	{

		// Template-Objekt anlegen
		$this->Template = new \FrontendTemplate('mod_wertungszahlen_suche');

		if($this->search)
		{
			// Die Suche ist aktiviert worden -> Daten einlesen
			$this->Template->sucheaktiv = true; // Eine Suche wurde ausgelöst
			$this->Template->suchbegriff = strtolower($this->search);

			$cachetime = 3600 * 24 * 40; // 40 Tage
			$cachekey = $this->wertungszahlen_liste;

			// Cache initialisieren
			$this->cache = new \Schachbulle\ContaoHelperBundle\Classes\Cache('Wertungszahlensuche');
			$this->cache->eraseExpired(); // Cache aufräumen, abgelaufene Schlüssel löschen

			if($this->cache->isCached($cachekey))
			{
				// Daten aus dem Cache laden
				$result = $this->cache->retrieve($cachekey);
			}
			else
			{

				// Wertungszahlen laden
				$objElo = \Database::getInstance()->prepare('SELECT tl_wertungszahlen_ratings.*, tl_wertungszahlen_players.* FROM tl_wertungszahlen_ratings LEFT JOIN tl_wertungszahlen_players ON tl_wertungszahlen_ratings.pid = tl_wertungszahlen_players.id WHERE ratingList = ? ORDER BY lastname DESC, firstname DESC')
				                                  ->execute($this->wertungszahlen_liste);

				// Elo zuweisen
				if($objElo->numRows > 1)
				{
					$result = array();
					// Datensätze anzeigen
					while($objElo->next())
					{
						$result[] = array
						(
							'name'     => $objElo->lastname.', '.$objElo->firstname,
							'rating'   => $objElo->rating,
							'games'    => $objElo->games,
						);
					}
					// Daten im Cache speichern
					$this->cache->store($cachekey, $result, $cachetime);
				}
			}

			// Suchen
			$ergebnis = array();
			foreach($result as $item)
			{
				$pos = stripos($item['name'], $this->search);
				if($pos !== false)
				{
					$ergebnis[] = $item['name'].' - '.$item['rating'].' ('.$item['games'].')';
				}
				else
				{
					//$ergebnis[] = $item['name']." nicht gefunden $pos";
				}
			}
			$this->Template->suchtreffer = count($ergebnis);
			$this->Template->suchergebnis = $ergebnis;
			
		}
		else
		{
			// Keine Suche aktiv, deshalb nur das Formular ausgeben
			$this->Template->sucheaktiv = false; // Eine Suche wurde nicht ausgelöst
		}

	}

}
