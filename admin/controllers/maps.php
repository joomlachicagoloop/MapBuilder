<?php
/**
 * Google Maps Map Controller
 *
 * @package		Subtext
 * @subpackage	Components
 * @license		GNU/GPL
 */

// NO DIRECT ACCESS
defined( '_JEXEC' ) or die( 'Restricted access' );

// PRIVILEGE CHECK
if(!JFactory::getUser()->authorise('core.manage', 'com_maps')){
	return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
}

// IMPORT CONTROLLER LIBRARY
jimport('joomla.application.component.controllerform');

class MapsControllerMaps extends JControllerForm
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	public function __construct($config = array())
	{
		$this->view_item = 'maps';
		$this->view_list = 'maps';
		parent::__construct();
		// REGISTER CUSTOM TASKS
		$this->registerTask('orderup', 'reorder');
		$this->registerTask('orderdown', 'reorder');
		$this->registerTask('unpublish', 'publish');
	}
	/**
	 * A convenience method for filtering lists.
	 *
	 * @return  void
	 */
	public function filter()
	{
		$model = $this->getModel();
		$model->getState();
		$this->setRedirect(JRoute::_("index.php?option=com_maps&view=".$this->view_list, false));
		return true;
	}
	/**
	 * Removes an item.
	 *
	 * @return  void
	 */
	public function delete()
	{
		// CHECK FOR REQUEST FORGERIES
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// GET ITEMS TO REMOVE FROM THE REQUEST.
		$input = JFactory::getApplication()->input;
		$cid = $input->get('cid', array(), 'array');

		if (!is_array($cid) || count($cid) < 1)
		{
			JError::raiseWarning(500, JText::_('COM_MAPS_NO_ITEM_SELECTED'));
		}
		else
		{
			// GET THE MODEL.
			$model = $this->getModel();

			// MAKE SURE THE ITEM IDS ARE INTEGERS
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);

			// REMOVE THE ITEMS.
			if ($model->delete($cid))
			{
				$this->setMessage(JText::plural('COM_MAPS_N_ITEMS_DELETED', count($cid)));
			}
			else
			{
				$this->setMessage($model->getError());
			}
		}

		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
	}
	/**
	 * Method to publish a list of items
	 *
	 * @return  void
	 */
	public function publish()
	{
		// CHECK FOR REQUEST FORGERIES
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// GET ITEMS TO PUBLISH FROM THE REQUEST.
		$input = JFactory::getApplication()->input;
		$cid = $input->get('cid', array(), 'array');
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
		$extension = $input->get('extension', null, 'cmd');
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
			$message = JText::plural('COM_MAPS_N_ITEMS_CHECKED_IN', count($ids));
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false), $message);
			return true;
		}
	}
	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function saveOrderAjax()
	{
		// Get the input
		$pks = $this->input->post->get('cid', array(), 'array');
		$order = $this->input->post->get('order', array(), 'array');

		// Sanitize the input
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}
}
