<?php
// NO DIRECT ACCESS
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableMaps extends JTable
{
	/** @var int Primary Key */
	var $map_id			= null;
	/** @var string SEO Alias */
	var $map_alias			= null;
	/** @var string Maps Name */
	var $map_name			= null;
	/** @var string Maps Description */
	var $map_description	= null;
	/** @var string Text field for storage of JParameter */
	var $attribs			= null;
	/** @var string Meta Description */
	var $meta_description	= null;
	/** @var string Meta Keywords */
	var $meta_keywords		= null;
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

	public function __construct(&$db){
		parent::__construct('#__mapbuilder_maps', 'map_id', $db);
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
			$this->ordering = $this->getNextOrder();
		}
		return true;
	}
	
	public function store($updateNulls = false){
		if(!parent::store($updateNulls)){
			return false;
		}
		return true;
	}
}
