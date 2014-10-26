<?php
/**
 * Google Maps Default View
 * 
 * @package		Google Maps
 * @subpackage	Components
 * @license		GNU/GPL
 */

jimport( 'joomla.application.component.view');

class MapBuilderViewMaps extends JViewLegacy
{
	function display($tpl = null)
	{
		$layout = JFactory::getApplication()->input->get('layout', 'default', 'word');
		switch($layout){
		case "ajax":
			$this->markers = $this->get('Markers');
			break;
		case "submit":
			$this->form = $this->get('Form');
			$this->map = $this->get('Map');
			$params = new JRegistry();
			$params->loadString($this->map->attribs);
			$this->params = $params;
			break;
		default:
			$this->map = $this->get('Map');
			$params = new JRegistry();
			$params->loadString($this->map->attribs);
			$this->params = $params;
		}
		parent::display($tpl);
	}
}
?>
