<?php
/**
 * MapBuilder View Controller
 *
 * @package		MapBuilder
 * @subpackage	Components
 * @license		GNU/GPL
 */

// NO DIRECT ACCESS
defined( '_JEXEC' ) or die( 'Restricted access' );

// PRIVILEGE CHECK
if(!JFactory::getUser()->authorise('core.manage', 'com_mapbuilder')){
	return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
}

class MapBuilderController extends JControllerLegacy
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	public function __construct($config = array())
	{
		parent::__construct(array('default_view'=>'maps'));
	}
	
	/**
	 * Medthod to display the correct view and layout
	 *
	 * @return  JController  A JController object to support chaining.
	 */
	public function display($cachable = false, $urlparams = false)
	{
		parent::display($cachable, $urlparams);
	}
}
