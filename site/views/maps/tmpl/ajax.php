<?php
	// NO DIRECT ACCESS
	defined( '_JEXEC' ) or die( 'Restricted access' );
	$mainframe =& JFactory::getApplication();
	header("Content-type: text/xml");
	echo "<root>".trim($this->markers)."</root>";
	$mainframe->close();