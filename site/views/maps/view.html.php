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
			$this->markers = $this->get('Markers');
			break;
		default:
			$document->addScript($uri->root()."components/com_maps/javascript/maps.js");
			$paramsdef = JPATH_COMPONENT_ADMINISTRATOR.DS."models".DS."maps.xml";
			$this->map = $this->get('Map');
			$params = new JRegistry();
			$params->loadJSON($map->attribs);
			$this->params = $params;
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
