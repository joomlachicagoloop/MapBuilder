<?php
/**
 * Maps Helper
 * 
 * @package		Google Maps
 * @subpackage	Components
 * @license		GNU/GPL
 */

// NO DIRECT ACCESS
defined( '_JEXEC' ) or die( 'Restricted access' );

abstract Class MapsHelper {
	public static function addSubmenu($submenu){
		// ADD SUBMENU TABS
		JSubMenuHelper::addEntry(JText::_('COM_MAPS_SUBMENU_MAPS'), 'index.php?option=com_maps', $submenu == 'maps');
		JSubMenuHelper::addEntry(JText::_('COM_MAPS_SUBMENU_MARKERS'), 'index.php?option=com_maps&view=markers&layout=list', $submenu == 'markers');
	}
}