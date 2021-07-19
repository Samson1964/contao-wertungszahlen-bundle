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
 * Table tl_wertungszahlen_players
 */
$GLOBALS['TL_DCA']['tl_wertungszahlen_players'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'             => 'Table',
		'ctable'                    => array('tl_wertungszahlen_ratings'),
		'enableVersioning'          => true,
		'sql' => array
		(
			'keys' => array
			(
				'id'                            => 'primary',
				'pid'                           => 'index',
			)
		)
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 1,
			'fields'                  => array('lastname'),
			'flag'                    => 1,
			'headerFields'            => array('title', 'datum'), 
			'panelLayout'             => 'sort,filter;search,limit',
		),
		'label' => array
		(
			'fields'                  => array('lastname', 'firstname'),
			'showColumns'             => true,
			'format'                  => '%s'
		),
		'global_operations' => array
		(
			'listen' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_wertungszahlen_players']['listen'],
				'href'                => 'table=tl_wertungszahlen',
				'icon'                => 'bundles/contaowertungszahlen/images/turniere.png',
				'attributes'          => 'onclick="Backend.getScrollOffset();"'
			),
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset();" accesskey="e"'
			)
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_wertungszahlen_players']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_wertungszahlen_players']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.gif'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_wertungszahlen_players']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_wertungszahlen_players']['toggle'],
				'icon'                => 'visible.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'     => array('tl_wertungszahlen_players', 'toggleIcon')
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_wertungszahlen_players']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)
		)
	),

	// Select
	'select' => array
	(
		'buttons_callback' => array()
	),

	// Edit
	'edit' => array
	(
		'buttons_callback' => array()
	),

	// Palettes
	'palettes' => array
	(
		'__selector__'                => array(''),
		'default'                     => '{name_legend},lastname,firstname,title,birthday,sex,country;{member_legend},memberId;{publish_legend},published'
	),

	// Subpalettes
	'subpalettes' => array
	(
		''                            => ''
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'memberId' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_wertungszahlen_players']['memberId'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'mandatory'           => false, 
				'maxlength'           => 16,
				'tl_class'            => 'w50'
			),
			'sql'                     => "int(16) unsigned NOT NULL default '0'"
		),
		'lastname' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_wertungszahlen_players']['lastname'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'mandatory'           => false, 
				'maxlength'           => 64,
				'tl_class'            => 'w50'
			),
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'firstname' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_wertungszahlen_players']['firstname'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'mandatory'           => false, 
				'maxlength'           => 64,
				'tl_class'            => 'w50'
			),
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'country' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_wertungszahlen_players']['country'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'mandatory'           => false, 
				'maxlength'           => 3,
				'tl_class'            => 'w50'
			),
			'sql'                     => "varchar(3) NOT NULL default ''"
		),
		'sex' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_wertungszahlen_players']['sex'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'mandatory'           => false, 
				'maxlength'           => 1,
				'tl_class'            => 'w50'
			),
			'sql'                     => "varchar(1) NOT NULL default ''"
		),
		'title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_wertungszahlen_players']['title'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'mandatory'           => false, 
				'maxlength'           => 16,
				'tl_class'            => 'w50 clr'
			),
			'sql'                     => "varchar(16) NOT NULL default ''"
		),
		'birthday' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_wertungszahlen_players']['birthday'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'mandatory'           => false, 
				'maxlength'           => 8,
				'tl_class'            => 'w50'
			),
			'sql'                     => "int(8) unsigned NOT NULL default '0'"
		),
		'published' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_wertungszahlen_players']['published'],
			'exclude'                 => true,
			'search'                  => false,
			'sorting'                 => false,
			'filter'                  => true,
			'inputType'               => 'checkbox',
			'eval'                    => array
			(
				'tl_class'            => 'w50',
				'isBoolean'           => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		), 
	)
);

/**
 * Provide miscellaneous methods that are used by the data configuration array
 */
class tl_wertungszahlen_players extends Backend
{
	 
	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}

	/**
	 * Ändert das Aussehen des Toggle-Buttons.
	 * @param $row
	 * @param $href
	 * @param $label
	 * @param $title
	 * @param $icon
	 * @param $attributes
	 * @return string
	 */
	public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
	{
		$this->import('BackendUser', 'User');
		
		if (strlen($this->Input->get('tid')))
		{
			$this->toggleVisibility($this->Input->get('tid'), ($this->Input->get('state') == 0));
			$this->redirect($this->getReferer());
		}
		
		// Check permissions AFTER checking the tid, so hacking attempts are logged
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_wertungszahlen_players::published', 'alexf'))
		{
			return '';
		}
		
		$href .= '&amp;id='.$this->Input->get('id').'&amp;tid='.$row['id'].'&amp;state='.$row[''];
		
		if (!$row['published'])
		{
			$icon = 'invisible.gif';
		}
		
		return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ';
	}

	/**
	 * Toggle the visibility of an element
	 * @param integer
	 * @param boolean
	 */
	public function toggleVisibility($intId, $blnPublished)
	{
		// Check permissions to publish
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_wertungszahlen_players::published', 'alexf'))
		{
			$this->log('Not enough permissions to show/hide record ID "'.$intId.'"', 'tl_wertungszahlen_players toggleVisibility', TL_ERROR);
			$this->redirect('contao/main.php?act=error');
		}
		
		$this->createInitialVersion('tl_wertungszahlen_players', $intId);
		
		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_wertungszahlen_players']['fields']['published']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_wertungszahlen_players']['fields']['published']['save_callback'] as $callback)
			{
				$this->import($callback[0]);
				$blnPublished = $this->$callback[0]->$callback[1]($blnPublished, $this);
			}
		}
		
		// Update the database
		$this->Database->prepare("UPDATE tl_wertungszahlen_players SET tstamp=". time() .", published='" . ($blnPublished ? '' : '1') . "' WHERE id=?")
		               ->execute($intId);
		$this->createNewVersion('tl_wertungszahlen_players', $intId);
	}

}
