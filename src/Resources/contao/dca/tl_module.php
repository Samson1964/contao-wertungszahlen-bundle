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
$GLOBALS['TL_DCA']['tl_module']['palettes']['wertungszahlen_bestlist'] = '{title_legend},name,headline,type;{options_legend},wertungszahlen_fromdate,wertungszahlen_todate,wertungszahlen_min,wertungszahlen_gender,wertungszahlen_topcount;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';
$GLOBALS['TL_DCA']['tl_module']['palettes']['wertungszahlen_suche']    = '{title_legend},name,headline,type;{options_legend},wertungszahlen_topx,wertungszahlen_gender,wertungszahlen_fromdate,wertungszahlen_todate;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

$GLOBALS['TL_DCA']['tl_module']['fields']['wertungszahlen_topcount'] = array
(
	'label'                              => &$GLOBALS['TL_LANG']['tl_module']['wertungszahlen_topcount'],
	'default'                            => 30,
	'exclude'                            => true,
	'inputType'                          => 'text',
	'eval'                               => array('tl_class'=>'w50', 'rgxp'=>'digit', 'maxlength'=>6),
	'sql'                                => "varchar(6) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['wertungszahlen_fromdate'] = array
(
	'label'                              => &$GLOBALS['TL_LANG']['tl_module']['wertungszahlen_fromdate'],
	'exclude'                            => true,
	'inputType'                          => 'text',
	'eval'                               => array('tl_class'=>'w50 clr', 'rgxp'=>'digit', 'maxlength'=>6),
	'sql'                                => "varchar(6) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['wertungszahlen_todate'] = array
(
	'label'                              => &$GLOBALS['TL_LANG']['tl_module']['wertungszahlen_todate'],
	'exclude'                            => true,
	'inputType'                          => 'text',
	'eval'                               => array('tl_class'=>'w50', 'rgxp'=>'digit', 'maxlength'=>6),
	'sql'                                => "varchar(6) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['wertungszahlen_min'] = array
(
	'label'                              => &$GLOBALS['TL_LANG']['tl_module']['wertungszahlen_min'],
	'exclude'                            => true,
	'inputType'                          => 'text',
	'eval'                               => array('tl_class'=>'w50', 'rgxp'=>'digit', 'maxlength'=>4),
	'sql'                                => "varchar(4) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['wertungszahlen_gender'] = array
(
	'label'                              => &$GLOBALS['TL_LANG']['tl_module']['wertungszahlen_gender'],
	'exclude'                            => true,
	'default'                            => 'M',
	'inputType'                          => 'select',
	'options'                            => $GLOBALS['TL_LANG']['tl_module']['wertungszahlen_gender_options'],
	'eval'                               => array('tl_class'=>'w50'),
	'sql'                                => "char(1) NOT NULL default 'M'"
);

// Anzahl der Top-Plätze, die angezeigt werden sollen (max. 9)
$GLOBALS['TL_DCA']['tl_module']['fields']['wertungszahlen_topx'] = array
(
	'label'                              => &$GLOBALS['TL_LANG']['tl_module']['wertungszahlen_topx'],
	'exclude'                            => true,
	'inputType'                          => 'text',
	'default'                            => 3,
	'eval'                               => array
	(
		'tl_class'                       => 'w50',
		'rgxp'                           => 'digit',
		'maxlength'                      => 1
	),
	'sql'                                => "int(1) unsigned NOT NULL default 3"
);
