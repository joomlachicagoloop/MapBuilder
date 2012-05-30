<?php
/**
 * Maps Controller
 *
 * @package		Google Maps
 * @subpackage	Components
 */

// NO DIRECT ACCESS
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class MapsController extends JController
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
 		jimport('joomla.filesystem.file');
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
		// IF A PAGINATION REQUEST IS SET, CALL FILTER
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
		$model =& $this->getModel();
		$model->getFilter();
		$this->setRedirect("index.php?option=com_maps");
	}
	/**
	 * Method to edit data for an entity
	 *
	 * @access	public
	 */
	function edit(){
		if(!JRequest::checkToken('method')){
			die("SECURITY BREACH");
		}
		$user		=& JFactory::getUser();
		$user_id	= $user->get('id');
		$model		=& $this->getModel();
		$row		=& $model->getTable();
		$task		= JRequest::getVar('task', '', 'post');
		$cid		= $this->_getCid();
		// LOAD THE CURRENT DATA
		$row->load($cid);
		// IF THE ITEM IS CHECKED OUT WE CANNOT EDIT IT
		// UNLESS IT WAS CHECKED OUT BY THE CURRENT USER
		if(JTable::isCheckedOut($user_id, $row->checked_out)){
			$msg = JText::_('DESCBEINGEDITTED', JText::_('The item'), $row->maps_name);
			$this->setRedirect("index.php?option=com_maps", $msg);
		}else{
			$row->checkout($user_id);
			$this->setRedirect("index.php?option=com_maps&layout=edit&cid[]={$cid}");
		}
	}
	/**
	 * Method to save data for an entity
	 *
	 * @access	public
	 */
	function save(){
		if(!JRequest::checkToken('method')){
			die("SECURITY BREACH");
		}
		$model	=& $this->getModel();
		$row 	=& $model->getTable();
		$data 	= JRequest::get('post');
		$task	= JRequest::getCmd('task', 'save', 'post');
		$id 	= JRequest::getInt('maps_id', 0);
		// LOAD DEFAULT DATA FROM THE DATABASE
		$row->load($id);
		// BIND THE FORM FIELDS TO THE SLIDESHOW TABLE
		if (!$row->bind($data)) {
			$this->setError($row->getDbo()->getErrorMsg());
			return false;
		}
		// ASSIGN ORDERING IF NECESSARY
		if(!$row->ordering){
			$row->ordering = $row->getNextOrder();
		}
		// MAKE SURE THE SLIDESHOW RECORD IS VALID
		if (!$row->check()) {
			$this->setError($row->getDbo()->getErrorMsg());
			return false;
		}
		// SAVE THE RECORD TO THE DATABASE
		if (!$row->store()) {
			$this->setError($model->getDbo()->getErrorMsg());
			return false;
		}
		// COMPACT THE ORDERING SEQUENCE
		if (!$row->reorder()) {
			$this->setError($model->getDbo()->getErrorMsg());
			return false;
		}
		switch($task){
		case "apply":
			$this->setRedirect("index.php?option=com_maps&layout=edit&cid[]={$row->maps_id}");
			break;
		default:
			$row->checkin();
			$this->setRedirect("index.php?option=com_maps");
			break;
		}
	}
	/**
	 * Method to delete an entity
	 *
	 * @access	public
	 */
	function remove(){
		if(!JRequest::checkToken('method')){
			die("SECURITY BREACH");
		}
		$cids 	= JRequest::getVar('cid', 0, 'post', 'array');
		$model 	=& $this->getModel();
		$row 	=& $model->getTable();
		$msg 	= "The selected items were successfully deleted.";
		foreach($cids as $cid){
			$row->load($cid);
			$row->delete();
			$row->reorder();
		}
		$this->setRedirect("index.php?option=com_maps", $msg);
	}
	/**
	 * Method to toggle the publish state of an entity
	 *
	 * @access	public
	 */
	 function publish(){
		if(!JRequest::checkToken('method')){
			die("SECURITY BREACH");
		}
	 	$id 	= $this->_getCid();
		$model 	=& $this->getModel();
		$row 	=& $model->getTable();
		$row->load($id);
		if($row->published){
			$row->published = 0;
		}else{
			$row->published = 1;
		}
		$row->store();
		$this->setRedirect("index.php?option=com_maps");
	 }
	/**
	 * Method to toggle the front end access of an entity
	 *
	 * @access	public
	 */
	function access(){
		if(!JRequest::checkToken('method')){
			die("SECURITY BREACH");
		}
		$model	=& $this->getModel();
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
		$this->setRedirect("index.php?option=com_maps");
	}
	/**
	 * Method to reorder an entity position
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
		$model =& $this->getModel();
		$row =& $model->getTable();
		$row->load($this->_getCid());
		$row->move($direction);
		
		$this->setRedirect("index.php?option=com_maps");
	}
	/**
	 * Method to reorder several entities at one time
	 *
	 * @access	public
	 */
	function saveorder(){
		if(!JRequest::checkToken('method')){
			die("SECURITY BREACH");
		}
		$cid		= JRequest::getVar('cid',  0, '', 'array');
		$ordering	= JRequest::getVar('order', 0, '', 'array');
		$model 		=& $this->getModel();
		$row		=& $model->getTable();
		for($i=0; $i < count($cid); $i++){
			if(!$row->load($cid[$i])){
				$this->setError($model->getDbo()->getErrorMsg());
				return false;
			}
			$row->ordering = $ordering[$i];
			// SAVE THE RECORD TO THE DATABASE
			if(!$row->store()){
				$this->setError($model->getDbo()->getErrorMsg());
				return false;
			}
		}
		// COMPACT THE ORDERING SEQUENCE
		if (!$row->reorder()) {
			$this->setError($model->getDbo()->getErrorMsg());
			return false;
		}
		$this->setRedirect("index.php?option=com_maps");
	}
	/**
	 * Method for cancelling an operation, checkin the resource if necessary
	 *
	 * @access	public
	 */
	function cancel(){
		if(!JRequest::checkToken('method')){
			die("SECURITY BREACH");
		}
		$model	=& $this->getModel();
		$row	=& $model->getTable();
		$id		= JRequest::getInt('maps_id', 0, 'post');
		
		$row->load($id);
		$row->checkin();
		$this->setRedirect("index.php?option=com_maps");
	}
	/**
	 * A utility method for retrieving an item Id
	 *
	 * @access	protected
	 */
	function _getCid(){
		$cid = JRequest::getVar('cid',  0, '', 'array');
		return $cid[0];
	}
}