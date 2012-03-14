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
			$this->filter = $this->get('Filter');
			$this->items = $this->get('List');
			$this->page = $this->get('Pagination');
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
			$this->data = $this->get('Data');
			$paramsdefs = JPATH_COMPONENT.DS."models".DS."markers.xml";
			$params = new JRegistry();
			$params->loadJSON($data->attribs);
			$maps = new JRegistry();
			$maps->loadJSON($data->maps);
			$params->merge($maps);
			$params->set('published', $data->published);
			$params->set('access', $data->access);
			$params->set('maps_id', $data->maps_id);
			$params->set('marker_lat', $data->marker_lat);
			$params->set('marker_lng', $data->marker_lng);
			$this->params = $params;
			break;
		}
		parent::display($tpl);
	}
}