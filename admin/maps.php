<?php
/**
 * @package		Google Maps
 * @subpackage	Components
 * @license		GNU/GPL
 */

// NO DIRECT ACCESS
defined( '_JEXEC' ) or die( 'Restricted access' );

// REQUIRE THE BASE CONTROLLER

require_once( JPATH_COMPONENT.DS.'controller.php' );

// REQUIRE SPECIFIC CONTROLLER IF REQUESTED
if($controller = JRequest::getVar('controller')) {
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}

// REQUIRE HELPER FILE
JLoader::register('MapsHelper', dirname(__FILE__).DS.'helpers'.DS.'maps.php');

// CREATE THE CONTROLLER
$classname	= 'MapsController'.$controller;
$controller	= new $classname( );

// PERFORM THE REQUEST TASK
if($controller->execute(JRequest::getVar( 'task' )) === false){
	$controller->setRedirect("index.php?option=com_maps", $controller->getError(), "error");
}

// REDIRECT IF SET BY THE CONTROLLER
$controller->redirect();