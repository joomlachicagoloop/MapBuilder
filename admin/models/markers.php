<?php
/**
 * Google Maps Model for Markers
 * 
 * @package    Google Maps
 * @subpackage Component
 * @license    GNU/GPL
 */
 
// CHECK TO ENSURE THIS FILE IS INCLUDED IN JOOMLA!
defined('_JEXEC') or die();
 
jimport( 'joomla.application.component.modelform' );
 
class MapsModelMarkers extends JModelForm
{
    /**
     * Markers data array
     * @var mixed This may be an object or array depending on context.
     */
    var $_data			= null;
    /**
     * Markers total
     * @var integer
     */
     var $_total		= null;
    /**
     * Markers Pagination object
     * @var object
     */
     var $_pagination	= null;
 
 	function __construct(){
 		parent::__construct();
  	}
    /**
     * Retrieves the Markers data
     * @return object A stdClass object containing the data for a single record.
     */
    public function getData()
    {
		$array 	= JRequest::getVar('cid',  0, '', 'array');
		$row 	= $this->getTable();
		$sql	= "SELECT m.*, map.*, map.`attribs` AS `maps` FROM `{$row->getTableName()}` m ".
		"LEFT JOIN `#__maps` map USING(`maps_id`) ".
		"WHERE `marker_id` = {$array[0]} LIMIT 1";
		$this->_db->setQuery($sql);
		$this->_data = $this->_db->loadObject();

        return $this->_data;
    }
	/**
	 * Method for getting the form from the model.
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 * @return  mixed  A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		if($form = $this->loadForm('com_maps.markers', 'markers', array('control'=>'', 'load_data'=>$loadData))){
			return $form;
		}
		JError::raiseError(0, JText::sprintf('JLIB_FORM_INVALID_FORM_OBJECT', 'markers'));
		return null;
	}
	/**
	 * Method to get the data that should be injected in the form.
	 * @return  array    The default data is an empty array.
	 */
	protected function loadFormData()
	{
		$db		= $this->getDbo();
		$array 	= JRequest::getVar('cid',  0, '', 'array');
		$id		= (int)$array[0];
		$row 	= $this->getTable();
		$sql	= "SELECT m.*, map.*, map.`attribs` AS `maps` FROM `{$row->getTableName()}` m ".
		"LEFT JOIN `#__maps` map USING(`maps_id`) ".
		"WHERE `marker_id` = {$id} LIMIT 1";
		$db->setQuery($sql);
		$data = $db->loadAssoc();
		$ini = new JRegistry();
		$ini->loadINI($data['attribs']);
		$data['params'] = $ini->toArray();

		return $data;
	}
    /**
     * Retrieves the Maps data for drop down list
     * @return array Array of objects containing the data from the database.
     */
    public function getMaps()
    {
    	$db = $this->getDbo();
    	$sql = "SELECT -1 AS `maps_id`, 'Select A Map...' AS `maps_name`, -1 AS `ordering` UNION SELECT maps_id, maps_name, `ordering` FROM #__maps ORDER BY `ordering` ASC";
    	$db->setQuery($sql);
    	$data = $db->loadObjectList();

    	return $data;
    }
    /**
     * Retrieves the Markers data
     * @return array Array of objects containing the data from the database.
     */
    public function getList()
    {
    	$mainframe = JFactory::getApplication();
    	$option = JRequest::getCmd('option', 'com_maps');
    	$scope = $this->getName();
    	$filter = array();
    	if($search = addslashes($mainframe->getUserState($option.'.'.$scope.'.filter_search'))){
    		$filter[] = "`marker_name` LIKE '%{$search}%'";
    	}
    	$map = $mainframe->getUserState($option.'.'.$scope.'.filter_map');
    	if($map != -1 && $map){
    		$filter[] = "m.`maps_id` = {$map}";
    	}
    	if(!$order_dir = $mainframe->getUserState($option.'.'.$scope.'.filter_order_Dir')){
    		$order_dir = "ASC";
    	}
    	if(!$ordering = $mainframe->getUserState($option.'.'.$scope.'.filter_order')){
    		$ordering = "map.`ordering` {$order_dir}, m.`ordering` {$order_dir}";
    	}elseif($ordering == "ordering"){
    		$ordering = "map.`ordering` {$order_dir}, m.`ordering` {$order_dir}";
    	}else{
    		$ordering = $ordering." ".$order_dir;
    	}
    	$row = $this->getTable();
		$sql = "SELECT ".
		"SQL_CALC_FOUND_ROWS m.*, map.*, m.ordering AS ordervalue, ".
		"v.`title` AS `access` ".
		"FROM `{$row->getTableName()}` m LEFT JOIN `#__maps` map USING(`maps_id`) ".
		"LEFT JOIN `#__viewlevels` v ON m.`access` = v.`id`";
		if(count($filter)){
			$sql .= " WHERE " . implode(" AND ", $filter);
		}
		$sql .= " ORDER BY {$ordering}";
		$this->_data = $this->_getList($sql, $this->getState('limitstart'), $this->getState('limit'));

    	return $this->_data;
    }
    /**
     * Retrieve filter variables from User State
     * @return object
     */
    public function getFilter()
    {
    	$mainframe = JFactory::getApplication();
    	$option = JRequest::getCmd('option', 'com_maps');
    	$scope = $this->getName();
    	$obj = new stdClass();
    	
 		$limit 					= $mainframe->getUserStateFromRequest($option.'.'.$scope.'.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
 		$limitstart 			= $mainframe->getUserStateFromRequest($option.'.'.$scope.'.limitstart', 'limitstart', 0, 'int');
  		$limitstart 			= ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
  		$this->setState('limit', $limit);
  		$this->setState('limitstart', $limitstart);
    	
    	$obj->filter_search			= $mainframe->getUserStateFromRequest($option.'.'.$scope.'.filter_search', 'filter_search', '', 'string');
    	$obj->filter_map			= $mainframe->getUserStateFromRequest($option.'.'.$scope.'.filter_map', 'filter_map', '-1', '-1', 'int');
    	$obj->filter_order			= $mainframe->getUserStateFromRequest($option.'.'.$scope.'.filter_order', 'filter_order', 'ordering', 'cmd');
    	$obj->filter_order_Dir		= $mainframe->getUserStateFromRequest($option.'.'.$scope.'.filter_order_Dir', 'filter_order_Dir', 'asc', 'string');
    	return $obj;
    }
    /**
     * Retrieves a JPagination object
     * @return object
     */
    public function getPagination()
    {
    	$this->_db->setQuery("SELECT FOUND_ROWS()");
    	jimport('joomla.html.pagination');
    	$this->_pagination = new JPagination($this->_db->loadResult(), $this->getState('limitstart'), $this->getState('limit'));
    
    	return $this->_pagination;
    }
}