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
 * BACK END MODULES
 *
 * Back end modules are stored in a global array called "BE_MOD". You can add
 * your own modules by adding them to the array.
 *
 * Not all of the keys mentioned above (like "tables", "key", "callback" etc.)
 * have to be set. Take a look at the system/modules/core/config/config.php
 * file to see how back end modules are configured.
 */

/**
 * Backend-Module
 */
$GLOBALS['BE_MOD']['content']['wertungszahlen'] = array
(
	'tables'                  => array('tl_wertungszahlen', 'tl_wertungszahlen_players', 'tl_wertungszahlen_ratings'),
);


/**
 * Frontend-Module
 */

$GLOBALS['FE_MOD']['elo'] = array
(
	'wertungszahlen_bestlist' => 'Schachbulle\ContaoWertungszahlenBundle\Classes\Bestenliste',
	'wertungszahlen_suche'    => 'Schachbulle\ContaoWertungszahlenBundle\Classes\Suche',
);
