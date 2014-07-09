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

class MapsViewMarkers extends JViewLegacy
{
	/**
	 * Markers view display method
	 * @return void
	 **/
	function display($tpl = null)
	{
		$input = JFactory::getApplication()->input;
		$layout = $input->get->get('layout', 'list', 'string');
		$this->setLayout($layout);
		switch($layout){
		case "list":
		    MapsHelper::addSubmenu('markers');
			JToolBarHelper::title(JText::_('Manage Map Markers'), 'generic.png');
			JToolBarHelper::addNew('markers.add', 'JTOOLBAR_NEW');
			JToolBarHelper::editList('markers.edit', 'JTOOLBAR_EDIT', true);
			JToolBarHelper::deleteList(JText::_('COM_MAPS_MSG_DELETE_CONFIRM'), 'markers.delete', 'JTOOLBAR_DELETE', true);
			// GET DATA FROM THE MODEL
			$this->maps		= $this->get('Maps');
			$this->filter	= $this->get('State');
			$this->items	= $this->get('List');
			$this->page		= $this->get('Pagination');
			break;
		default:
			$input->set('hidemainmenu', 1);
			JToolBarHelper::title(JText::_('Edit Marker Details'), 'generic.png');
			JToolBarHelper::apply('markers.apply');
			JToolBarHelper::save('markers.save');
			JToolBarHelper::save2new('markers.save2new');
			JToolBarHelper::cancel('markers.cancel');
			$this->data = $this->get('Data');
			$this->form = $this->get('Form');
			break;
		}
		parent::display($tpl);
	}
}
