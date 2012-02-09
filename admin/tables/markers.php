<?php
// NO DIRECT ACCESS
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableMarkers extends JTable
{
	/** @var int Primary Key */
	var $marker_id			= null;
	/** @var string Category Name */
	var $marker_name		= null;
	/** @var string SEO Alias */
	var $marker_alias		= null;
	/** @var float Marker Latitude */
	var $marker_lat			= null;
	/** @var float Marker Longitude */
	var $marker_lng			= null;
	/** @var string Info Window Content */
	var $marker_description	= null;
	/** @var string Text field for storage of JParameter */
	var $attribs			= null;
	/** @var int */
	var $ordering			= null;
	/** @var int */
	var $published			= null;
	/** @var int */
	var $checked_out		= null;
	/** @var time */
	var $checked_out_time	= null;
	/** @var int */
	var $access				= null;
	/** @var int */
	var $maps_id			= null;
	
	function TableMarkers(&$db){
		parent::__construct('#__maps_marker', 'marker_id', $db);
	}
	
	function bind($array, $ignore=''){
		if(key_exists('options', $array)){
			if(is_array($array['options'])){
				if(!parent::bind($array['options'], $ignore)){
					return false;
				}
			}
		}
		return parent::bind($array, $ignore);
	}
}
?>