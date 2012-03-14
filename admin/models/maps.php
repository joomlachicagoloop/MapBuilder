<?php
/**
 * Maps Model
 * 
 * @package    Google Maps
 * @subpackage Component
 * @license    GNU/GPL
 */
 
// CHECK TO ENSURE THIS FILE IS INCLUDED IN JOOMLA!
defined('_JEXEC') or die();
 
jimport( 'joomla.application.component.model' );
 
class MapsModelMaps extends JModel
{
    /**
     * Products data array
     * @var mixed This may be an object or array depending on context.
     */
    var $_data			= null;
    /**
     * Products total
     * @var integer
     */
     var $_total		= null;
    /**
     * Products Pagination object
     * @var object
     */
     var $_pagination	= null;
 
 	function __construct(){
 		parent::__construct();
  	}
    /**
     * Retrieves the Maps data
     * @return object A stdClass object containing the data for a single record.
     */
    function getData()
    {
		$array 	= JRequest::getVar('cid',  0, '', 'array');
		$row 	=& $this->getTable();
		$row->load($array[0]);
		$this->_data = $row;

        return $this->_data;
    }
    /**
     * Retrieves the Products data
     * @return array Array of objects containing the data from the database.
     */
    function getList()
    {
    	global $mainframe, $option;
    	$scope = $this->getName();
    	$filter = array();
    	if($search = addslashes($mainframe->getUserState($option.'.'.$scope.'.filter_search'))){
    		$filter[] = "a.`maps_name` LIKE '%{$search}%'";
    	}
    	if(!$ordering = $mainframe->getUserState($option.'.'.$scope.'.filter_order')){
    		$ordering = "a.`ordering`";
    	}
    	if(!$order_dir = $mainframe->getUserState($option.'.'.$scope.'.filter_order_Dir')){
    		$order_dir = "ASC";
    	}
    	$row =& $this->getTable();
		$sql = "SELECT ".
		"SQL_CALC_FOUND_ROWS a.*, ".
		"g.`name` AS `groupname` ".
		"FROM `{$row->getTableName()}` a ".
		"LEFT JOIN `#__groups` g ON a.`access` = g.`id`";
		if(count($filter)){
			$sql .= " WHERE " . implode(" AND ", $filter);
		}
		$sql .= " ORDER BY {$ordering} {$order_dir}";
		$this->_data = $this->_getList($sql, $this->getState('limitstart'), $this->getState('limit'));
    	
    	return $this->_data;
    }
    /**
     * Retrieve filter variables from User State
     * @return object
     */
    function getFilter()
    {
    	global $mainframe, $option;
    	$scope = $this->getName();
    	$obj = new stdClass();
    	
 		$limit 					= $mainframe->getUserStateFromRequest($option.'.'.$scope.'.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
 		$limitstart 			= $mainframe->getUserStateFromRequest($option.'.'.$scope.'.limitstart', 'limitstart', 0, 'int');
  		$limitstart 			= ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
  		$this->setState('limit', $limit);
  		$this->setState('limitstart', $limitstart);
    	
    	$obj->filter_search			= $mainframe->getUserStateFromRequest($option.'.'.$scope.'.filter_search', 'filter_search', '', 'string');
    	$obj->filter_order			= $mainframe->getUserStateFromRequest($option.'.'.$scope.'.filter_order', 'filter_order', 'a.ordering', 'cmd');
    	$obj->filter_order_Dir		= $mainframe->getUserStateFromRequest($option.'.'.$scope.'.filter_order_Dir', 'filter_order_Dir', 'asc', 'string');
    	return $obj;
    }
    /**
     * Retrieves a JPagination object
     * @return object
     */
    function getPagination()
    {
    	$this->_db->setQuery("SELECT FOUND_ROWS()");
    	jimport('joomla.html.pagination');
    	$this->_pagination = new JPagination($this->_db->loadResult(), $this->getState('limitstart'), $this->getState('limit'));
    
    	return $this->_pagination;
    }
}