<?php
/**
 * Google Maps View Controller
 *
 * @package		Maps
 * @subpackage	Components
 * @license		GNU/GPL
 */

// NO DIRECT ACCESS
defined( '_JEXEC' ) or die( 'Restricted access' );

// PRIVILEGE CHECK
if(!JFactory::getUser()->authorise('core.manage', 'com_maps')){
	return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
}

class MapsController extends JControllerLegacy
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	public function __construct($config = array())
	{
		parent::__construct();
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
