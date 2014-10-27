<?php
/**
 * MapBuilder Markers Controller
 *
 * @package		MapBuilder
 * @subpackage	Components
 * @license		GNU/GPL
 */

// NO DIRECT ACCESS
defined( '_JEXEC' ) or die( 'Restricted access' );

// REQUIRE THE BASE CONTROLLER
jimport('joomla.application.component.controllerform');

class MapBuilderControllerMarkers extends JControllerForm
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JControllerLegacy
	 * @since   1.1.0
	 * @throws  Exception
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
 		$this->view_item = 'markers';
 		$this->view_list = 'markers';
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param   integer  $recordId  The primary key id for the item.
	 * @param   string   $urlVar    The name of the URL variable for the id.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   1.1.0
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$id = JFactory::getApplication()->input->get('map_id', null);
		return parent::getRedirectToItemAppend($id);
	}

	/**
	 * Gets the URL arguments to append to a list redirect.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   1.1.0
	 */
	protected function getRedirectToListAppend()
	{
		$id = JFactory::getApplication()->input->get('map_id', null);
		return parent::getRedirectToItemAppend($id);
	}
}
