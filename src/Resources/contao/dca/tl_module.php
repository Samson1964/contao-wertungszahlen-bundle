<?php
/**
 * Avatar for Contao Open Source CMS
 *
 * Copyright (C) 2013 Kirsten Roschanski
 * Copyright (C) 2013 Tristan Lins <http://bit3.de>
 *
 * @package    Avatar
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Add palette to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['wertungszahlen_bestenliste'] = '{title_legend},name,headline,type;{options_legend},wertungszahlen_liste,wertungszahlen_topcount;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';
$GLOBALS['TL_DCA']['tl_module']['palettes']['wertungszahlen_suche']       = '{title_legend},name,headline,type;{options_legend},wertungszahlen_liste;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

$GLOBALS['TL_DCA']['tl_module']['fields']['wertungszahlen_topcount'] = array
(
	'label'                              => &$GLOBALS['TL_LANG']['tl_module']['wertungszahlen_topcount'],
	'default'                            => 0,
	'exclude'                            => true,
	'inputType'                          => 'text',
	'eval'                               => array('tl_class'=>'w50', 'rgxp'=>'digit', 'maxlength'=>6),
	'sql'                                => "varchar(6) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['wertungszahlen_liste'] = array
(
	'label'                              => &$GLOBALS['TL_LANG']['tl_module']['wertungszahlen_liste'],
	'exclude'                            => true,
	'options_callback'                   => array('tl_module_wertungszahlen', 'getListe'),
	'inputType'                          => 'select',
	'eval'                               => array
	(                                    
		'includeBlankOption'             => true,
		'mandatory'                      => false,
		'multiple'                       => false,
		'chosen'                         => true,
		'tl_class'                       => 'w50'
	),
	'sql'                                => "int(10) unsigned NOT NULL default '0'"
);

/*****************************************
 * Klasse tl_content_championslist
 *****************************************/

class tl_module_wertungszahlen extends \Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}

	public function getListe(DataContainer $dc)
	{
		$array = array();
		$objListe = $this->Database->prepare("SELECT * FROM tl_wertungszahlen ORDER BY listmonth DESC")
		                           ->execute();

		while($objListe->next())
		{
			$array[$objListe->id] = $objListe->title;
		}
		return $array;

	}

}
