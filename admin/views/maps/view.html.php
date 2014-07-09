<?php
/**
 * Google Maps Map View
 * 
 * @package		Maps
 * @subpackage	Components
 * @license		GNU/GPL
 */

// NO DIRECT ACCESS
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

class MapsViewMaps extends JViewLegacy
{
	/**
	 * Maps view display method
	 * @return void
	 **/
	function display($tpl = null)
	{
		$input = JFactory::getApplication()->input;
		$layout = $input->get->get('layout', 'list', 'string');
		$this->setLayout($layout);
		switch($layout){
		case "list":
			JToolBarHelper::title(JText::_('COM_MAPS_VIEW_SUBTEXT_LIST_TITLE'), 'generic.png');
			JToolBarHelper::addNew('maps.add', 'JTOOLBAR_NEW');
			JToolBarHelper::editList('maps.edit', 'JTOOLBAR_EDIT', true);
			JToolBarHelper::deleteList(JText::_('COM_MAPS_MSG_DELETE_CONFIRM'), 'maps.delete', 'JTOOLBAR_DELETE', true);
			JToolBarHelper::preferences('com_maps', '500');
			// GET DATA FROM THE MODEL
			$this->filter = $this->get('State');
			$this->items = $this->get('List');
			$this->page = $this->get('Pagination');
			break;
		default:
			$input->set('hidemainmenu', 1);
			JToolBarHelper::title(JText::_('COM_MAPS_VIEW_SUBTEXT_EDIT_TITLE'), 'generic.png');
			JToolBarHelper::apply('maps.apply');
			JToolBarHelper::save('maps.save');
			JToolBarHelper::save2new('maps.save2new');
			JToolBarHelper::cancel('maps.cancel');
			$this->form = $this->get('Form');
			break;
		}
		parent::display($tpl);
	}
}