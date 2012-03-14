<?php
/**
 * Maps View For All Layouts
 * 
 * @package		Maps
 * @subpackage	Components
 * @license		GNU/GPL
 */

// NO DIRECT ACCESS
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

class MapsViewMaps extends JView
{
	/**
	 * Maps view display method
	 * @return void
	 **/
	function display($tpl = null)
	{
		$layout	= JRequest::getVar('layout', 'list', 'get');
		switch($layout){
		case "list":
			JToolBarHelper::title(JText::_('Manage Google Maps'), 'generic.png');
			JToolBarHelper::addNewX();
			JToolBarHelper::editListX();
			JToolBarHelper::deleteList();
			JToolBarHelper::preferences('com_maps', '500');
			// GET DATA FROM THE MODEL
			$this->filter = $this->get('Filter');
			$this->items = $this->get('List');
			$this->page = $this->get('Pagination');
			break;
		default:
			$component = JComponentHelper::getParams('com_maps');
			$api_key = $component->get('api_key');
			$document =& JFactory::getDocument();
			$document->addScript("http://maps.google.com/maps/api/js?sensor=false&amp;key={$api_key}");
			$document->addScript("components".DS."com_maps".DS."javascript".DS."maps.js");
			$bar =& JToolBar::getInstance('toolbar');
			JToolBarHelper::title(JText::_('Edit Google Maps Details'), 'generic.png');
			JToolBarHelper::save();
			JToolBarHelper::apply();
			JToolBarHelper::cancel();
			JRequest::setVar('hidemainmenu', 1);
			$this->data = $this->get('Data');
			$this->contact = $this->get('Contact');
			$paramsdefs = JPATH_COMPONENT.DS."models".DS."maps.xml";
			$params = new JRegistry();
			$params->loadJSON($data->attribs);
			$params->set('published', $data->published);
			$params->set('access', $data->access);
			$params->set('meta_keywords', $data->meta_keywords);
			$params->set('meta_description', $data->meta_description);
			$this->assignRef('params', $params);
			break;
		}
		parent::display($tpl);
	}
}