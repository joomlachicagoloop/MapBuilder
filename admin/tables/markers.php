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
	var $map_id				= null;
	
	public function __construct(&$db){
		parent::__construct('#__mapbuilder_markers', 'marker_id', $db);
	}
	
	public function bind($array, $ignore=''){
		if(key_exists('params', $array)){
			if(is_array($array['params'])){
				$registry = new JRegistry();
				$registry->loadArray($array['params']);
				$array['attribs'] = $registry->toString();
			}
		}
		return parent::bind($array, $ignore);
	}
	
	public function check(){
		// ASSIGN ORDERING IF NECESSARY
		if(is_null($this->ordering)){
			$this->ordering = $this->getNextOrder("map_id = {$this->map_id}");
		}
		return true;
	}
	
	public function store($updateNulls = false){
		if(!parent::store($updateNulls)){
			return false;
		}
		$this->reorder("map_id = {$this->map_id}");
		return true;
	}
}
