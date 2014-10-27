<?php
/**
 * MapBuilder Main
 * 
 * @package		MapBuilder
 * @subpackage	Components
 * @license		GNU/GPL
*/
 
// NO DIRECT ACCESS
defined( '_JEXEC' ) or die( 'Restricted access' );

// DEFINE DS CONSTANT
if(!defined('DS')) define( 'DS', DIRECTORY_SEPARATOR );

// IMPORT CONTROLLER LIBRARY
jimport('joomla.application.component.controller');

// GET CONTROLLER INSTANCE
$controller = JControllerLegacy::getInstance('MapBuilder');
 
// PERFORM THE REQUEST TASK
$controller->execute(JRequest::getVar('task'));

// REDIRECT IF SET BY THE CONTROLLER
$controller->redirect();
