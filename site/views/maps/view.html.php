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
		$layout = JRequest::getVar('layout', 'default', 'method', 'word');
		switch($layout){
		case "ajax":
			$this->markers = $this->get('Markers');
			break;
		default:
			$this->map = $this->get('Map');
			$params = new JRegistry();
			$params->loadINI($this->map->attribs);
			$this->params = $params;
		}
		parent::display($tpl);
	}
}
?>
