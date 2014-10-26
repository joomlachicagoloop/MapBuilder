<?php
/**
 * MapBuilder Model
 * 
 * @package		MapBuilder
 * @subpackage	Component
 * @license		GNU/GPL
 */

// CHECK TO ENSURE THIS FILE IS INCLUDED IN JOOMLA!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.modelform' );

class MapBuilderModelMaps extends JModelForm
{
	public function __construct(){
		$user	= JFactory::getUser();
		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS."tables");
		parent::__construct();
		$this->setState('user', $user);

	}
	/**
	 * Retrieve the default data for all published categories with appropriate user access.
	 * @return array An array of object based results from the database
	 */
	public function getMarkers(){
		$user	= $this->getState('user');
		$levels = implode(",", array_unique($user->getAuthorisedViewLevels()));
		$id		= JRequest::getInt('id', 0, 'get');
		$table	= $this->getTable('Markers');
		$sql    = $this->_db->getQuery(true);
		
		$sql->select("*");
		$sql->from($this->_db->quoteName($table->getTableName()));
		$sql->where("`map_id` = {$id}");
		$sql->where("`published` = 1");
		$sql->where("`access` IN ({$levels})");
		$sql->order("`ordering`");
		$this->_db->setQuery($sql);
		if($result = $this->_db->loadObjectList()){
			return $result;
		}else{
			return false;
		}
	}
	/**
	 * Retrieve the maps listing data for the A-Z listing layout.
	 * @return array An array of object based results from the database
	 */
	public function getMap(){
		$user	= $this->getState('user');
		$id		= JRequest::getInt('id', 0);
		$table  = $this->getTable();
		$table->load($id);
		return $table;
	}
	
	public function getForm($data = array(), $loadData = true){
		if($form = $this->loadForm('com_mapbuilder.maps', 'maps', array('control'=>'jform', 'load_data'=>$loadData))){
			return $form;
		}
		throw new InvalidArgumentException(JText::sprintf('JLIB_FORM_INVALID_FORM_OBJECT', 'maps'));
		return null;
	}
}
