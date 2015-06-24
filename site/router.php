<?php
/**
 * Router
 * 
 * @package		MapBuilder
 * @subpackage	Component
 * @license		GNU/GPL
 */

// NO DIRECT ACCESS
defined( '_JEXEC' ) or die( 'Restricted access' );

function MapBuilderBuildRoute(&$query){
	$segments	= array();
	$link = array();
	if(!empty($query['view'])){
		$segments[] = $query['view'];
		$link[] = "view=".$query['view'];
		unset($query['view']);
	}
	if(!empty($query['layout'])){
		$segments[] = $query['layout'];
		$link[] = "layout=".$query['layout'];
		unset($query['layout']);
	}
	if(!empty($query['id'])){
		$segments[] = $query['id'];
		$link[] = "id=".$query['id'];
		unset($query['id']);
	}
	if(empty($query['Itemid'])){
		$params = JComponentHelper::getParams('com_mapbuilder');
		$query['Itemid'] = $params->get('params.default_id');
	}else{
		$menu = JFactory::getApplication()->getMenu();
		$item = $menu->getItem($query['Itemid']);
		if($item->link == ("index.php?option=com_mapbuilder&".implode("&", $link))){
			return array();
		}
	}
	return $segments;
}

function MapBuilderParseRoute($segments){
	$query	= array();
	$query['view'] = $segments[0];
	$query['layout'] = $segments[1];
	$parts = explode(":", $segments[2]);
	$query['id'] = array_shift($parts);

	return $query;
}
