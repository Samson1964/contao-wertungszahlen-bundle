<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @package   Elo
 * @author    Frank Hoppe
 * @license   GNU/LPGL
 * @copyright Frank Hoppe 2016
 */


/**
 * Namespace
 */
namespace Schachbulle\ContaoWertungszahlenBundle\Classes;

/**
 * Class Elo
 *
 * @copyright  Frank Hoppe 2016
 * @author     Frank Hoppe
 * @package    Devtools
 */
class Bestenliste extends \Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_wertungszahlenbestenliste';
	var $cache = false;

	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### WERTUNGSZAHLEN-BESTENLISTE ###';
			$objTemplate->title = $this->name;
			$objTemplate->id = $this->id;

			return $objTemplate->parse();
		}

		return parent::generate(); // Weitermachen mit dem Modul
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{
		$cachetime = 3600 * 24 * 40; // 40 Tage
		$cachekey = $this->wertungszahlen_liste.'_'.$this->wertungszahlen_topcount;

		// Cache initialisieren
		$this->cache = new \Schachbulle\ContaoHelperBundle\Classes\Cache('Wertungszahlenbestenliste');
		$this->cache->eraseExpired(); // Cache aufräumen, abgelaufene Schlüssel löschen

		if($this->cache->isCached($cachekey))
		{
			// Daten aus dem Cache laden
			$result = $this->cache->retrieve($cachekey);
		}
		else
		{

			// Wertungszahlen laden
			$objElo = \Database::getInstance()->prepare('SELECT tl_wertungszahlen_ratings.*, tl_wertungszahlen_players.* FROM tl_wertungszahlen_ratings LEFT JOIN tl_wertungszahlen_players ON tl_wertungszahlen_ratings.pid = tl_wertungszahlen_players.id WHERE ratingList = ? ORDER BY rating DESC, games DESC')
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

		// Ausgabe schreiben
		$content = '<table>';
		$content .= '<tr>';
		$content .= '<th class="head_0 col_first">Platz</th>';
		$content .= '<th class="head_1">Name</th>';
		$content .= '<th class="head_2">Wertungszahl</th>';
		$content .= '<th class="head_3 col_last">Partien</th>';
		$content .= '</tr>';
		$altelo = 0;
		$odd = 'odd';
		for($x = 0; $x < count($result); $x++)
		{
			$class = 'row_'.$x.' ';
			if($x == 0) $class .= 'row_first ';
			elseif($x + 1 == count($result)) $class .= 'row_last ';
			$class .= $odd;
			if($odd == 'odd') $odd = 'even';
			else $odd = 'odd';

			$content .= '<tr class="'.$class.'">';
			if($altelo == $result[$x]['rating']) $content .= '<th class="col_0 col_first place"></th>';
			else $content .= '<th class="col_0 col_first place">'.($x+1).'</th>';
			$content .= '<td class="col_1 name">'.$result[$x]['name'].'</td>';
			$content .= '<td class="col_2 rating">'.$result[$x]['rating'].'</td>';
			$content .= '<td class="col_3 col_last games">'.$result[$x]['games'].'</td>';
			$content .= '</tr>';
			$altelo = $result[$x]['rating'];
		}
		$content .= '</table>';

		$this->Template->content = $content;

	}

}
