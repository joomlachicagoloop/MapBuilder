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
			$filter =& $this->get('Filter');
			$this->assignRef('filter', $filter);
			$items =& $this->get('List');
			$this->assignRef('items', $items);
			$page =& $this->get('Pagination');
			$this->assignRef('page', $page);
			break;
		default:
			$component = JComponentHelper::getParams('com_maps');
			$api_key = $component->get('api_key');
			$document =& JFactory::getDocument();
			$document->addScript("http://maps.google.com/maps/api/js?sensor=false&amp;key={$api_key}");
			$document->addScript("components".DS."com_maps".DS."javascript".DS."markers.js");
			JToolBarHelper::title(JText::_('Edit Marker Details'), 'generic.png');
			JToolBarHelper::save();
			JToolBarHelper::apply();
			JToolBarHelper::cancel();
			JRequest::setVar('hidemainmenu', 1);
			$data = & $this->get('Data');
			$this->assignRef('data', $data);
			$paramsdefs = JPATH_COMPONENT.DS."models".DS."markers.xml";
			$params = new JParameter($data->attribs, $paramsdefs);
			$maps = new JParameter($data->maps);
			$params->merge($maps);
			$params->set('published', $data->published);
			$params->set('access', $data->access);
			$params->set('maps_id', $data->maps_id);
			$params->set('marker_lat', $data->marker_lat);
			$params->set('marker_lng', $data->marker_lng);
			$this->assignRef('params', $params);
			break;
		}
		parent::display($tpl);
	}
}