<?php
// NO DIRECT ACCESS
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableMaps extends JTable
{
	/** @var int Primary Key */
	var $maps_id			= null;
	/** @var string SEO Alias */
	var $maps_alias			= null;
	/** @var string Maps Name */
	var $maps_name			= null;
	/** @var string Maps Description */
	var $maps_description	= null;
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

	function TableMaps(&$db){
		parent::__construct('#__maps', 'maps_id', $db);
	}
	
	function bind($array, $ignore=''){
		if(key_exists('params', $array)){
			if(is_array($array['params'])){
				$registry = new JRegistry();
				$registry->loadArray($array['params']);
				$array['attribs'] = $registry->toString('INI');
			}
		}
		return parent::bind($array, $ignore);
	}
}
?>