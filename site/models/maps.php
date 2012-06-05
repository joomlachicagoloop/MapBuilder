<?php
/**
 * Google Maps Model
 * 
 * @package		Google Maps
 * @subpackage	Component
 * @license		GNU/GPL
 */

// CHECK TO ENSURE THIS FILE IS INCLUDED IN JOOMLA!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

class MapsModelMaps extends JModel
{
	function __construct(){
		$user	=& JFactory::getUser();
		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS."tables");
		parent::__construct();
		$this->setState('user', $user);

	}
	/**
	 * Retrieve the default data for all published categories with appropriate user access.
	 * @return array An array of object based results from the database
	 */
	function getMarkers(){
		$user	= $this->getState('user');
		$levels = implode(",", array_unique($user->getAuthorisedViewLevels()));
		$id		= JRequest::getInt('id', 0, 'get');
		$table	=& $this->getTable('Markers');
		$sql 	= "SELECT `marker_id` ".
		"FROM `#__maps_marker` ".
		"WHERE `maps_id` = {$id} AND `published` = 1 AND `access` IN ({$levels}) ".
		"ORDER BY `ordering`";
		$this->_db->setQuery($sql);
		if($result = $this->_db->loadResultArray()){
			$data = "";
			foreach($result as $mid){
				$table->load($mid);
				$data .= $table->toXML();
			}
			return $data;
		}else{
			echo "<error></error>";
			return false;
		}
	}
	/**
	 * Retrieve the maps listing data for the A-Z listing layout.
	 * @return array An array of object based results from the database
	 */
	function getMap(){
		$user	= $this->getState('user');
		$id		= JRequest::getInt('id', 0);
		$table  =& $this->getTable();
		$table->load($id);
		return $table;
	}
}
