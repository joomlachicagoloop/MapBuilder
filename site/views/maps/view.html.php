<?php
/**
 * Google Maps Default View
 * 
 * @package		Google Maps
 * @subpackage	Components
 * @license		GNU/GPL
 */


jimport( 'joomla.application.component.view');

class MapsViewMaps extends JView
{
	function display($tpl = null)
	{
		$uri		=& JFactory::getURI();
		$document	=& JFactory::getDocument();
		$layout		= JRequest::getVar('layout', 'default', 'method', 'word');
		switch($layout){
		case "ajax":
			$markers = $this->get('Markers');
			$this->assignRef('markers', $markers);
			break;
		default:
			$document->addScript($uri->root()."components/com_maps/javascript/maps.js");
			$paramsdef = JPATH_COMPONENT_ADMINISTRATOR.DS."models".DS."maps.xml";
			$map = $this->get('Map');
			$this->assignRef('map', $map);
			$params = new JParameter($map->attribs, $paramsdef);
			$this->assignRef('params', $params);
			if(trim($map->meta_keywords)){
				$document->setMetaData('keywords', $map->meta_keywords);
			}
			if(trim($map->meta_description)){
				$document->setMetaData('description', $map->meta_description);
			}
		}
		parent::display($tpl);
	}
}
?>
