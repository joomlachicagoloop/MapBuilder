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
	if(!empty($segments['view'])){
		$segments[] = $query['view'];
		unset($query['view']);
	}
	if(!empty($segments['layout'])){
		$segments[] = $query['layout'];
		unset($query['layout']);
	}
	if(!empty($segments['id'])){
		$segments[] = $query['id'];
		unset($query['id']);
	}
	if(empty($query['Itemid'])){
		$params = JComponentHelper::getParams('com_mapbuilder');
		$query['Itemid'] = $params->get('params.default_id');
	}
	return $segments;
}

function MapBuilderParseRoute($segments){
	$query	= array();
	$query['view'] = $segments[0];
	$query['layout'] = $segments[1];
	$query['id'] = array_shift(explode(":", $segments[2]));

	return $query;
}
