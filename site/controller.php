<?php
/**
 * MapBuilder Controller
 * 
 * @package		MapBuilder
 * @subpackage 	Components
 * @license		GNU/GPL
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class MapBuilderController extends JControllerLegacy
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	public function __construct($config = array())
	{
		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS."tables");
 		jimport('joomla.filesystem.file');
		parent::__construct(array('default_view'=>'maps'));
	}
}
