<?php
/**
 * Google Maps Markers Controller
 *
 * @package		Google Maps
 * @subpackage	Components
 * @license		GNU/GPL
 */

// NO DIRECT ACCESS
defined( '_JEXEC' ) or die( 'Restricted access' );

// PRIVILEGE CHECK
if(!JFactory::getUser()->authorise('core.manage', 'com_slideshow')){
	return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
}

// REQUIRE THE BASE CONTROLLER
jimport('joomla.application.component.controllerform');

class MapsControllerMarkers extends JControllerForm
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
 		$this->view_item = 'markers';
 		$this->view_list = 'markers';
		parent::__construct();
		// REGISTER EXTRA TASKS
		$this->registerTask('orderup', 'reorder');
		$this->registerTask('orderdown', 'reorder');
		$this->registerTask('unpublish', 'publish');
	}
	/**
	 * Method for processing selection filter data
	 *
	 * @access	public
	 */
	function filter(){
		if(!JSession::checkToken('method')){
			die("SECURITY BREACH");
		}
		$model = $this->getModel('Markers');
		//$model->getState();
		$this->setRedirect("index.php?option=com_maps&controller=markers&view=markers");
	}
	/**
	 * Method to delete a marker entity
	 *
	 * @access	public
	 */
	function delete(){
		// CHECK FOR FORGERIES
		if(!JRequest::checkToken('method')){
			die("SECURITY BREACH");
		}
		// CHECK FOR USER AUTHORIZATION
		$app 	= JFactory::getApplication();
		$user	= JFactory::getUser();
		if(!$user->authorise('core.delete', 'com_maps')){
			$this->setRedirect("index.php?option=com_maps&view=markers", JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'), 'error');
			return false;
		}
		$cids = JRequest::getVar('cid', 0, 'request', 'array');
		$model = $this->getModel('Markers');
		$table = $model->getTable();
		foreach($cids as $cid){
			$table->load($cid);
			$table->delete();
		}
		$this->setRedirect("index.php?option=com_maps&controller=markers&view=markers&layout=list", JText::_('COM_MAPS_MSG_SUCCESS_DELETE_MARKER'));
	}
	/**
	 * Method to toggle the publish state of a marker entity
	 * 
	 * @return  void
	 */
	 function publish()
	 {
		// CHECK FOR REQUEST FORGERIES
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// GET ITEMS TO PUBLISH FROM THE REQUEST.
		$input = JFactory::getApplication()->input;
		$cid = $input->get('cid', array(), '', 'array');
		$data = array('publish' => 1, 'unpublish' => 0, 'archive' => 2, 'trash' => -2, 'report' => -3);
		$task = $this->getTask();
		$value = JArrayHelper::getValue($data, $task, 0, 'int');

		if (empty($cid))
		{
			JError::raiseWarning(500, JText::_('COM_MAPS_NO_ITEM_SELECTED'));
		}
		else
		{
			// GET THE MODEL.
			$model = $this->getModel();

			// MAKE SURE THE ITEM IDS ARE INTEGERS
			JArrayHelper::toInteger($cid);

			// PUBLISH THE ITEMS.
			if (!$model->publish($cid, $value))
			{
				JError::raiseWarning(500, $model->getError());
			}
			else
			{
				if ($value == 1)
				{
					$ntext = 'COM_MAPS_N_ITEMS_PUBLISHED';
				}
				elseif ($value == 0)
				{
					$ntext = 'COM_MAPS_N_ITEMS_UNPUBLISHED';
				}
				elseif ($value == 2)
				{
					$ntext = 'COM_MAPS_N_ITEMS_ARCHIVED';
				}
				else
				{
					$ntext = 'COM_MAPS_N_ITEMS_TRASHED';
				}
				$this->setMessage(JText::plural($ntext, count($cid)));
			}
		}
		$extension = $input->get('extension', '', 'cmd');
		$extensionURL = ($extension) ? '&extension=' . $extension : '';
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $extensionURL, false));
	 }
	/**
	 * Changes the order of one or more records.
	 *
	 * @return  boolean  True on success
	 */
	public function reorder()
	{
		// CHECK FOR REQUEST FORGERIES.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// INITIALIZE VARIABLES.
		$ids = JFactory::getApplication()->input->post->get('cid', null,'array');
		$inc = ($this->getTask() == 'orderup') ? -1 : +1;

		$model = $this->getModel();
		$return = $model->reorder($ids, $inc);
		if ($return === false)
		{
			// REORDER FAILED.
			$message = JText::sprintf('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError());
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false), $message, 'error');
			return false;
		}
		else
		{
			// REORDER SUCCEEDED.
			$message = JText::_('JLIB_APPLICATION_SUCCESS_ITEM_REORDERED');
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false), $message);
			return true;
		}
	}
	/**
	 * Method to save the submitted ordering values for records.
	 *
	 * @return  boolean  True on success
	 */
	public function saveorder()
	{
		// CHECK FOR REQUEST FORGERIES.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// GET THE INPUT
		$input = JFactory::getApplication()->input;
		$pks = $input->post->get('cid', null, 'array');
		$order = $input->post->get('order', null, 'array');

		// SANITIZE THE INPUT
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		// GET THE MODEL
		$model = $this->getModel();

		// SAVE THE ORDERING
		$return = $model->saveorder($pks, $order);

		if ($return === false)
		{
			// REORDER FAILED
			$message = JText::sprintf('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError());
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false), $message, 'error');
			return false;
		}
		else
		{
			// REORDER SUCCEEDED.
			$this->setMessage(JText::_('JLIB_APPLICATION_SUCCESS_ORDERING_SAVED'));
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
			return true;
		}
	}
	/**
	 * Check in of one or more records.
	 *
	 * @return  boolean  True on success
	 */
	public function checkin()
	{
		// CHECK FOR REQUEST FORGERIES.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// INITIALIZE VARIABLES.
		$ids = JFactory::getApplication()->input->post->get('cid', null, 'array');

		$model = $this->getModel();
		$return = $model->checkin($ids);
		if ($return === false)
		{
			// CHECKIN FAILED.
			$message = JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError());
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false), $message, 'error');
			return false;
		}
		else
		{
			// CHECKIN SUCCEEDED.
			$message = JText::plural('COM_SLIDESHOW_N_ITEMS_CHECKED_IN', count($ids));
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false), $message);
			return true;
		}
	}
}
