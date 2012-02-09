<?php
/**
 * Google Maps Controller
 * 
 * @package		Google Maps
 * @subpackage 	Components
 * @license		GNU/GPL
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class MapsController extends JController
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS."tables");
 		jimport('joomla.filesystem.file');
		parent::__construct();
	}

	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	function display()
	{
		$document		=& JFactory::getDocument();
		$view_type		= $document->getType();
		$view_name		= JRequest::getCmd('view', $this->getName());
		$view_layout	= JRequest::getCmd('layout', 'default');
		$user			= JFactory::getUser();

		// ASSIGN THE DEFAULT MODEL TO ALL VIEWS
		$view =& $this->getView($view_name, $view_type, '', array('base_path'=>$this->_basePath));
		$model =& $this->getModel();
		$view->setModel($model, true);
		
		parent::display();
	}
}