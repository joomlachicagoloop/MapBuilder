<?php
	// NO DIRECT ACCESS
	defined( '_JEXEC' ) or die( 'Restricted access' );
	$mainframe = JFactory::getApplication();
	echo json_encode($this->markers);
	$mainframe->close();
