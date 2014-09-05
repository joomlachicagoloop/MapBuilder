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

abstract Class MapBuilderHelper {
	public static function addSubmenu($submenu){
		// ADD SUBMENU TABS
		if(JFactory::getApplication()->getTemplate() == "isis"){
            JHtmlSidebar::addEntry(JText::_('COM_MAPBUILDER_SUBMENU_MAPS'), 'index.php?option=com_mapbuilder', $submenu == 'maps');
            JHtmlSidebar::addEntry(JText::_('COM_MAPBUILDER_SUBMENU_MARKERS'), 'index.php?option=com_mapbuilder&view=markers&layout=list', $submenu == 'markers');
		}else{
            JSubMenuHelper::addEntry(JText::_('COM_MAPBUILDER_SUBMENU_MAPS'), 'index.php?option=com_mapbuilder', $submenu == 'maps');
            JSubMenuHelper::addEntry(JText::_('COM_MAPBUILDER_SUBMENU_MARKERS'), 'index.php?option=com_mapbuilder&view=markers&layout=list', $submenu == 'markers');
	    }
	}
}
