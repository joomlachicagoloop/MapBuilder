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
	$segments	= array();
	if(!empty($segments['layout'])){
		$segments[] = $query['layout'];
		unset($query['layout']);
	}
	if(!empty($segments['id'])){
		$segments[] = $query['id'];
		unset($query['id']);
	}
	if(empty($query['Itemid'])){
		$params = JComponentHelper::getParams('com_maps');
		$query['Itemid'] = $params->get('params.default_id');
	}
	return $segments;
}

function MapsParseRoute($segments){
	$query	= array();
	$query['layout'] = $segments[0];
	$query['id'] = array_shift(explode(":", $segments[1]));

	return $query;
}
