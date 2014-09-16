<?php
/**
 * MapBuilder Main Controller
 *
 * @package		MapBuilder
 * @subpackage	Components
 * @license		GNU/GPL
 */

// NO DIRECT ACCESS
defined( '_JEXEC' ) or die( 'Restricted access' );

// DEFINE DS CONSTANT
if(!defined('DS')) define( 'DS', DIRECTORY_SEPARATOR );

// PRIVILEGE CHECK
if(!JFactory::getUser()->authorise('core.manage', 'com_mapbuilder')){
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// REQUIRE HELPER FILE
JLoader::register('MapBuilderHelper', dirname(__FILE__).DS.'helpers'.DS.'mapbuilder.php');

// IMPORT CONTROLLER LIBRARY
jimport('joomla.application.component.controller');

// GET CONTROLLER INSTANCE
$controller = JControllerLegacy::getInstance('MapBuilder');

// PERFORM THE REQUESTED TASK
$controller->execute(JFactory::getApplication()->input->get('task'));

// REDIRECT IF NECESSARY
$controller->redirect();
