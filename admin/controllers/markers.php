<?php
/**
 * Google Maps Markers Controller
 *
 * @package    Google Maps
 * @subpackage Components
 */

// NO DIRECT ACCESS
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class MapsControllerMarkers extends JController
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
		$layout = JRequest::getVar('layout', 'list', 'get', STRING);
		JRequest::setVar('layout', $layout);
		parent::__construct();
		// REGISTER EXTRA TASKS
		$this->registerTask('apply', 'save');
		$this->registerTask('add', 'edit' );
		$this->registerTask('orderup', 'moveorder');
		$this->registerTask('orderdown', 'moveorder');
		$this->registerTask('unpublish', 'publish');
		$this->registerTask('accesspublic', 'access');
		$this->registerTask('accessregistered', 'access');
		$this->registerTask('accessspecial', 'access');
		// If A Pagination Request Is Set, Redirect
		if($_POST['task'] == "" && ($_POST['limit'] != "" || $_POST['limitstart'] != "")){
			$this->filter();
		}
	}
	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	function display()
	{
		parent::display();
	}
	/**
	 * Method for processing selection filter data
	 *
	 * @access	public
	 */
	function filter(){
		if(!JRequest::checkToken('method')){
			die("SECURITY BREACH");
		}
		$model =& $this->getModel('Markers');
		$model->getFilter();
		$this->setRedirect("index.php?option=com_maps&controller=markers&view=markers&layout=list");
	}
	/**
	 * Method to edit data for an retail item
	 *
	 * @access	public
	 */
	 
	function edit(){
		if(!JRequest::checkToken('method')){
			die("SECURITY BREACH");
		}
		$user		=& JFactory::getUser();
		$user_id	= $user->get('id');
		$model 		=& $this->getModel('Markers');
		$row		=& $model->getTable();
		$task		= JRequest::getVar('task', '', 'post');
		$cid 		= $this->_getCid();
		
		$row->load($cid);
		/**
		 * If the item is checked out we cannot edit it...
		 * unless it was checked out by the current user.
		 */
		if(JTable::isCheckedOut($user_id, $row->checked_out)){
			$msg = JText::sprintf('DESCBEINGEDITTED', JText::_('The item'), $row->price_name);
			$this->setRedirect("index.php?option=com_maps&controller=markers&view=markers&layout=list", $msg);
		}else{
			$row->checkout($user_id);
			$this->setRedirect("index.php?option=com_maps&controller=markers&view=markers&layout=edit&cid[]={$cid}");
		}
	}
	/**
	 * Method to save data for an retail item
	 *
	 * @access	public
	 */
	function save(){
		if(!JRequest::checkToken('method')){
			die("SECURITY BREACH");
		}
		$user		=& JFactory::getUser();
		$model 		=& $this->getModel('Markers');
		$row 		=& $model->getTable();
		$id			= JRequest::getInt('country_id', 0, 'post');
		$task		= JRequest::getCmd('task', 'save', 'post');
		$options	= JRequest::getVar('options', array(), 'post', 'array');
		$data 		= JRequest::get('post');

		// LOAD DEFAULT DATA FROM THE DATABASE
		$row->load($id);
		// BIND THE FORM FIELDS TO THE MARKERS TABLE
		if (!$row->bind($data)) {
			$this->setError($model->_db->getErrorMsg());
			return false;
		}
		// ASSIGN ORDERING IF NECESSARY
		if(!$row->ordering){
			$row->ordering = $row->getNextOrder("`maps_id` = {$row->maps_id}");
		}
		// MAKE SURE THE COMPONENT RECORD IS VALID
		if (!$row->check()) {
			$this->setError($model->_db->getErrorMsg());
			return false;
		}
		// SAVE THE RECORD TO THE DATABASE
		if(!$row->store()){
			$this->setError($model->_db->getErrorMsg());
			return false;
		}
		// COMPACT THE ORDERING SEQUENCE FOR THE CATEGORY LISTING
		if (!$row->reorder("`maps_id` = {$row->maps_id}")) {
			$this->setError( $row->_db->getErrorMsg() );
			return false;
		}
		switch($task){
		case "apply":
			$this->setRedirect("index.php?option=com_maps&controller=markers&view=markers&layout=edit&cid[]={$row->marker_id}");
			break;
		default:
			$row->checkin();
			$this->setRedirect("index.php?option=com_maps&controller=markers&view=markers&layout=list");
			break;
		}
	}
	/**
	 * Method to delete an retail item from the system
	 *
	 * @access	public
	 */
	function remove(){
		if(!JRequest::checkToken('method')){
			die("SECURITY BREACH");
		}
		$cids = JRequest::getVar('cid', 0, 'request', 'array');
		$model =& $this->getModel('Markers');
		$table =& $model->getTable();
		foreach($cids as $cid){
			$table->load($cid);
			$table->delete();
		}
		$this->setRedirect("index.php?option=com_maps&controller=markers&view=markers&layout=list");
	}
	/**
	 * Method to toggle the publish state of an retail item
	 *
	 * @access	public
	 */
	 function publish(){
		if(!JRequest::checkToken('method')){
			die("SECURITY BREACH");
		}
	 	$id 	= $this->_getCid();
		$model 	=& $this->getModel('Markers');
		$row 	=& $model->getTable();
		$row->load($id);
		if($row->published){
			$row->published = 0;
		}else{
			$row->published = 1;
		}
		$row->store();
		$this->setRedirect("index.php?option=com_maps&controller=markers&view=markers&layout=list");
	 }
	/**
	 * Method to toggle the front end access of an retail item
	 *
	 * @access	public
	 */
	function access(){
		if(!JRequest::checkToken('method')){
			die("SECURITY BREACH");
		}
		$model	=& $this->getModel('Markers');
		$row	=& $model->getTable();
		$task	= JRequest::getCmd('task', '', 'post');
		$id		= $this->_getCid();
		
		$row->load($id);
		switch($task){
		case "accesspublic":
			$access = 0;
			break;
		case "accessregistered":
			$access = 1;
			break;
		case "accessspecial":
			$access = 2;
			break;
		}
		$row->access = $access;
		$row->store();
		$this->setRedirect("index.php?option=com_maps&controller=markers&view=markers&layout=list");
	}
	/**
	 * Method to reorder an retail item
	 *
	 * @access	public
	 */
	function moveorder(){
		if(!JRequest::checkToken('method')){
			die("SECURITY BREACH");
		}
		$task = JRequest::getWord('task', '', 'post');
		switch($task){
		case "orderup":
			$direction = -1;
			break;
		case "orderdown":
			$direction = 1;
			break;
		default:
			$direction = 0;
			break;
		}
		$model =& $this->getModel('Markers');
		$row =& $model->getTable();
		$row->load($this->_getCid());
		$row->move($direction, "`marker_parent` = {$row->marker_parent}");

		$this->setRedirect("index.php?option=com_maps&controller=markers&view=markers&layout=list");
	}
	/**
	 * Method to reorder several retail countries at one time
	 *
	 * @access	public
	 */
	function saveorder(){
		if(!JRequest::checkToken('method')){
			die("SECURITY BREACH");
		}
		$cid = JRequest::getVar('cid',  0, '', 'array');
		$ordering = JRequest::getVar('order', 0, '', 'array');
		$catid = array();
		$model =& $this->getModel('Markers');
		$row =& $model->getTable();
		for($i=0; $i < count($cid); $i++){
			if(!$row->load($cid[$i])){
				$this->setError($row->_db->getErrorMsg());
				return false;
			}
			$row->ordering = $ordering[$i];
			// Save the record to the database
			if(!$row->store()){
				$this->setError($row->_db->getErrorMsg());
				return false;
			}
			if(!in_array($row->marker_parent, $catid)){
				$catid[] = $row->marker_parent;
			}
		}
		foreach($catid as $subcatid){
			// COMPACT THE ORDERING SEQUENCE FOR THE CATEGORY LISTING
			if (!$row->reorder("`marker_parent` = $subcatid")) {
				$this->setError( $row->_db->getErrorMsg() );
				return false;
			}
		}
		
		$this->setRedirect("index.php?option=com_maps&controller=markers&view=markers&layout=list");
	}
	
	function cancel(){
		if(!JRequest::checkToken('method')){
			die("SECURITY BREACH");
		}
		$model	=& $this->getModel('Markers');
		$row	=& $model->getTable();
		$id		= JRequest::getInt('country_id', 0, 'post');
		
		$row->load($id);
		$row->checkin();
		$this->setRedirect("index.php?option=com_maps&controller=markers&view=markers&layout=list");
	}
	
	function _getCid(){
		$cid = JRequest::getVar('cid',  0, '', 'array');
		return $cid[0];
	}
}