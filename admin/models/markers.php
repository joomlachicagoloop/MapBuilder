<?php
/**
 * Google Maps Markers Model
 * 
 * @package    Google Maps
 * @subpackage Component
 * @license    GNU/GPL
 */
 
// CHECK TO ENSURE THIS FILE IS INCLUDED IN JOOMLA!
defined('_JEXEC') or die();
 
jimport( 'joomla.application.component.modeladmin' );
 
class MapsModelMarkers extends JModelAdmin
{
    /**
     * Markers data array
     *
     * @var mixed This may be an object or array depending on context.
     */
    var $_data			= null;
    /**
     * Markers count of record retrieved
     *
     * @var integer
     */
     var $_total		= null;
    /**
     * Markers Pagination object
     *
     * @var object
     */
     var $_pagination	= null;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   11.1
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->populateState();
	}
	
    /**
     * Retrieves the Markers data
     *
     * @return object A stdClass object containing the data for a single record.
     *
     * @since 1.0
     */
    public function getData()
    {
		$id 	= $this->_getCid();
		$row 	= $this->getTable();

		$row->load($id);
		$this->_data = $row;

        return $this->_data;
    }
    
	/**
	 * Method for getting the form from the model.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since   1.0
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
		$query 	= $db->getQuery(true);
		$row 	= $this->getTable();
		$id 	= $this->_getCid();
		
		$query->select("m.*, map.*, map.`attribs` AS `maps`");
		$query->from("`{$row->getTableName()}` m");
		$query->join("left", "`#__maps` map USING(`maps_id`)");
		$query->where("`marker_id` = {$id}");
		
		$db->setQuery($query);
		$this->_data = $db->loadAssoc();
		$ini = new JRegistry();
		$ini->loadString($this->_data['attribs']);
		$this->_data['params'] = $ini->toArray();
		$data['params'] = $ini->toArray();

		return $this->_data;
	}
	
    /**
     * Retrieves the Maps data for drop down list
     *
     * @return array Array of objects containing the data from the database.
     *
     * @since 0.5
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
     * Method to retrieve the item data list
     *
     * @return	array	Array of objects containing the data from the database.
     *
     * @since	1.0
     */
    public function getList()
    {
    	$mainframe	= JFactory::getApplication();
    	$option		= JRequest::getCmd('option', 'com_maps');
    	$scope		= $this->getName();
    	$row		= $this->getTable();
    	$filter		= array();
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
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * @return  void
	 *
	 * @note    Calling getState in this method will result in recursion.
	 * @since   1.0
	 */
	 protected function populateState()
	 {
	 	$app	= JFactory::getApplication();
	 	$option	= $app->input->get('option', 'com_subtext', 'CMD');
	 	$scope	= $this->getName();

  		$this->setState('limit', $app->getUserStateFromRequest($option.'.'.$scope.'.limit', 'limit', $app->getCfg('list_limit'), 'int'));
  		$this->setState('limitstart', $app->getUserStateFromRequest($option.'.'.$scope.'.limitstart', 'limitstart', 0, 'int'));
  		$this->setState('filter_search', $app->getUserStateFromRequest($option.'.'.$scope.'.filter_search', 'filter_search', '', 'string'));
  		$this->setState('filter_map', $app->getUserStateFromRequest($option.'.'.$scope.'.filter_map', 'filter_map', '-1', 'int'));
  		$this->setState('filter_order', $app->getUserStateFromRequest($option.'.'.$scope.'.filter_order', 'filter_order', 'ordering', 'cmd'));
  		$this->setState('filter_order_Dir', $app->getUserStateFromRequest($option.'.'.$scope.'.filter_order_Dir', 'filter_order_Dir', 'asc', 'string'));
	 }
	    
    /**
     * Method to retrieve a JPagination object
     *
     * @return	object	a JPagination object
     *
     * @since	1.0
     */
    public function getPagination()
    {
    	$this->_db->setQuery("SELECT FOUND_ROWS()");
    	$this->_total = $this->_db->loadResult();
    	jimport('joomla.html.pagination');
    	$this->_pagination = new JPagination($this->_total, $this->getState('limitstart'), $this->getState('limit'));
    
    	return $this->_pagination;
    }
    
	/**
	 * A utility method for retrieving an item Id
	 *
	 * @return	int	the primary key
	 *
	 * @since	1.0
	 */
	protected function _getCid()
	{
		$row = $this->getTable();
		return JFactory::getApplication()->input->get($row->getKeyName(), 0, 'int');
	}
}