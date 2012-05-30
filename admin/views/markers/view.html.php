<?php
/**
 * Maps Markers View
 * 
 * @package		Maps
 * @subpackage	Components
 * @license		GNU/GPL
 */

// NO DIRECT ACCESS
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

class MapsViewMarkers extends JView
{
	/**
	 * Images view display method
	 * @return void
	 **/
	function display($tpl = null)
	{
		$layout = JRequest::getVar('layout', 'list', 'get');
		switch($layout){
		case "list":
			JToolBarHelper::title(JText::_('Manage Map Markers'), 'generic.png');
			JToolBarHelper::addNewX();
			JToolBarHelper::editListX();
			JToolBarHelper::deleteList();
			// GET DATA FROM THE MODEL
			$this->maps		= $this->get('Maps');
			$this->filter	= $this->get('Filter');
			$this->items	= $this->get('List');
			$this->page		= $this->get('Pagination');
			break;
		default:
			$component = JComponentHelper::getParams('com_maps');
			$api_key = $component->get('api_key');
			JToolBarHelper::title(JText::_('Edit Marker Details'), 'generic.png');
			JToolBarHelper::save();
			JToolBarHelper::apply();
			JToolBarHelper::cancel();
			JRequest::setVar('hidemainmenu', 1);
			$this->data = $this->get('Data');
			$this->form = $this->get('Form');
			break;
		}
		parent::display($tpl);
	}
}