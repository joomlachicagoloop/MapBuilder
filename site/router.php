<?php
/**
 * Router
 * 
 * @package		Google Maps
 * @subpackage	Component
 * @license		GNU/GPL
 */

// NO DIRECT ACCESS
defined( '_JEXEC' ) or die( 'Restricted access' );

function MapsBuildRoute(&$query){
	$mainframe = JFactory::getApplication();
	$menu = $mainframe->getMenu();
	$link = "index.php?option=com_maps&id={$query['id']}";
	$segments	= array();
	$segments[] = $query['id'];
	unset($query['id']);
	//$mainframe->close();
	if(empty($query['Itemid'])){
		$params = JComponentHelper::getParams('com_maps');
		$query['Itemid'] = $params->get('default_id');
	}else{
		$active = $menu->getActive();
		if($active->id == $query['Itemid']){
			$item = $menu->getItems('link', $link, true);
			if(!is_null($item)){
				$query['Itemid'] = $item->id;
				$segments = array();
			}else{
				$params = JComponentHelper::getParams('com_maps');
				$query['Itemid'] = $params->get('default_id');
			}
		}
	}
	return $segments;
}

function MapsParseRoute($segments){
	$query	= array();
	$query['view'] = 'maps';
	$query['id'] = array_shift(explode(":", $segments[0]));

	return $query;
}
